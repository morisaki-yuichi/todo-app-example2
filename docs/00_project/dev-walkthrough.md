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
| US-3 作成する | 3-1([751c7ce](https://github.com/morisaki-yuichi/todo-app-example2/commit/751c7ce))、3-2([493bbee](https://github.com/morisaki-yuichi/todo-app-example2/commit/493bbee))、3-3([c8e0e50](https://github.com/morisaki-yuichi/todo-app-example2/commit/c8e0e50))、3-4([e37c923](https://github.com/morisaki-yuichi/todo-app-example2/commit/e37c923)) | [#4](https://github.com/morisaki-yuichi/todo-app-example2/pull/4) |
| US-4 編集する | 4-1([b5af3c9](https://github.com/morisaki-yuichi/todo-app-example2/commit/b5af3c9)) | [#5](https://github.com/morisaki-yuichi/todo-app-example2/pull/5) |
| US-5 完了/未完了にする | 4-2([55ebc8a](https://github.com/morisaki-yuichi/todo-app-example2/commit/55ebc8a))、4-4のCSS([fa08682](https://github.com/morisaki-yuichi/todo-app-example2/commit/fa08682)) | [#5](https://github.com/morisaki-yuichi/todo-app-example2/pull/5) |
| US-6 削除する | 4-3([2296628](https://github.com/morisaki-yuichi/todo-app-example2/commit/2296628)) | [#5](https://github.com/morisaki-yuichi/todo-app-example2/pull/5) |
| US-7 教材 | 各スプリントのdocsコミット全体(このガイド・概念解説集・スクラム記録) | #1〜#5 |
| (仕上げ) | 4-4 CSS([fa08682](https://github.com/morisaki-yuichi/todo-app-example2/commit/fa08682))、4-5 トップ整理([28914b2](https://github.com/morisaki-yuichi/todo-app-example2/commit/28914b2)) | [#5](https://github.com/morisaki-yuichi/todo-app-example2/pull/5) |
| **第2部** | | |
| US-8 期限日 | 5-1([a1f586d](https://github.com/morisaki-yuichi/todo-app-example2/commit/a1f586d))、5-2([6288c60](https://github.com/morisaki-yuichi/todo-app-example2/commit/6288c60)) | [#6](https://github.com/morisaki-yuichi/todo-app-example2/pull/6) |
| US-9 絞り込み | 5-3([3357f4a](https://github.com/morisaki-yuichi/todo-app-example2/commit/3357f4a)) | [#6](https://github.com/morisaki-yuichi/todo-app-example2/pull/6) |
| US-10 ページネーション | 5-4([8b3b26d](https://github.com/morisaki-yuichi/todo-app-example2/commit/8b3b26d)) | [#6](https://github.com/morisaki-yuichi/todo-app-example2/pull/6) |

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
| わざと失敗実験: ルート定義順序ミスで /todos/create が404 | このガイドの [実験3-A](#実験3-a-わざと失敗ルートの定義順序を間違えてみる) |
| わざと失敗実験: バリデーションなしのPOSTで500(2パターン) | このガイドの [実験3-B](#実験3-b-わざと失敗バリデーションなしで不正なpostを送る) |
| curlの `-X POST` + `-L` がリダイレクト先にもPOSTを強制し419になった | [ステップ3-3](#ステップ3-3-フラッシュメッセージを出す)、[スプリント3レビュー記録](../03_sprint3/sprint-review.md) |
| わざと失敗実験: @method('PUT')を外すと405(予想的中) | このガイドの [実験4-A](#実験4-a-わざと失敗methodputを外してみる) |
| わざと失敗実験: GETでは削除できないことの検証(3パターン) | このガイドの [実験4-B](#実験4-b-わざと失敗getリクエストで削除を試みる) |
| **第2部** | |
| わざと失敗実験: SQLインジェクション(連結で全件漏れ・プレースホルダで安全) | このガイドの [実験5-A](#実験5-a-sqlインジェクションを安全に体験する) |
| 標準ページネーションビューがCSSフレームワーク前提で500 → 自前ビュー作成 | [ステップ5-4](#ステップ5-4-ページネーション) |
| curlはURL直書きの日本語をエンコードしない → `--get --data-urlencode` を使う | [ステップ5-3](#ステップ5-3-状態キーワードの絞り込み) |
| page=abc はHTTP経由なら200(tinker直渡しはTypeError)= HTTP境界での防御 | [実験5-B](#実験5-b境界-不正な-page-値と-http境界のありがたみ) |

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

---

# スプリント3: Create(新規作成)

**ゴール**: フォームからTODOを作成でき、不正な入力は入力値を保持して差し戻され、
成功時はメッセージつきで一覧に戻る(リロードしても二重登録されない)
(計画: [スプリント3バックログ](../03_sprint3/sprint-backlog.md) / PR: [#4](https://github.com/morisaki-yuichi/todo-app-example2/pull/4))

**開始前の準備**: `git switch main && git pull` → `git switch -c feature/sprint3-create`

このスプリントは概念が濃いスプリントです。
【概念】[GETでデータを変えない原則](laravel-concepts.md#21-httpメソッドの使い分けとgetでデータを変えない原則) /
[CSRF](laravel-concepts.md#22-csrfとcsrf) / [バリデーション](laravel-concepts.md#23-バリデーション自動差し戻しold値境界値) /
[PRG](laravel-concepts.md#24-prgパターン) / [フラッシュデータ](laravel-concepts.md#25-フラッシュデータ)

---

## ステップ3-1: 作成フォームと保存処理を作る

- 差分: [GitHubで見る](https://github.com/morisaki-yuichi/todo-app-example2/commit/751c7ce) / ローカル: `git show 751c7ce`

### これから何を・なぜやるか

「フォーム表示(GET)」と「保存(POST)」を**セットで**作ります。片方だけコミットすると
「送信すると405になるフォーム」という壊れた状態が履歴に残るためです。
データを変える操作をPOSTにするのは
**[GETでデータを変えない原則](laravel-concepts.md#21-httpメソッドの使い分けとgetでデータを変えない原則)**のため。
バリデーションはまだ入れません(「ない状態の危険」を実験3-Bで先に体験するため)。

### 足場の作り方

| ファイル | 作り方 |
|---|---|
| `routes/web.php` | **手で編集**(create/storeルート追加。**順序に注意** — 実験3-A参照) |
| `app/Http/Controllers/TodoController.php` | **手で編集**(create/storeメソッド追加。`use Illuminate\Http\Request;` を忘れずに) |
| `resources/views/todos/create.blade.php` | **手で新規作成**(@csrf必須) |
| `resources/views/todos/index.blade.php` | **手で編集**(「+ 新規作成」リンク追加) |

### 実験3-A: わざと失敗(ルートの定義順序を間違えてみる)

createルートを**わざと** `{todo}` ルートの後ろに書いてみます:

```php
Route::get('/todos/{todo}', [TodoController::class, 'show'])->name('todos.show');
Route::get('/todos/create', [TodoController::class, 'create'])->name('todos.create'); // わざと後ろ
```

```bash
curl -s -o /dev/null -w "%{http_code}\n" http://localhost:8080/todos/create   # => 404 !
```

- **なぜ404か**: ルートは**定義した順**に上からマッチします。`/todos/create` は先に
  `/todos/{todo}` にマッチし、「create」という文字列がTODOのIDとして解釈され、
  該当レコードがないので404になります
- **罠**: `sail artisan route:list` は**URIのアルファベット順に表示する**ため、
  一覧では正しい順に見えます。表示順≠マッチ順。定義ファイル(routes/web.php)が正
- 確認できたら `create` を `{todo}` より**前**に移動 → 200

### 編集の順序とその理由

1. ルート(create→store→既存のshowの順に並べる。**具体的なURLはワイルドカードより先**)
2. コントローラ(`create()` はビューを返すだけ。`store()` は保存して
   `redirect()->route('todos.index')` — [PRG](laravel-concepts.md#24-prgパターン))
3. フォームビュー(`@csrf` を最初に書く。忘れると419 — 下のcurl検証参照)
4. 最後に一覧へ「+ 新規作成」リンク(行き先ができてから)

### 動作確認(ブラウザ)

- 一覧 → 「+ 新規作成」→ フォームに入力 → 「作成する」→ 一覧に戻り、先頭に新TODOが出る
- **リロード(F5)しても二重登録されない**(PRGの効果。「フォーム再送信」の警告も出ない)

### 動作確認(CLI): 新出ステータスコードを意図的に再現する

「見たことがあるエラー」は怖くない。3つのコードをcurlで作り出します:

```bash
# 419: CSRFトークンなしのPOST(攻撃サイトからのPOSTはこうなって弾かれる)
curl -s -o /dev/null -w "%{http_code}\n" -X POST http://localhost:8080/todos -d "title=x"   # => 419

# 405: ルートが存在しないHTTPメソッド(GET専用のURLにPOST)
curl -s -o /dev/null -w "%{http_code}\n" -X POST http://localhost:8080/todos/create        # => 405

# 302: 正規のフロー。CSRFはトークン+Cookieの両方が必要なので、この2段階を踏む
JAR=/tmp/cookies.txt
TOKEN=$(curl -s -c "$JAR" http://localhost:8080/todos/create | grep -oP 'name="_token" value="\K[^"]+')
curl -s -b "$JAR" -o /dev/null -w "%{http_code}\n" \
     --data-urlencode "_token=$TOKEN" --data-urlencode "title=curlから作成" \
     http://localhost:8080/todos                                                            # => 302
```

**なぜこの確認方法か**: ステータスコードだけ見れば「CSRF切れ(419)/URL・メソッド違い(405)
/正常(302)」を機械的に区別できます。フォームのデバッグで「なんか動かない」と思ったら、
まずこの3値のどれかを疑うと速い。

> **写経時の差異**: `_token` の値はセッションごとに毎回変わります。上のように
> 「取得してから使う」形にし、値を決め打ちでコピペしないこと。

### ここでコミット

```bash
git add -A
git commit -m "feat: TODO作成フォームと保存処理を追加"
```

- **粒度の理由**: フォーム(GET)と保存(POST)で「作成できる」という1つの意味。
  リンク追加も含め、このコミット単体で機能が完結する
- **メッセージの理由**: 本文にルート順序の理由と「バリデーションは意図的に未実装」を
  明記(次のコミットへの伏線を履歴に残す)

---

## ステップ3-2: バリデーションを入れる

- 差分: [GitHubで見る](https://github.com/morisaki-yuichi/todo-app-example2/commit/493bbee) / ローカル: `git show 493bbee`

### 実験3-B: わざと失敗(バリデーションなしで不正なPOSTを送る)

実装前に、**今の実装がどう壊れるか**を見ておきます(実験前後チェックを忘れずに)。

```bash
# (a) titleフィールド自体を送らない
TOKEN=$(curl -s -c "$JAR" http://localhost:8080/todos/create | grep -oP 'name="_token" value="\K[^"]+')
curl -s -b "$JAR" -o /dev/null -w "%{http_code}\n" \
     --data-urlencode "_token=$TOKEN" --data-urlencode "description=titleなし" \
     http://localhost:8080/todos    # => 500 !
grep ERROR storage/logs/laravel.log | tail -1
# => SQLSTATE[HY000]: General error: 1364 Field 'title' doesn't have a default value

# (b) 空文字のtitleを送る
# => これも500。ログには「Column 'title' cannot be null」
```

- (a) はDBの「titleに既定値がない」エラー。**DBが最後の砦**として機能した形
- (b) は予想が外れた実録: 「空タイトルのゴミデータができる」と予想したが、
  Laravelは標準ミドルウェア(ConvertEmptyStringsToNull)が**空文字をnullに変換**するため、
  NOT NULL制約違反の500になった。**思い込みではなく実出力で検証**の実例
- どちらにせよ利用者に500を見せるのは事故。**入口(バリデーション)で守る**必要がある

### 足場の作り方

| ファイル | 作り方 |
|---|---|
| `app/Http/Controllers/TodoController.php` | **手で編集**(store冒頭に `$request->validate([...])`) |
| `resources/views/todos/create.blade.php` | **手で編集**(エラー表示 `$errors->all()` と `old()` の追加) |

### 編集の順序とその理由

1. コントローラに `validate()`(守りの本体が先)
2. ビューにエラー表示と `old()`(守った結果を見せるのが後)

`validate()` は違反時に**自動で**フォームへ302リダイレクトし、エラー内容と入力値を
セッションに積んでくれます(自動差し戻し)。自分でif文を書く必要はありません。

### 動作確認(ブラウザ)

- 空のまま送信 → フォームに戻り「The title field is required.」が出る。
  **descriptionに入れた文字は消えずに残っている**(old値)
- **境界値**: タイトルに100文字ちょうど → 成功。101文字 → エラー。
  (100文字の文字列作り: `python3 -c "print('あ'*100)"` をコピペ)
- ※ エラーメッセージが英語なのはLaravel標準のまま使っているため。日本語化には
  言語ファイルの整備が必要で、本教材ではスコープ外(概念解説集23で言及)

### 動作確認(CLI)

```bash
# 空titleが「500」ではなく「302(差し戻し)」に変わったことを確認
TOKEN=$(curl -s -c "$JAR" http://localhost:8080/todos/create | grep -oP 'name="_token" value="\K[^"]+')
curl -s -b "$JAR" -o /dev/null -w "%{http_code}\n" \
     --data-urlencode "_token=$TOKEN" --data-urlencode "title=" \
     http://localhost:8080/todos    # => 302(実験3-Bでは500だった)
```

**なぜ302を確認するのか**: 成功も差し戻しも302です。「302だから成功」ではなく、
**行き先**(Location: 一覧なら成功、フォームなら差し戻し)までがワンセット。

### よくあるエラーと症状の対応表

| 症状 | 原因 → 対処 |
|---|---|
| 送信すると419 | @csrfの書き忘れ / セッション切れ(フォームを開き直す) |
| 送信すると405 | formのaction先ルートのメソッド不一致(POSTルートがあるか `route:list` で確認) |
| /todos/create が404 | ルート定義順序({todo}が先に食べている)。実験3-A参照 |
| エラーは出るが入力値が消える | ビューに `old('title')` を書いていない |
| 100文字でもエラーになる | `max:100` は**文字数**(バイト数ではない)。全角で試したか、余計な空白が入っていないか |

### ここでコミット

```bash
git add -A && git commit -m "feat: TODO作成にバリデーションを追加(required/max:100/max:1000)"
```

- **粒度の理由**: 「入口の防御」というひとまとまり。フォーム本体(3-1)と分けたことで、
  差分に「守りに必要な変更」だけが写る
- **メッセージの理由**: 本文に実験3-Bの結果(なぜ必要だったか)と境界値の検証結果を記録

---

## ステップ3-3: フラッシュメッセージを出す

- 差分: [GitHubで見る](https://github.com/morisaki-yuichi/todo-app-example2/commit/c8e0e50) / ローカル: `git show c8e0e50`

### これから何を・なぜやるか

作成成功後、一覧に「TODOを作成しました。」を**一度だけ**表示します。
PRGでリダイレクトすると「成功した」という情報が次のページに渡らないので、
**次の1リクエストだけ生きるセッション値**=フラッシュデータで渡します。

### 足場の作り方

| ファイル | 作り方 |
|---|---|
| `app/Http/Controllers/TodoController.php` | **手で編集**(redirectに `->with('status', ...)`) |
| `resources/views/layouts/app.blade.php` | **手で編集**(`session('status')` の表示。全画面で使うので**レイアウトに**置く) |

### 動作確認(ブラウザ)

- TODOを作成 → 一覧の先頭に「TODOを作成しました。」→ **F5でリロード → メッセージだけ消える**
  (これがフラッシュの寿命。TODO自体は残る)

### 動作確認(CLI)と実録トラブル

```bash
TOKEN=$(curl -s -c "$JAR" http://localhost:8080/todos/create | grep -oP 'name="_token" value="\K[^"]+')
curl -s -b "$JAR" -c "$JAR" -L \
     --data-urlencode "_token=$TOKEN" --data-urlencode "title=フラッシュ確認" \
     http://localhost:8080/todos | grep 'TODOを作成しました。'
```

> **実録(curlの罠)**: 最初 `-X POST` を付けて `-L` と併用したところ、メッセージが
> 出ませんでした。調査(`-v` でヘッダーを見る)の結果、**`-X POST` はリダイレクト先への
> リクエストもPOSTに強制する**ことが判明(2回目のPOSTが419に)。ブラウザは302を受けると
> **GETに切り替えて**リダイレクト先を開きます — PRGが二重登録を防げるのはこの挙動の
> おかげです。curlでは `--data` を使えば自動でPOSTになるので `-X POST` は書かないこと。

### ここでコミット

```bash
git add -A && git commit -m "feat: TODO作成成功時のフラッシュメッセージを追加"
```

- **粒度の理由**: 「成功の可視化」という独立したUX改善。バリデーションとは目的が別
- **メッセージの理由**: 本文にcurlの罠(-X POSTと-L)の実録を残した。
  再び踏んだとき、`git log --grep="curl"` で見つけられる

---

## ステップ3-4: シーダーの時刻をずらす(前スプリントのTryの実行)

- 差分: [GitHubで見る](https://github.com/morisaki-yuichi/todo-app-example2/commit/e37c923) / ローカル: `git show e37c923`

スプリント2のレトロで出したTry(T-4)の実行です。シーダー3件が同一秒だと
「新しい順」を目視検証できないため、`created_at` を2日前/1日前/今日に分散します。

- `Todo::create()` は `created_at` を配列で渡しても**$fillableにないため無視**します。
  `new Todo([...])` → `$todo->created_at = now()->subDays(2);` → `$todo->save()` の
  形にするのがポイント(プロパティ直接代入は$fillableの制限を受けない)
- 確認: `sail artisan db:seed` → 一覧の並びが「部屋の掃除(今日)→教材(昨日)→牛乳(一昨日)」

```bash
git add database/seeders/TodoSeeder.php && git commit -m "chore: シーダーのcreated_atを日単位でずらす"
```

- **粒度・種別の理由**: アプリの機能は変わらないので `feat` ではなく `chore`。
  レトロのTryをコミットとして実行し、履歴に「振り返り→改善」の痕跡を残す

---

## スプリント3の振り返り課題(写経者向け)

回答例は[スプリント3レトロスペクティブ](../03_sprint3/sprint-retrospective.md)にあります。

1. CSRF攻撃はどんな手口か。@csrfトークンはなぜそれを防げるのか
2. PRGパターンがないと何が起きるか。「302を受けたブラウザがGETで開き直す」ことと
   合わせて説明せよ
3. バリデーションとDBの制約(NOT NULL、varchar(100))は役割がどう違うか。
   なぜ両方必要か

---

# スプリント4: Update / Delete と仕上げ

**ゴール**: 編集・完了切り替え・削除(確認ページ経由)ができ、アプリと教材が完成する
(計画: [スプリント4バックログ](../04_sprint4/sprint-backlog.md) / PR: [#5](https://github.com/morisaki-yuichi/todo-app-example2/pull/5))

**開始前の準備**: `git switch main && git pull` → `git switch -c feature/sprint4-update-delete`

【概念】[メソッドスプーフィング](laravel-concepts.md#26-メソッドスプーフィングmethod) /
[確認ページ方式](laravel-concepts.md#27-確認ページ方式) /
[設定の2段構え](laravel-concepts.md#28-設定の2段構え環境変数configアプリ) / [DRY](laravel-concepts.md#29-dry)

---

## ステップ4-1: 編集機能を作る

- 差分: [GitHubで見る](https://github.com/morisaki-yuichi/todo-app-example2/commit/b5af3c9) / ローカル: `git show b5af3c9`

### これから何を・なぜやるか

編集は **Read(既存値の表示)+ Create(フォーム送信)の合成技**です。新しい部品は
2つだけ: HTMLフォームでPUTを表現する**メソッドスプーフィング**と、`old()` の第2引数です。

### 足場の作り方

| ファイル | 作り方 |
|---|---|
| `routes/web.php` | **手で編集**(edit=GET、update=PUTの2ルート追加) |
| `app/Http/Controllers/TodoController.php` | **手で編集**(edit/updateメソッド追加) |
| `resources/views/todos/edit.blade.php` | **手で新規作成**(create.blade.phpを参考に) |
| `resources/views/todos/show.blade.php` | **手で編集**(編集リンク追加) |

### 編集の順序とその理由

いつもどおり、ルート → コントローラ → ビュー → リンクの順。

- `update()` のバリデーションルールは `store()` と**同一にコピー**します
  (US-4の受け入れ条件が「作成時と同一に動作」のため。この重複はDRY違反では?という
  論点は[概念解説集29](laravel-concepts.md#29-dry)で扱います)
- ビューの入力値は `old('title', $todo->title)`:
  **差し戻し時はold値、初回表示は現在のDB値**という二段構え

### 実験4-A: わざと失敗(@method('PUT')を外してみる)

**予想を先に書く**(スプリント3レトロのTry): @methodがないと素の
`POST /todos/{id}` が送られる。このURL+POSTのルートは未定義なので**405になるはず**。

```bash
# フォームからトークンを取り、_method を付けずにPOST
curl -s -b "$JAR" -o /dev/null -w "%{http_code}\n" \
     --data-urlencode "_token=$TOKEN" --data-urlencode "title=x" \
     http://localhost:8080/todos/<実ID>    # => 405(予想的中)
```

**結果**: 予想どおり405。`@method('PUT')` の実体は
`<input type="hidden" name="_method" value="PUT">` という**ただの隠しフィールド**で、
Laravelがこれを見てPUTルートへ振り分けています(ブラウザが送るのはあくまでPOST)。

### 動作確認(ブラウザ)

- 詳細 → 「このTODOを編集する」→ **現在の値が入った**フォーム
- タイトルを変えて「更新する」→ 詳細に戻り「TODOを更新しました。」+新タイトル
- **異常系**: タイトルを空にして送信 → エラー表示で差し戻し(作成時と同じ挙動)

### ここでコミット

```bash
git add -A && git commit -m "feat: TODO編集機能を追加(メソッドスプーフィング)"
```

- **粒度の理由**: edit+update+リンクで「編集できる」という1つの意味
- **メッセージの理由**: 本文に実験4-Aの予想と結果を記録(予想を書く習慣を履歴にも残す)

---

## ステップ4-2: 完了/未完了トグルを作る

- 差分: [GitHubで見る](https://github.com/morisaki-yuichi/todo-app-example2/commit/55ebc8a) / ローカル: `git show 55ebc8a`

### これから何を・なぜやるか

一覧から1クリックで完了⇔未完了を切り替えます。**状態が変わる操作なのでGETリンクは禁止**
— 各行に小さなフォーム(PATCH)を置きます。PUTでなくPATCHなのは
「リソースの一部だけの変更」だから(使い分けの慣習)。

### 足場の作り方

| ファイル | 作り方 |
|---|---|
| `routes/web.php` | **手で編集**(PATCH toggleルート) |
| `app/Http/Controllers/TodoController.php` | **手で編集**(toggleメソッド。`!$todo->completed` で反転) |
| `resources/views/todos/index.blade.php` | **手で編集**(各行にフォームとボタン) |

### 動作確認(ブラウザ+CLI)

- 「完了にする」→ 一覧に戻り「TODOを完了にしました。」ボタンが「未完了に戻す」に変わる
- もう一度押すと元に戻る(**双方向**を必ず確認。片方向しか試さないと反転バグを見逃す)
- CLI: `tinker --execute="var_dump(App\Models\Todo::find(<実ID>)->completed);"`
  で **bool(true)/bool(false)** を確認(画面は正しくてもDBが違う、を潰す)

### ここでコミット

```bash
git add -A && git commit -m "feat: 完了/未完了のトグルを追加(PATCH)"
```

- **粒度の理由**: US-5がまるごと1コミット。編集(US-4)とは別のストーリーなので分ける

---

## ステップ4-3: 削除を確認ページ方式で作る

- 差分: [GitHubで見る](https://github.com/morisaki-yuichi/todo-app-example2/commit/2296628) / ローカル: `git show 2296628`

### これから何を・なぜやるか

削除には誤操作防止の確認ステップが必要ですが、本プロジェクトはJSを使わないので
`confirm()` は使えません。そこで**確認ページ方式**:
「GET(確認ページ=**表示するだけ**)」と「DELETE(実行)」の2段に分けます。

### 足場の作り方

| ファイル | 作り方 |
|---|---|
| `routes/web.php` | **手で編集**(GET confirmDestroy + DELETE destroyの2ルート) |
| `app/Http/Controllers/TodoController.php` | **手で編集**(confirmDestroy/destroyメソッド) |
| `resources/views/todos/confirm-destroy.blade.php` | **手で新規作成**(対象タイトル表示+削除フォーム+キャンセルリンク) |
| `resources/views/todos/show.blade.php` | **手で編集**(確認ページへのリンク) |

### 編集の順序とその理由

確認ページへの**リンクはGETでよい**(ページを表示するだけでデータを変えないから)。
実行フォームは `@method('DELETE')`。「リンク=見るだけ」「フォーム=変える」という
役割分担が崩れていないか、実装後に実験4-Bで検証します。

### 実験4-B: わざと失敗(GETリクエストで削除を試みる)

**予想を先に書く**: (a) 確認ページを何度GETしても消えない(表示のみ)。
(b) GETに `?_method=DELETE` を付けても、スプーフィングは**POSTでしか効かない**ので消えない。
(c) 正規のDELETEでは対象だけ消え、**他のデータは残る**。

```bash
curl -s -o /dev/null -w "%{http_code}\n" http://localhost:8080/todos/<実ID>/delete  # 2回実行
curl -s -o /dev/null -w "%{http_code}\n" "http://localhost:8080/todos/<実ID>?_method=DELETE"
./vendor/bin/sail artisan tinker --execute="echo App\Models\Todo::count();"   # 減っていない!
```

**結果**: 3つとも予想どおり。正規の削除後は件数が1減り、**残り2件のタイトルも確認**
(「対象が消えた」だけでなく「他が残っている」まで見るのが削除確認の作法 —
全件deleteしてしまうバグはこの確認でしか捕まえられません)。削除済みIDの詳細は404。

### 動作確認(ブラウザ)

- 詳細 → 「このTODOを削除する…」→ 確認ページ(タイトルと警告)
- 「キャンセル」→ 消えずに詳細へ戻る
- もう一度確認ページ → 「削除する」→ 一覧に戻り「TODOを削除しました。」対象だけ消えている

### よくあるエラーと症状の対応表

| 症状 | 原因 → 対処 |
|---|---|
| 削除ボタンで419 | 確認ページのフォームに@csrf忘れ |
| 削除ボタンで405 | @method('DELETE')忘れ(素のPOSTにはルートがない) |
| 確認ページが404 | ルート順序({todo}が先に食べる)…ではない! `/todos/{todo}/delete` は2セグメントなので{todo}(1セグメント)とは衝突しない。単純なタイポやルート未定義を疑う |
| 削除後の一覧で対象が残って見える | ブラウザキャッシュ。リロード。それでも残るならdestroyが呼ばれていない(route:listで確認) |

### ここでコミット

```bash
git add -A && git commit -m "feat: TODO削除を確認ページ方式で追加"
```

---

## ステップ4-4: Plain CSSで見た目を整える

- 差分: [GitHubで見る](https://github.com/morisaki-yuichi/todo-app-example2/commit/fa08682) / ローカル: `git show fa08682`

### 足場の作り方

| ファイル | 作り方 |
|---|---|
| `public/css/app.css` | **手で新規作成** |
| `resources/views/layouts/app.blade.php` | **手で編集**(`<link rel="stylesheet" href="{{ asset('css/app.css') }}">`) |
| `resources/views/todos/index.blade.php` | **手で編集**(`todo-list`クラスと完了時の`completed`クラス) |
| `resources/views/todos/create.blade.php` / `edit.blade.php` | **手で編集**(エラーリストに`errors`クラス) |

- `public/` 直下のファイルはそのままWebに公開されます。`asset()` はそこへのURLを作る
  ヘルパ(将来ドメインやサブディレクトリが変わっても追従)
- 完了の区別は**liのクラス+CSSの打ち消し線**で表現(見た目の関心はCSSに寄せる)
- ビルド(Vite)は不要。素のCSSファイルを置くだけ

### 動作確認(ブラウザ)

全5画面(一覧・詳細・作成・編集・削除確認)を開き、崩れがないこと。
完了TODOに打ち消し線が付くこと。**CSSが効かないときは** `curl -I
http://localhost:8080/css/app.css` で200が返るか(パス間違いの切り分け)。

### ここでコミット

```bash
git add -A && git commit -m "style: Plain CSSで一覧・フォーム・通知の見た目を整える"
```

- **種別の理由**: 挙動を変えない見た目の変更なので `style:`

---

## ステップ4-5: トップページを一覧へリダイレクト

- 差分: [GitHubで見る](https://github.com/morisaki-yuichi/todo-app-example2/commit/28914b2) / ローカル: `git show 28914b2`

`/` はもうWelcome画面である必要がないので、`Route::redirect('/', '/todos')` に変え、
`welcome.blade.php` を削除します。**使わなくなったファイルはコミットで消す**
(「いつか使うかも」で残すと、読者がどれが本物か分からなくなる。Gitに履歴があるので
いつでも戻せます)。

- 確認: `curl -s -o /dev/null -w "%{http_code} → %{redirect_url}\n" http://localhost:8080/`
  → `302 → http://localhost:8080/todos`

```bash
git add -A && git commit -m "feat: トップページをTODO一覧へリダイレクト"
```

---

## 仕上げ: PRマージと全機能の通し確認

```bash
git status    # 綺麗なことを確認
git push -u origin feature/sprint4-update-delete
gh pr create ... && gh pr merge ...
git switch main && git pull
```

マージ後、**CRUD一周の通し確認**をブラウザで行います:
作成 → 一覧で確認 → 詳細 → 編集 → 完了にする → 削除(確認ページ経由)→ 一覧が元どおり。
これが全部通れば、プロダクトゴール達成です。

---

## スプリント4の振り返り課題(写経者向け)

回答例は[スプリント4レトロスペクティブ](../04_sprint4/sprint-retrospective.md)にあります。

1. メソッドスプーフィングとは何か。「ブラウザが実際に送っているもの」と
   「Laravelが解釈するもの」を区別して説明せよ
2. 削除を「確認ページ方式」にした理由を、技術制約と安全性の両面から説明せよ
3. 4スプリントを通して、「動くことをどう確かめるか」について自分の習慣に
   したいことを3つ挙げよ

---
---

# 第2部: 追加開発編

第1部ではゼロからアプリを作りました。しかし実務のコードの大半は
**「すでに動いているものへの追加・変更」**です。第2部では同じリポジトリに
機能を足しながら、追加開発ならではの考え方を追体験します。

## 追加開発の心得(第2部の各ステップに追加される3要素)

| 要素 | 内容 | なぜ必要か |
|---|---|---|
| **影響調査** | 書く前に「どのファイルを読み・何に影響するか」を調べる | 新規開発は「白紙に書く」、追加開発は「他人(過去の自分)の絵に描き足す」。まず絵の全体を見ないと、思わぬ場所を塗りつぶす |
| **既存データへの配慮** | テーブル変更は既存レコードが生き残る形で行う | 本番DBには利用者のデータが入っている。「作り直せばいい」は新規開発でしか通用しない |
| **リグレッション確認** | 新機能の確認に加えて**既存機能が壊れていないこと**を確認する | 追加開発のバグの多くは「新機能が動かない」ではなく「元の機能が壊れた」として現れる |

> 第1部を写経済みのリポジトリがあれば、そのまま続けられます。
> mainが第1部完了時点(PR #5マージ地点)であることを `git log` で確認してください。

---

# スプリント5: Readの発展(期限日・絞り込み・ページネーション)

**ゴール**: 稼働中のアプリに、既存データ・既存機能を壊さずに
「期限日・絞り込み・ページネーション」を追加する
(計画: [スプリント5バックログ](../05_sprint5/sprint-backlog.md) / 仕様: [qa-log.md](qa-log.md))

**開始前の準備**: `git switch main && git pull` → `git switch -c feature/sprint5-due-date-filter`

## ステップ5-0: 影響調査(コミットなし・でも最重要)

期限日を追加すると何が変わるか、**コードを書く前に**調べます。

```bash
# 「Todo」に触れているファイルの一覧(=候補地図)
grep -rl "Todo" app/ resources/views/ routes/ database/ --include="*.php"

# 現在のテーブル構造(変更対象の現状)
./vendor/bin/sail artisan db:table todos
```

この調査から導いた変更計画(実録):

| ファイル | 変更内容 |
|---|---|
| 新規マイグレーション | due_dateカラム追加(**既存のcreate_todosは触らない** — 適用済みマイグレーションの書き換えは他環境と食い違う事故のもと) |
| `app/Models/Todo.php` | $fillableとcastsにdue_date |
| `TodoController` | store/updateのバリデーションにdue_date |
| `create/edit/show/index` の4ビュー | 入力欄・表示の追加 |
| `TodoSeeder` | 期限のバリエーション追加 |
| `routes/web.php` | **変更不要**(絞り込みは既存ルート+クエリパラメータ) |
| `public/css/app.css` | 期限切れ強調のスタイル |

**なぜ調査を記録するのか**: レビューする人(未来の自分)が「変更漏れがないか」を
この表と差分を突き合わせて確認できるからです。

## ステップ5-1: due_dateカラムを追加する(追加マイグレーション)

- 差分: [GitHubで見る](https://github.com/morisaki-yuichi/todo-app-example2/commit/a1f586d) / ローカル: `git show a1f586d`
- 【概念】[追加マイグレーション](laravel-concepts.md#30-追加マイグレーション稼働中テーブルの変更)

### これから何を・なぜやるか

第1部との最大の違いはここ。テーブルに列を足すのに、既存のマイグレーションファイルを
**書き換えるのではなく**、変更専用のマイグレーションを**新しく積みます**。
マイグレーションは「DBの変更履歴」— 歴史は書き換えず、追記します。

### 足場の作り方

| ファイル | 作り方 |
|---|---|
| `database/migrations/XXXX_add_due_date_to_todos_table.php` | **ジェネレータ**で生成後、**手で編集** |
| `app/Models/Todo.php` | **手で編集**($fillable・castsにdue_date) |
| `database/seeders/TodoSeeder.php` | **手で編集**(期限3パターン: 期限切れ/過去日だが完了/なし) |

```bash
./vendor/bin/sail artisan make:migration add_due_date_to_todos_table --table=todos
```

- `--table=todos`(既存テーブルの変更)であって `--create` ではない点に注意
- **既存データへの配慮**: `->nullable()` が必須。NOT NULLで追加すると、
  既存レコードが値を持てず**マイグレーション自体が失敗**します

### 動作確認(CLI): 「データが生き残る」ことを見る

```bash
./vendor/bin/sail artisan tinker --execute="echo App\Models\Todo::count();"   # 適用前の件数を記録
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan tinker --execute="var_dump(App\Models\Todo::first()->due_date);"
# => NULL(既存行は期限なしとして生き残っている)
./vendor/bin/sail artisan migrate:rollback --step=1   # downも検証(データは残り、列だけ消える)
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan db:seed                     # 期限バリエーション入りで入れ直し
```

### リグレッション確認

```bash
curl -s -o /dev/null -w "%{http_code}\n" http://localhost:8080/todos          # => 200
curl -s -o /dev/null -w "%{http_code}\n" http://localhost:8080/todos/create   # => 200
```

列を足しただけなので画面は変わらないはず — 「変わらないことの確認」もリグレッション確認です。

### ここでコミット

```bash
git add -A && git commit -m "feat: todosにdue_date(期限日)カラムを追加"
```

- **粒度の理由**: 「データの器の変更」だけで1コミット。画面はまだ触らない。
  こうすると万一問題が出たとき「器の問題か、画面の問題か」を履歴で切り分けられる

## ステップ5-2: 期限日の入力・表示と期限切れ強調

- 差分: [GitHubで見る](https://github.com/morisaki-yuichi/todo-app-example2/commit/6288c60) / ローカル: `git show 6288c60`

### これから何を・なぜやるか

器(カラム)ができたので、入力(フォーム)と出力(一覧・詳細)を繋ぎます。
「期限切れかどうか」の判定は**モデルのメソッド `isOverdue()`** に置きます —
一覧と詳細の2箇所で使う判定ルールをビューにベタ書きすると、
基準がズレる事故(DRY違反)になるからです。

### 足場の作り方

| ファイル | 作り方 |
|---|---|
| `app/Models/Todo.php` | **手で編集**(isOverdue()メソッド追加) |
| `app/Http/Controllers/TodoController.php` | **手で編集**(store/updateの検証に `'due_date' => ['nullable', 'date']`) |
| `create/edit/show/index` の4ビュー | **手で編集**(入力欄・表示。影響調査の表どおり) |
| `public/css/app.css` | **手で編集**(.due / .overdue) |

### 編集の順序とその理由

1. **モデル**の `isOverdue()`(判定ルールが先。「今日が期限」は期限切れに含めない=
   `lt(today())` という境界の決定もここで言語化する)
2. **コントローラ**のバリデーション(入口の防御)
3. **フォーム2枚**(`<input type="date">`。editは `old('due_date', $todo->due_date?->format('Y-m-d'))` —
   input[type=date]の値は `Y-m-d` 文字列なのでCarbonから形式を合わせる)
4. **表示2枚+CSS**(一覧は期限切れだけ赤強調。**完了済みの過去日は強調しない**のが仕様)

### 動作確認(ブラウザ)

シーダーの3件がそのまま試験データです:
- 「牛乳を買う」(未完了・昨日期限)→ 一覧と詳細に**赤い「期限切れ」**
- 「Laravel教材…」(完了・昨日期限)→ 期限は出るが**強調されない**
- 「部屋の掃除」(期限なし)→ 期限表示自体が出ない
- 新規作成で期限 `2026-07-10` を入れる → 詳細・一覧に反映
- **異常系**: due_dateに手打ちで不正な値(ブラウザのdate入力を避けてcurlで
  `due_date=あした` 等)→ `The due date field must be a valid date.`

### リグレッション確認

```bash
for u in /todos /todos/create /todos/<実ID> /todos/<実ID>/edit /todos/<実ID>/delete; do
  curl -s -o /dev/null -w "$u => %{http_code}\n" "http://localhost:8080$u"
done   # すべて200のまま
```

### ここでコミット

```bash
git add -A && git commit -m "feat: 期限日の入力・表示と期限切れの強調表示を追加"
```

- **粒度の理由**: 「利用者から見て期限日機能が使える」単位。器(5-1)と分けたので、
  この差分には「見た目と入口」だけが写っている

## ステップ5-3: 状態・キーワードの絞り込み

- 差分: [GitHubで見る](https://github.com/morisaki-yuichi/todo-app-example2/commit/3357f4a) / ローカル: `git show 3357f4a`
- 【概念】[クエリパラメータとGET絞り込み](laravel-concepts.md#31-クエリパラメータとget絞り込み) / [SQLインジェクション](laravel-concepts.md#32-sqlインジェクションとプレースホルダ)

### これから何を・なぜやるか

一覧に「状態(すべて/未完了/完了)」と「キーワード(タイトル・内容の部分一致)」の
絞り込みを付けます。**ルートは増やしません** — 既存の `GET /todos` に
クエリパラメータ(`?status=open&keyword=牛乳`)を足すだけ。絞り込みは
「見るだけ」の操作なので[GETが正解](laravel-concepts.md#21-httpメソッドの使い分けとgetでデータを変えない原則)です
(URLに条件が乗る=共有・ブックマークできる利点も付いてくる)。

### 足場の作り方

| ファイル | 作り方 |
|---|---|
| `TodoController::index()` | **手で編集**(クエリ組み立て) |
| `resources/views/todos/index.blade.php` | **手で編集**(GETフォーム+0件時の文言分岐) |
| `public/css/app.css` | **手で編集**(.filter) |

### 実装の要点

- `index(Request $request)` に引数を追加し、`$request->query('status', 'all')` で受ける
- **orWhereはクロージャで括る**:
  ```php
  $query->where('completed', false)   // 状態の条件
        ->where(function ($q) use ($keyword) {   // ← これで括らないと…
            $q->where('title', 'like', "%{$keyword}%")
              ->orWhere('description', 'like', "%{$keyword}%");
        });
  ```
  括らないと `完了=false AND title LIKE .. OR description LIKE ..` となり、
  ORが状態条件を打ち消して**完了済みも表示されるバグ**になります(演算子の優先順位)
- 想定外のstatus値(`?status=hack`)は「すべて」として扱う(不正な値で500にしない)

### 実験5-A: SQLインジェクションを安全に体験する

**予想を先に書く**(Try T-7): キーワードに `' OR '1'='1` を入れたとき、
文字列連結でSQLを組むと**全件が漏れる**はず。プレースホルダなら「そういう名前の
TODOを探す」だけで0件のはず。

tinkerで**SELECTのみ**(データは変えない)で比較します:

```php
$kw = "' OR '1'='1";
DB::select("select * from todos where title like '%{$kw}%'");   // (1) 危険: 連結
DB::select("select * from todos where title like ?", ["%{$kw}%"]); // (2) 安全: プレースホルダ
App\Models\Todo::where('title', 'like', "%{$kw}%")->count();      // (3) Eloquent
```

**結果**(実録): (1) は**全12件が漏れた**(`OR '1'='1'` が常に真になりWHEREが無効化)。
(2) と (3) は**0件**(入力はただの文字列として扱われ、そんなタイトルは無い)。
本アプリの絞り込みは(3)のEloquentを使っているので安全 — **なぜ安全かを実演で確認**しました。

> この実験はコミットしません。tinkerでSELECTしただけなのでデータは無傷
> (`git status` が綺麗なことを確認して次へ)。

### 動作確認(CLI): curlの日本語エンコードの罠

```bash
# ✕ URLに日本語を直書きすると、curlはエンコードせず送るためヒットしない
curl -s "http://localhost:8080/todos?keyword=牛乳"        # 0件になってしまう
# ○ --get --data-urlencode で正しくエンコードする
curl -s --get --data-urlencode "keyword=牛乳" http://localhost:8080/todos
```

- 状態: `?status=done` で完了のみ、`?status=open` で未完了のみ
- 組み合わせ: `status=done&keyword=教材` で1件
- **0件**: 存在しないキーワード → 「条件に一致するTODOがありません。」
  (絞り込み時と全体0件で文言を出し分けている)
- **異常系**: `?status=hack` → 200(「すべて」扱い)

### リグレッション確認

絞り込みなしの `/todos` が従来どおり全件出ること、既存CRUDが200であること。

### ここでコミット

```bash
git add -A && git commit -m "feat: 一覧に状態・キーワードの絞り込みを追加"
```

## ステップ5-4: ページネーション

- 差分: [GitHubで見る](https://github.com/morisaki-yuichi/todo-app-example2/commit/8b3b26d) / ローカル: `git show 8b3b26d`
- 【概念】[ページネーションとwithQueryString](laravel-concepts.md#33-ページネーションとwithquerystring)

### これから何を・なぜやるか

件数が増えても見通せるよう、一覧を5件/ページに分割します。`get()` を
`paginate(5)` に変えるのが中心。**絞り込み条件をページ移動でも保つ**のが要点です。

### 足場の作り方

| ファイル | 作り方 |
|---|---|
| `TodoController::index()` | **手で編集**(`get()` → `paginate(5)->withQueryString()`) |
| `resources/views/todos/index.blade.php` | **手で編集**(`$todos->links(...)`) |
| `resources/views/pagination/simple.blade.php` | **手で新規作成**(自前のページ送りビュー) |
| `database/seeders/TodoSeeder.php` | **手で編集**(12件に増やす) |
| `public/css/app.css` | **手で編集**(nav.pagination) |

### 実録トラブル: 標準ページネーションビューが見つからない

最初 `$todos->links('pagination::simple-default')` と書いたら
**`View [simple-default] not found` で全ページ500**になりました。

- **調査**: 500ページのエラー1行目 = ビューが見つからない。Laravelの標準
  ページネーションビューは**Tailwind/Bootstrap前提**の名前しかない
- **対処**: 本プロジェクトはCSSフレームワーク不使用なので、paginatorのメソッド
  (`onFirstPage()` / `hasMorePages()` / `previousPageUrl()` / `nextPageUrl()` /
  `currentPage()` / `lastPage()`)で**自前のビュー**を書き、`links('pagination.simple')`
  で指定した

### `withQueryString()` を忘れると

ページ移動リンクに絞り込み条件(`status`/`keyword`)が乗らず、
**2ページ目に行くと絞り込みが解除される**バグになります。`withQueryString()` を
付けると現在のクエリパラメータをページリンクに引き継げます。

### 動作確認(CLI)

```bash
for p in 1 2 3; do echo -n "page=$p: "; curl -s "http://localhost:8080/todos?page=$p" | grep -c '<li class'; done
# => 5 / 5 / 2(合計12)
curl -s --get --data-urlencode "status=open" http://localhost:8080/todos | grep -oE 'page=2[^"]*'
# => ページリンクに status=open が乗っている
```

### 実験5-B(境界): 不正な page 値と、HTTP境界のありがたみ

```bash
curl -s -o /dev/null -w "%{http_code}\n" "http://localhost:8080/todos?page=abc"   # => 200
curl -s -o /dev/null -w "%{http_code}\n" "http://localhost:8080/todos?page=999"   # => 200
```

面白い実録: **tinkerで** `paginate(5, ['*'], 'page', 'abc')` と page に直接 'abc' を
渡すと `TypeError: Unsupported operand types: string - int` で落ちます。
しかし**HTTP経由**の `?page=abc` は200 — Laravelがリクエストのpage値を
`resolveCurrentPage()` で検証し、不正なら1に丸めているからです。
**「フレームワークが入口(HTTP境界)で守ってくれている」**ことの実演であり、
逆に言えば「その守りを迂回すると同じ脆さが顔を出す」教訓でもあります。

### リグレッション確認+ここでコミット

既存CRUD一式が200のままであることを確認してから:

```bash
git add -A && git commit -m "feat: 一覧にページネーションを追加(5件/ページ)"
```
