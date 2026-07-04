# TODO App(Laravel Sail 教材プロジェクト)

Laravel Sail 上で動く、CRUD機能を持つTODOアプリです。
**初級エンジニアがスクラム開発を写経・追体験するための教材**として、
アプリ本体と開発過程のドキュメント一式をセットで管理しています。

## 教材ドキュメント

| ドキュメント | 内容 |
|---|---|
| [開発トレースガイド](docs/00_project/dev-walkthrough.md) | コミット単位で開発を追体験するためのガイド(写経の本編) |
| [概念解説集](docs/00_project/laravel-concepts.md) | 開発に登場した概念の「一言定義→なぜ必要か→実例」集 |
| [プロダクトロードマップ](docs/00_project/roadmap.md) | 全4スプリントの計画 |
| [Q&A記録](docs/00_project/qa-log.md) | 仕様決定の経緯 |

## 技術スタック

- Docker + [Laravel Sail](https://laravel.com/docs/sail)(Laravel公式の開発環境)
- Laravel 13 / PHP 8.4
- Blade + Plain HTML/CSS(JS・CSSフレームワーク不使用)
- MySQL 8.4

## 動作要件

- Docker(Docker Compose v2 以降を含む)
- Git

PHP や Composer をホストマシンにインストールする必要は**ありません**(すべてコンテナ内で動きます)。

## セットアップ手順(クローン直後から)

### 1. クローンして設定ファイルを用意する

```bash
git clone https://github.com/morisaki-yuichi/todo-app-example2.git
cd todo-app-example2
cp .env.example .env
```

> **ポートについて**: このプロジェクトは `.env` の `APP_PORT=8080` / `VITE_PORT=5180` で
> ホスト側ポートを明示しています。手元で既に使用中の場合は、`.env` の値を空いている
> ポートに変更してください(空き確認: `ss -tlnp | grep 8080`)。
> `COMPOSE_PROJECT_NAME` は、コンテナ名が他プロジェクトと衝突しないための設定です。

### 2. 依存パッケージをインストールする(鶏と卵問題の解決)

Sailのコマンド本体は `vendor/bin/sail` にあるため、クローン直後は
「`sail` を使うには `vendor/` が必要、`vendor/` を作るには Composer が必要」という
鶏と卵の状態です。[Laravel公式の解決手順](https://laravel.com/docs/sail#installing-composer-dependencies-for-existing-projects)どおり、
**Composer入りの使い捨てコンテナ**で最初の1回だけインストールします。

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs
```

### 3. 起動してアプリキーを発行する

```bash
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
```

初回はDockerイメージのビルド・取得で数分かかります。
`sail up -d` の `-d` はバックグラウンド起動(detached)の意味です。

### 4. データベースを準備する

```bash
./vendor/bin/sail artisan migrate
```

> 初回起動直後はMySQLの初期化が終わっておらず、接続エラーになることがあります。
> その場合は30秒ほど待ってから再実行してください。

### 5. ブラウザで確認する

http://localhost:8080 を開き、Laravelのウェルカム画面
(タブのタイトルが「TODO App」)が表示されれば成功です。

## よく使うコマンド

このプロジェクトでは artisan 等のコマンドをすべて `./vendor/bin/sail` 経由で統一しています
(ホストではなく**コンテナの中で**実行するため)。

```bash
./vendor/bin/sail up -d      # 起動
./vendor/bin/sail stop       # 停止(データは保持される)
./vendor/bin/sail ps         # コンテナの状態確認
./vendor/bin/sail artisan …  # artisanコマンド
./vendor/bin/sail tinker     # 対話シェル(REPL)
./vendor/bin/sail logs       # ログ確認
```

## トラブルシューティング

| 症状 | 原因と対処 |
|---|---|
| `port is already allocated` | ホスト側ポートの衝突。`.env` の `APP_PORT` 等を空きポートに変更して `sail up -d` し直す |
| `Connection refused`(migrate時) | MySQL初期化中。少し待って再実行 |
| 500エラー + ログに `No application encryption key` | `sail artisan key:generate` を実行していない |
| `.env` 変更直後だけ接続が切れる | 開発サーバが `.env` の変更を検知して自動再起動している。数秒待って再アクセス |
| ポート等の変更が反映されない | `APP_PORT` など compose.yaml が参照する値は、コンテナ起動時に読まれる。`sail up -d` をやり直す |

その他の開発中のトラブルは各スプリントのレビュー記録
(`docs/0X_sprintX/sprint-review.md`)に調査過程つきで記録しています。
