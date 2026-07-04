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
| US-0 開発環境の再現 | 1-1([5621cfd](https://github.com/morisaki-yuichi/todo-app-example2/commit/5621cfd))、1-2([1c97590](https://github.com/morisaki-yuichi/todo-app-example2/commit/1c97590))、1-3([eb93e7c](https://github.com/morisaki-yuichi/todo-app-example2/commit/eb93e7c)) | [#1](https://github.com/morisaki-yuichi/todo-app-example2/pull/1)、[#2](https://github.com/morisaki-yuichi/todo-app-example2/pull/2) |
| US-1 一覧を見る | 2-1([db050d7](https://github.com/morisaki-yuichi/todo-app-example2/commit/db050d7))、2-2([8e1b772](https://github.com/morisaki-yuichi/todo-app-example2/commit/8e1b772))、2-3([75ec45b](https://github.com/morisaki-yuichi/todo-app-example2/commit/75ec45b)) | [#3](https://github.com/morisaki-yuichi/todo-app-example2/pull/3) |
| US-2 詳細を見る | 2-4([571473f](https://github.com/morisaki-yuichi/todo-app-example2/commit/571473f)) | [#3](https://github.com/morisaki-yuichi/todo-app-example2/pull/3) |
| US-3〜US-6 | スプリント3以降 | - |

## コミットに残っていない出来事

コードの差分だけでは分からない「開発中に実際に起きたこと」の一覧です。
**トラブルの調査過程こそ教材**なので、必ずリンク先も読んでください。

| 出来事 | 記録先 |
|---|---|
| ポート事前調査で `VITE_PORT=5174` が別プロジェクトと衝突 → 5180に変更 | [qa-log.md](qa-log.md) |
| `sail:install` が「no commands defined in the "sail" namespace」で失敗(インストーラがsailを同梱しなくなっていた) | [スプリント1レビュー記録](../01_sprint1/sprint-review.md) |
| わざと失敗実験: APP_KEYを空にして500を観察 | このガイドの [実験1-A](#実験1-a-わざと失敗app_keyを空にして500を観察する) |
| `.env` 変更直後にcurlが `000`(接続リセット)になった | [スプリント1レビュー記録](../01_sprint1/sprint-review.md) |
| マージ後確認で500(laravel.logに出ない)→ コンテナログで特定し `sail restart` で復旧 | [スプリント1レビュー記録](../01_sprint1/sprint-review.md) |
| クローン再現テストで3306衝突 → FORWARD_DB_PORTを明示 | [スプリント1レビュー記録](../01_sprint1/sprint-review.md)、[PR #2](https://github.com/morisaki-yuichi/todo-app-example2/pull/2) |
| わざと失敗実験: $fillableなしでMassAssignmentException | このガイドの [実験2-A](#実験2-a-わざと失敗fillableなしでcreateしてみる) |
| シーダーの3件が同一秒作成でlatest()の並びが不安定 → id第2ソートで安定化 | [ステップ2-3](#ステップ2-3-todo一覧画面を作る)、[スプリント2レビュー記録](../02_sprint2/sprint-review.md) |

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
| `port is already allocated` | ホスト側ポート衝突 → `.env` のAPP_PORT等を空きポートへ変更し `sail up -d`。**mysqlコンテナで出た場合は3306の衝突**(別のMySQL/Sailが動いている)→ `FORWARD_DB_PORT` を3307等に変更(実録: レビュー時のクローン再現テストで発生) |
| 全ページ500だが `laravel.log` に何も出ない | アプリより外側の問題。1段外側の**コンテナのログ**を見る: `sail logs laravel.test`(実録: 開発サーバのプロセス状態が壊れ `Failed opening required '/index.php'` → `sail restart` で復旧) |
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

---

# スプリント2: Read(一覧・詳細)

**ゴール**: シーダーで投入したTODOが一覧・詳細で閲覧できる(存在しないIDは404)
(計画: [スプリント2バックログ](../02_sprint2/sprint-backlog.md) / PR: [#3](https://github.com/morisaki-yuichi/todo-app-example2/pull/3))

**開始前の準備**: `git switch main && git pull` してから `git switch -c feature/sprint2-read`

このスプリントから**MVC**が登場します。先に
[概念解説集の「MVCとリクエストの流れ」](laravel-concepts.md#11-mvcとリクエストの流れ)を読むと、
以降のステップで「今どの層を作っているのか」を見失いません。

---

## ステップ2-1: todosテーブルとTodoモデルを作る

- 差分: [GitHubで見る](https://github.com/morisaki-yuichi/todo-app-example2/commit/db050d7) / ローカル: `git show db050d7`
- 【概念】[マイグレーションのup/down](laravel-concepts.md#13-マイグレーションのupdownとカラム定義) / [Eloquentと設定より規約](laravel-concepts.md#14-eloquentと設定より規約) / [$fillable](laravel-concepts.md#15-fillableとマスアサインメント) / [casts](laravel-concepts.md#16-casts型キャスト) / [tinker](laravel-concepts.md#17-tinker)

### これから何を・なぜやるか

画面より先に**データの置き場所(テーブル)と出し入れ口(モデル)**を作ります。
内側(DB)から外側(画面)へ向かって作ると、各段階で「そこまでの部品だけ」を
検証でき、問題の切り分けが簡単になるからです。

### 足場の作り方

| ファイル | 作り方 |
|---|---|
| `app/Models/Todo.php` | **ジェネレータ**で生成後、**手で編集**($fillable・casts追加) |
| `database/migrations/XXXX_create_todos_table.php` | **ジェネレータ**で生成後、**手で編集**(カラム定義追加) |

```bash
# 実行前に --help で現行仕様を確認する癖をつける(スプリント1レトロのTry)
./vendor/bin/sail artisan make:model --help
./vendor/bin/sail artisan make:model Todo -m    # -m: マイグレーションも同時生成
```

> **写経時の差異**: マイグレーションのファイル名の先頭は**生成日時のタイムスタンプ**です
> (例: `2026_07_04_110541_...`)。あなたの環境では必ず別の名前になります。正常です。

### 編集の順序とその理由

1. **マイグレーションのup()にカラム定義を追加**(器が先):
   `title` は `string('title', 100)`(DB層でも100文字制限)、`description` は
   `text()->nullable()`(任意項目)、`completed` は `boolean()->default(false)`
2. `sail artisan migrate` で適用し、**`migrate:rollback --step=1` → 再度 `migrate`** で
   down()も検証(やり直しが安全にできるか先に確かめる)
3. **モデルに `$fillable` と `casts()` を追加**(下の実験2-Aを先にやると、
   $fillableがなぜ要るのか腹落ちします)

### 実験2-A: わざと失敗($fillableなしでcreateしてみる)

**実験前チェック**(スプリント1レトロのTry T-1): `git status` で今の状態を把握し、
`curl -s -o /dev/null -w "%{http_code}" http://localhost:8080/` が200であることを確認。

モデルが空クラスのまま、tinkerで作成を試みます:

```bash
./vendor/bin/sail artisan tinker --execute="App\Models\Todo::create(['title' => 'テスト']);"
```

結果(エラーの1行目):

```
Illuminate\Database\Eloquent\MassAssignmentException
  Add [title] to fillable property to allow mass assignment on [App\Models\Todo].
```

- 1行目に**例外の名前**(マスアサインメント例外)、2行目に**対処そのもの**が書いてあります。
  Laravelのエラーは「読めば直せる」ものが多い — だからまず読む
- なぜこんな安全装置があるのか(悪意ある入力で `is_admin=1` を書き込まれる攻撃例)は
  [概念解説集15](laravel-concepts.md#15-fillableとマスアサインメント)へ

モデルに `$fillable` と `casts()` を書いてから再実行し、成功することを確認します:

```bash
./vendor/bin/sail artisan tinker --execute="
\$t = App\Models\Todo::create(['title' => 'tinker検証', 'completed' => 1]);
var_dump(\$t->completed);   // => bool(true)  ← castsの効果(DBはtinyintでもPHPではbool)
App\Models\Todo::query()->delete();  // 検証データの後片付け
"
```

**なぜtinkerで確認するのか**: 画面(ルート・コントローラ・ビュー)を作る前に、
**モデル層だけ**を単独で検証できるから。あとで画面がおかしくても
「モデル層は検証済みなので、原因はそこより外側」と切り分けられます。

**実験後チェック**: `git status` が実験前と同じで、curlが200のままであること。

### ここでコミット

```bash
git add app/Models/Todo.php database/migrations/
git commit -m "feat: todosテーブルとTodoモデルを追加"
```

- **粒度の理由**: マイグレーションとモデルは「Todoというデータの器」という1つの意味。
  逆に、この後のシーダーは「ダミーデータ投入」という別の目的なので分ける
- **メッセージの理由**: 初の機能追加なので `feat:`。本文にdown()検証と実験2-Aの
  実施を書き、「動くことをどう確かめたか」を残した(実物: `git show db050d7 -s`)

---

## ステップ2-2: シーダーでダミーデータを入れる

- 差分: [GitHubで見る](https://github.com/morisaki-yuichi/todo-app-example2/commit/8e1b772) / ローカル: `git show 8e1b772`
- 【概念】[シーダー](laravel-concepts.md#18-シーダー)

### これから何を・なぜやるか

一覧画面を作る前に、表示するデータを用意します。手作業のINSERTではなくシーダーに
するのは、**「誰でも・何度でも・同じデータを再現できる」**からです。
データの中身は「完了/未完了」「内容あり/なし」を**わざと混在**させます —
画面側の全分岐(完了表示・内容なし表示)を目視確認するためです。

### 足場の作り方

| ファイル | 作り方 |
|---|---|
| `database/seeders/TodoSeeder.php` | **ジェネレータ**で生成後、**手で編集** |
| `database/seeders/DatabaseSeeder.php` | **既存ファイルを手で編集**(TodoSeeder呼び出し・User生成削除) |

```bash
./vendor/bin/sail artisan make:seeder TodoSeeder
```

### 編集の順序とその理由

1. `TodoSeeder::run()` に3件の `Todo::create` を書く。先頭で `Todo::query()->delete()`
   して入れ直す方式にする(**再実行しても増殖しない**=冪等にするため)
2. `DatabaseSeeder` から `$this->call([TodoSeeder::class])` で呼ぶ
   (`db:seed` だけで全部入る入口を1つに保つ)。認証はスコープ外なので
   雛形にあったUser生成は削除

### 動作確認(CLI)

```bash
./vendor/bin/sail artisan db:seed
./vendor/bin/sail artisan db:seed   # わざと2回実行
./vendor/bin/sail artisan tinker --execute="echo App\Models\Todo::count();"  # => 3(6ではない)
```

**なぜ2回実行するのか**: 冪等性の確認。写経中は「エラーで途中まで入った」等で
シーダーを何度も叩くことになるため、再実行に強いことを最初に確かめておきます。

### ここでコミット

```bash
git add database/seeders/
git commit -m "feat: TodoSeederで動作確認用ダミーデータを投入可能に"
```

- **粒度の理由**: 「ダミーデータの仕組み」で1つの意味。モデルとは目的が違う
- **メッセージの理由**: 「何を入れるか」より「なぜその構成のデータか
  (全表示分岐の目視用)」を本文に残した

---

## ステップ2-3: TODO一覧画面を作る

- 差分: [GitHubで見る](https://github.com/morisaki-yuichi/todo-app-example2/commit/75ec45b) / ローカル: `git show 75ec45b`
- 【概念】[MVCとリクエストの流れ](laravel-concepts.md#11-mvcとリクエストの流れ) / [ルーティングと名前つきルート](laravel-concepts.md#12-ルーティングと名前つきルート) / [Blade](laravel-concepts.md#19-bladeテンプレートextendsyield--のxss対策)

### これから何を・なぜやるか

いよいよMVCを1周します。**ルート→コントローラ→ビュー**の順で作ります。
リクエストが通る順(外から内)に書いていくと、「今どこまで通じているか」を
段階的に確認でき、エラーが出ても最後に書いた場所が原因だと分かるからです。

### 足場の作り方

| ファイル | 作り方 |
|---|---|
| `app/Http/Controllers/TodoController.php` | **ジェネレータ**で生成後、**手で編集** |
| `routes/web.php` | **既存ファイルを手で編集** |
| `resources/views/layouts/app.blade.php` | **手で新規作成** |
| `resources/views/todos/index.blade.php` | **手で新規作成** |

```bash
./vendor/bin/sail artisan make:controller TodoController
```

### 編集の順序とその理由

1. **ルート**: `routes/web.php` に `Route::get('/todos', [TodoController::class, 'index'])->name('todos.index');`
2. **コントローラ**: `index()` で `Todo::orderByDesc('created_at')->orderByDesc('id')->get()`
   を取得してビューへ渡す
3. **レイアウト** `layouts/app.blade.php`: 全ページ共通のHTML骨格(@yieldで差し込み口)
4. **一覧ビュー** `todos/index.blade.php`: @extendsでレイアウトを継承し、
   0件なら案内文、あれば`<ul>`で列挙

> **実録(なぜidの第2ソートがあるのか)**: 当初は `latest()`(created_at降順)だけの
> 予定でしたが、tinkerで `latest()->first()` を実行したら**一番古い「牛乳を買う」が
> 返ってきました**。シーダーの3件は同一秒に作られるため created_at が同値になり、
> 並びが不定になっていたのです。「思い込みではなく実データで検証」が効いた場面で、
> 対処として id を第2ソートキーに追加しました。

> **重要**: この時点では一覧のタイトルに**リンクを張りません**。詳細ページがまだ
> 存在しないからです(「未実装ルートへのリンクを含めない」ルール。リンクを張るのは
> 行き先ができる次のステップ)。

### 動作確認(ブラウザ)

- http://localhost:8080/todos を開く
- **正常系**: 3件が「部屋の掃除 → Laravel教材… → 牛乳を買う」の順(新しい順)で並び、
  「Laravel教材…」にだけ「(完了)」が付く
- **異常系(0件)**: tinkerで `Todo::query()->delete()` してからリロードすると
  「TODOがありません。」が出る。確認後 `db:seed` で戻す

### 動作確認(CLI)

```bash
curl -s -o /dev/null -w "%{http_code}\n" http://localhost:8080/todos   # => 200
curl -s http://localhost:8080/todos | grep '<li>' 
```

### よくあるエラーと症状の対応表

| 症状 | 原因 → 対処 |
|---|---|
| `Target class [TodoController] does not exist.` | ルートで `use App\Http\Controllers\TodoController;` を書き忘れ(コピペ時に落としがち) |
| `View [todos.index] not found.` | ビューのファイル名・置き場所の不一致。`todos.index` = `resources/views/todos/index.blade.php`(ドット=ディレクトリ区切り) |
| 一覧は出るが並びがおかしい | created_at が同値(シーダー由来)。第2ソートキーを確認 |
| `Undefined variable $todos` | コントローラからビューへの受け渡し漏れ(`view('todos.index', ['todos' => $todos])`) |

### ここでコミット

```bash
git add routes/web.php app/Http/Controllers/TodoController.php resources/views/
git commit -m "feat: TODO一覧画面を追加"
```

- **粒度の理由**: ルート+コントローラ+ビュー=「一覧が見られる」という1つの意味。
  層ごとにコミットを割ると「中途半端でどこにも遷移できない」状態が履歴に残ってしまう
- **メッセージの理由**: 本文に並び順の実録(第2ソートキーの理由)を残した。
  コードだけ見ると「なぜidでもソート?」と疑問になる箇所だから

---

## ステップ2-4: TODO詳細画面とリンクを作る

- 差分: [GitHubで見る](https://github.com/morisaki-yuichi/todo-app-example2/commit/571473f) / ローカル: `git show 571473f`
- 【概念】[ルートモデルバインディングと404](laravel-concepts.md#20-ルートモデルバインディングと404)

### これから何を・なぜやるか

詳細画面(`/todos/{id}`)を作り、**最後に**一覧からリンクを張ります。
「行き先を作ってからリンクを張る」順なら、リンク切れの状態がコミットに残りません。

### 足場の作り方

| ファイル | 作り方 |
|---|---|
| `routes/web.php` | **手で編集**(showルート追加) |
| `app/Http/Controllers/TodoController.php` | **手で編集**(showメソッド追加) |
| `resources/views/todos/show.blade.php` | **手で新規作成** |
| `resources/views/todos/index.blade.php` | **手で編集**(タイトルをリンク化) |

### 編集の順序とその理由

1. ルート: `Route::get('/todos/{todo}', ...)->name('todos.show');`
2. コントローラ: `public function show(Todo $todo)` — 引数を**Todo型**にするだけで、
   URLの `{todo}` に対応するレコードをLaravelが自動取得(ルートモデルバインディング)。
   見つからなければ**自動で404**
3. 詳細ビュー: タイトル・状態・内容(なければ「(内容はありません)」)・作成日時・一覧へ戻るリンク
4. 一覧ビューのタイトルを `route('todos.show', $todo)` でリンク化(行き先ができたので解禁)

### 動作確認(ブラウザ)

- 一覧の「部屋の掃除」をクリック → 詳細が開き「状態: 未完了」「(内容はありません)」
- 「牛乳を買う」の詳細 → 内容が**改行されて**2行で表示される
- **異常系**: URLを `/todos/99999` に書き換える → Laravelの404ページ

### 動作確認(CLI)

```bash
curl -s -o /dev/null -w "%{http_code}\n" http://localhost:8080/todos/99999  # => 404
# 存在するIDでの200確認(IDは環境で違うので、まずtinkerで実IDを取る)
./vendor/bin/sail artisan tinker --execute="echo App\Models\Todo::first()->id;"
```

**なぜ404を確認するのか**: 「存在しないIDで500(サーバエラー)になる」実装は
攻撃者にスタックトレースを見せる事故につながります。404(見つからない)と
500(壊れている)の区別は、利用者にもセキュリティにも重要です。

> **写経時の差異**: レコードIDは実験・シーダー再実行の回数で変わります
> (本リポジトリでもこの時点のIDは1〜3ではなく8〜10でした)。
> 「ID=1のはず」と決め打ちせず、一覧からのリンクかtinkerで実IDを確認する癖を。

### ここでコミット

```bash
git status    # 変更ファイルが意図どおりか見る
git add -A
git commit -m "feat: TODO詳細画面と一覧からのリンクを追加"
```

- **粒度の理由**: 「詳細が見られる+そこへ辿り着ける」で1つの意味。
  リンク追加だけを別コミットにすると、間のコミットが「作ったのに辿り着けない画面」になる
- **メッセージの理由**: 本文に「行き先を先に作る順序」の意図を残した

---

## スプリント2の振り返り課題(写経者向け)

回答例は[スプリント2レトロスペクティブ](../02_sprint2/sprint-retrospective.md)にあります。

1. `/todos` をブラウザで開いてから画面が出るまで、リクエストはどの層を
   どの順に通るか(ルート・コントローラ・モデル・ビューを使って)
2. `$fillable` は何を防ぐ仕組みか。なければどんな攻撃が可能になるか
3. 「画面を作る前にtinkerで検証する」ことの利点を、切り分けの観点で説明せよ
