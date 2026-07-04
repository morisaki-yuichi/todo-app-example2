# 開発トレースガイド(dev-walkthrough.md)

このガイドは、TODOアプリの開発過程を**コミット単位で追体験する**ための教材です。
各ステップは「1コミット=1つの意味のある変更」に対応しています。
上から順に、実際に手を動かしながら進めてください。

- 前提知識の補足は [概念解説集](laravel-concepts.md) にあります。
  本文中の【概念】リンクは、初めて出てきたタイミングで読んでください
- 仕様がなぜこうなっているかは [Q&A記録](qa-log.md) と [ロードマップ](roadmap.md) を参照

## 差分の見方 3通り

各ステップの「何をどう変えたか」は、次の3つの方法で確認できます。
慣れないうちは①、手元にクローンしたら②③がおすすめです。

| 方法 | やり方 | 向いている場面 |
|---|---|---|
| ① GitHubで見る | 各ステップに貼ってあるコミット/PRリンクを開く | ブラウザだけで読みたいとき。ファイル横断の差分が色つきで見やすい |
| ② git show | `git show <コミットID>` | 手元で1コミット分の差分全体を見る |
| ③ ファイル単位の履歴 | `git log -p -- <ファイルパス>` | 「このファイルはどう育ってきたか」を時系列で追う |

> **写経時の注意(重要)**: コミットID(`5621cfd` など)は**このリポジトリの履歴の値**です。
> あなたが自分で写経して作るリポジトリでは必ず別の値になります。
> 「差分を見る」ときはこのリポジトリのIDを、「自分の進み具合を確認する」ときは
> 自分のリポジトリの `git log` を使ってください。

## ユーザーストーリー × 実装コミット × PR 対応マップ

進行に合わせて更新します。US本文は [user-stories.md](user-stories.md)。

| ユーザーストーリー | ステップ(コミット) | PR |
|---|---|---|
| US-0 開発環境の再現 | 1-1([5621cfd](https://github.com/morisaki-yuichi/todo-app-example2/commit/5621cfd))、1-2([1c97590](https://github.com/morisaki-yuichi/todo-app-example2/commit/1c97590))、1-3([eb93e7c](https://github.com/morisaki-yuichi/todo-app-example2/commit/eb93e7c)) | [#1](https://github.com/morisaki-yuichi/todo-app-example2/pull/1) |
| US-1〜US-6 | スプリント2以降 | - |

## コミットに残っていない出来事

コードの差分だけでは分からない「開発中に実際に起きたこと」の一覧です。
**トラブルの調査過程こそ教材**なので、必ずリンク先も読んでください。

| 出来事 | 記録先 |
|---|---|
| ポート事前調査で `VITE_PORT=5174` が別プロジェクトと衝突 → 5180に変更 | [qa-log.md](qa-log.md) |
| `sail:install` が「no commands defined in the "sail" namespace」で失敗(インストーラがsailを同梱しなくなっていた) | [スプリント1レビュー記録](../01_sprint1/sprint-review.md) |
| わざと失敗実験: APP_KEYを空にして500を観察 | このガイドの [実験1-A](#実験1-a-わざと失敗app_keyを空にして500を観察する) |
| `.env` 変更直後にcurlが `000`(接続リセット)になった | [スプリント1レビュー記録](../01_sprint1/sprint-review.md) |

---

# スプリント1: 環境構築と土台

**ゴール**: クローンした人がREADMEの手順だけで `http://localhost:8080` にLaravelの画面を出せる状態にする
(計画: [スプリント1バックログ](../01_sprint1/sprint-backlog.md))

**開始前の準備**:
- Docker と Git が入っていること(`docker --version` / `git --version`)
- GitHubアカウントがあること
- 空のリポジトリを作り、クローンしておくこと(このリポジトリの履歴では、
  先に教材用ドキュメント `docs/` をコミットしていますが、写経では空スタートで構いません)
- 作業ブランチを切ること: `git switch -c feature/sprint1-environment`
  - **なぜブランチを切るのか**: mainを「常に動く状態」に保つため。
    作業途中の壊れた状態はブランチに隔離し、完成したらPull Request(PR)でmainに取り込む

---

## ステップ1-1: Laravel本体をSail構成で導入する

- 差分: [GitHubで見る](https://github.com/morisaki-yuichi/todo-app-example2/commit/5621cfd) / ローカル: `git show 5621cfd --stat`(ファイル数が多いので、まず `--stat` で一覧を見るのがコツ)
- 【概念】[コンテナとイメージ](laravel-concepts.md#1-コンテナイメージ) / [Composerと鶏と卵問題](laravel-concepts.md#4-composerと鶏と卵問題) / [Laravel Sail](laravel-concepts.md#5-laravel-sail)

### これから何を・なぜやるか

Laravel本体一式(約60ファイル+`vendor/`)を導入します。PHPもComposerもホストには
入れない方針なので、**Composer入りの使い捨てコンテナ**の中でインストーラを動かします。
「開発環境を汚さない・誰の環境でも同じ結果になる」というDockerの利点そのものです。

### 足場の作り方(このステップのファイルはどこから来るか)

| ファイル | 作り方 |
|---|---|
| `composer.json` `artisan` `app/` `routes/` など Laravel一式 | **ジェネレータ**(下記コマンド1) |
| `composer.json` の `require-dev` への `laravel/sail` 追加 | **ジェネレータ**(下記コマンド2) |
| `compose.yaml` | **ジェネレータ**(下記コマンド3) |
| `.gitignore` | ジェネレータ生成後、**手で編集**(自分の環境用の除外を追記してよい) |

コマンド(コピペ可。作業ディレクトリの**1つ上**で実行し、`todo-app` は好きな名前に):

```bash
# 1. Laravel本体の生成(コンテナ内で laravel new を実行)
docker run --rm -u "$(id -u):$(id -g)" -e HOME=/tmp \
    -v "$(pwd):/opt" -w /opt \
    laravelsail/php84-composer:latest \
    laravel new todo-app --no-interaction

# 2と3. Sailパッケージの追加と、compose.yaml の生成(MySQL構成)
cd todo-app
docker run --rm -u "$(id -u):$(id -g)" -e HOME=/tmp \
    -v "$(pwd):/opt" -w /opt \
    laravelsail/php84-composer:latest \
    bash -c "composer require laravel/sail --dev --no-interaction && php artisan sail:install --with=mysql"
```

> **注意(実録トラブル)**: 以前は `laravel new` だけでsailが同梱されていましたが、
> **現行のインストーラはsailを同梱しません**。コマンド2を飛ばすと
> `There are no commands defined in the "sail" namespace.` というエラーになります。
> 実際に開発中に踏んだトラブルで、調査過程は
> [スプリント1レビュー記録](../01_sprint1/sprint-review.md)に記録しています。

> **写経時の差異**: Laravel・Sailのバージョンが上がっていると、生成されるファイルの
> 内容が本リポジトリと多少異なることがあります。差分が完全一致しなくても、
> 動作確認が通れば先へ進んで構いません。

### 編集の順序とその理由

このステップは「生成 → 不要物の削除」だけです。

1. コマンド1〜3で生成(上記)
2. `database/database.sqlite` を削除(`rm database/database.sqlite`)
   — インストーラはデフォルトでSQLite用のファイルを作るが、本プロジェクトはMySQLを使うため
3. `.gitignore` の末尾に、自分のエディタ・AIツール等のローカル設定の除外を追記(必要な人のみ)

### 動作確認(CLI)

まだコンテナを起動していないので、「ファイルが正しく生成されたか」だけ確認します。

```bash
ls vendor/bin/sail        # => vendor/bin/sail が表示される(sailコマンド本体)
grep -A3 services compose.yaml | head -5   # => laravel.test: と mysql: がある
```

**なぜこの確認方法か**: この段階の成果物は「ファイル群」なので、確認手段も
ファイルの存在チェックが最小で確実。ブラウザ確認は起動後(ステップ1-2)に行います。

### ここでコミット

```bash
git add -A
git commit -m "chore: Laravel 13をLaravel Sail(MySQL)構成で導入"
```

- **粒度の理由**: 生成された約60ファイルは「Laravelの雛形導入」という**1つの意味**の
  まとまり。ここで区切ると、以降の自分の変更が「雛形との差分」として綺麗に見える
- **メッセージの理由**: 種別は依存導入なので `chore:`(機能追加=featではない)。
  本文には実行した生成コマンドを書いておくと、後から「どうやって作ったか」を再現できる
  (実物のコミット本文を `git show 5621cfd -s` で見てみてください)

---

## ステップ1-2: .env.example をプロジェクト用に整備する

- 差分: [GitHubで見る](https://github.com/morisaki-yuichi/todo-app-example2/commit/1c97590) / ローカル: `git show 1c97590`
- 【概念】[.envと.env.example](laravel-concepts.md#6-envとenvexample) / [ポートマッピング](laravel-concepts.md#3-ポートマッピング) / [Docker Compose](laravel-concepts.md#2-docker-composeとcomposeyaml)

### これから何を・なぜやるか

Sailは `.env` の値で「どのポートで公開するか」「コンテナ名を何にするか」を決めます(.env駆動)。
デフォルトのままだと **80番ポート・ディレクトリ名のコンテナ名**になり、他プロジェクトと
衝突しやすいので、`.env.example`(=設定の見本ファイル)に明示します。

実際、このプロジェクトの開発中も**予定していた5174番が別プロジェクトに使われており**、
5180に変更しました(経緯: [qa-log.md](qa-log.md))。ポートは「あなたの環境の空き」に
合わせて決めるものです: `ss -tlnp | grep 8080` で確認できます。

### 足場の作り方

| ファイル | 作り方 |
|---|---|
| `.env.example` | **既存ファイルを手で編集** |
| `.env` | `.env.example` から**コピーして作り直す**(下記) |

### 編集の順序とその理由

1. **`.env.example` を先に編集**(APP_NAME、APP_URL、APP_PORT/VITE_PORT/COMPOSE_PROJECT_NAME、DB設定をMySQL用に)
2. `.env` を `.env.example` から作り直す:
   ```bash
   cp .env.example .env
   ./vendor/bin/sail artisan key:generate   # APP_KEYを発行(コピーで空になるため)
   ```
   ※ sailコマンドがまだ使えない場合(コンテナ未起動でもartisanはコンテナ内実行のため
   起動が必要)は、先に `./vendor/bin/sail up -d` を実行してから

**なぜ .env.example が先か**: コミットされるのは `.env.example` だけ(`.env` は
秘密情報を含みうるので `.gitignore` 済み)。「見本を正として、実物は見本から作る」
順にすると、見本の整備漏れに必ず気づけます。逆順だと「自分の.envでは動くが、
クローンした人は動かない」事故になります。

### 動作確認(CLI→ブラウザ)

```bash
./vendor/bin/sail up -d          # 初回はビルドで数分かかる
./vendor/bin/sail ps             # 2つのコンテナがUp(mysqlは healthy)になるまで待つ
./vendor/bin/sail artisan migrate
curl -s -o /dev/null -w "%{http_code}\n" http://localhost:8080/   # => 200
```

- `sail ps` の `NAME` 列が `todo-app-example2-...` になっていれば、
  `COMPOSE_PROJECT_NAME` が効いている証拠
- `migrate` が成功すれば、**アプリコンテナからMySQLコンテナへ接続できている**証拠
  (DB_HOST=mysql という「サービス名での接続」が機能している)
- **なぜ curl か**: `-w "%{http_code}"` でステータスコードだけを見れば、
  「疎通OK(200)/サーバ内エラー(500)/そもそも繋がらない(000)」を機械的に切り分けられる

**ブラウザ確認**: http://localhost:8080 を開く。Laravelのウェルカム画面が表示され、
**ブラウザのタブに「TODO App」**と出れば合格(APP_NAMEが反映されている)。

**異常系も見る**: `./vendor/bin/sail stop` してから同じURLを開くと「接続できません」に
なること、`sail up -d` で復活することも確認しておくと、「動かない」ときに
コンテナ起動状態をまず疑う癖がつきます。

### よくあるエラーと症状の対応表

| 症状 | 原因 → 対処 |
|---|---|
| `port is already allocated` | ホスト側ポート衝突 → `.env` のAPP_PORT等を空きポートへ変更し `sail up -d` |
| migrate時 `Connection refused` | MySQL初期化がまだ → `sail ps` で healthy を待って再実行 |
| migrate時 `Access denied for user` | MySQLボリュームに**古い初期化データ**が残っている(DB名やパスワードを初回起動後に変えた場合)→ `sail down -v` でボリュームごと作り直す(**データは消える**) |
| 500エラー | APP_KEY未設定の可能性 → 実験1-Aの手順でログを読む |

### 実験1-A: わざと失敗(APP_KEYを空にして500を観察する)

環境が動いたところで、**わざと壊して直す**練習をします。「500エラーのときに
どこを見るか」を安全に体験するのが目的です。

1. `.env` の `APP_KEY=base64:...` の値を消して `APP_KEY=` にする(元の値はメモしておく)
2. 数秒待つ(開発サーバが.envの変更を検知して自動再起動するため。
   直後にアクセスすると接続リセット `000` になることがあります — これも実録です)
3. `curl -s -o /dev/null -w "%{http_code}\n" http://localhost:8080/` → **500**
4. **調査の型**どおり、ログのエラー1行目から読む:
   ```bash
   grep ERROR storage/logs/laravel.log | tail -1
   # => local.ERROR: No application encryption key has been specified.
   ```
   1行目だけで「暗号化キーが指定されていない」と原因がそのまま書いてあります。
   スタックトレース(下に続く大量の行)を読む前に、まず1行目。
5. 復旧: `./vendor/bin/sail artisan key:generate` を実行(またはメモした値を戻す)し、
   数秒待って再度curl → **200**

> この実験はコミットしません(壊した状態を履歴に残さないため)。
> `git status` で差分がないことを確認してから次へ進んでください
> (`.env` はGit管理外なので、正しくやれば何も出ないはずです)。

### ここでコミット(実験の前に済ませてもOK)

```bash
git add .env.example
git commit -m "chore: .env.exampleをプロジェクト用に整備(ポート・MySQL接続)"
```

- **粒度の理由**: 「設定の整備」だけで1コミット。雛形導入(1-1)と混ぜると、
  後から「Laravelのデフォルトからどこを変えたのか」が差分で追えなくなる
- **メッセージの理由**: 本文にポート変更の理由(5174が使用中だった)を残した。
  設定値の変更は「なぜその値か」が失われやすいため、コミット本文が最後の砦

---

## ステップ1-3: READMEをセットアップ手順に書き換える

- 差分: [GitHubで見る](https://github.com/morisaki-yuichi/todo-app-example2/commit/eb93e7c) / ローカル: `git show eb93e7c`

### これから何を・なぜやるか

「クローンした直後の人」が誰にも聞かずに起動できるREADMEを書きます。
ポイントは**鶏と卵問題**: クローン直後は `vendor/` がないため `sail` コマンドが
使えません。Composer入りコンテナで `composer install` する公式手順をREADMEに明記します。

### 足場の作り方

| ファイル | 作り方 |
|---|---|
| `README.md` | **既存ファイル(Laravel標準のREADME)を全面的に手で書き換え** |

### 編集の順序とその理由

README内の手順は「読者が実行する順」=「1. クローン → 2. composer install →
3. sail up + key:generate → 4. migrate → 5. ブラウザ確認」で書きます。
**自分が今日やった順ではなく、クローンした人がやる順**に並べ替えるのがコツです
(自分はインストーラ経由で作ったので実は手順が違う。読者視点への翻訳が必要)。

### 動作確認

READMEの動作確認は「手順どおりに再現できること」です。厳密には別ディレクトリに
クローンし直して手順を上から実行するのが理想ですが、時間がなければ最低限:

```bash
grep -n "composer install" README.md   # 鶏と卵の解決手順が入っているか
grep -n "APP_PORT" README.md           # ポート変更の案内が入っているか
```

> 本リポジトリでは、スプリント1レビューでクローンからの再現テストを実施しています
> (結果は[レビュー記録](../01_sprint1/sprint-review.md))。

### ここでコミット

```bash
git add README.md
git commit -m "docs: READMEをセットアップ手順に書き換え"
```

- **粒度の理由**: ドキュメントの書き換えは独立した1つの意味。コードと混ぜない
- **メッセージの理由**: 種別は `docs:`。「何を書いたか」より「READMEの役割が
  変わった(Laravel紹介文→この教材のセットアップ手順)」ことが伝わる表現にする

---

## ステップ1-4: PRを作ってmainへマージする

### これから何を・なぜやるか

スプリント1の成果をPull Requestにまとめ、mainに取り込みます。
一人開発でもPRを作るのは、**変更のまとまりに「レビューの単位」という意味を持たせる**ためと、
後からこのガイドのように「PR単位で振り返れる」ようにするためです。

### 手順

```bash
git status                 # 作業ツリーが綺麗なこと(コミット漏れがないこと)を確認!
git push -u origin feature/sprint1-environment
gh pr create --title "Sprint 1: Sail環境構築とセットアップ手順の整備" --body "(概要・動作確認結果を書く)"
gh pr merge --merge        # またはGitHubの画面からマージ
```

実物のPR: [#1 Sprint 1: Sail環境構築とセットアップ手順の整備](https://github.com/morisaki-yuichi/todo-app-example2/pull/1)

### マージ後の動作確認(マージ作業の一部!)

「マージしたら壊れていた」を防ぐため、**マージ後のmainで**もう一度確認します。

```bash
git switch main && git pull
./vendor/bin/sail up -d
curl -s -o /dev/null -w "%{http_code}\n" http://localhost:8080/   # => 200
```

---

## スプリント1の振り返り課題(写経者向け)

自分の言葉で、次の3つを2〜3行ずつ書いてみてください(答え合わせ・ヒントは
[レトロスペクティブ記録](../01_sprint1/sprint-retrospective.md)にあります)。

1. 「鶏と卵問題」とは何が卵で何が鶏か。どう解決したか
2. `.env` と `.env.example` の役割の違い。なぜ `.env` はコミットしないのか
3. 500エラーが出たとき、最初に見るべき場所と読み方
