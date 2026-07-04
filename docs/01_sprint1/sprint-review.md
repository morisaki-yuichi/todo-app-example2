# スプリント1 レビュー記録

- 実施日: 2026-07-04
- スプリントゴール: **クローンした写経者がREADMEの手順だけで `http://localhost:8080` に
  Laravelの初期画面を表示できる状態にする**
- 判定: **達成** ✅

## 確認した「動くもの」

| 確認内容 | 結果 |
|---|---|
| main上で `sail up -d` → `GET http://localhost:8080/` | 200、タイトル「TODO App」 |
| `sail ps` | laravel.test / mysql(healthy)がUp、コンテナ名は `todo-app-example2-*` |
| `sail artisan migrate` | MySQLの `todo_app` データベースに対して成功 |
| **クローン再現テスト**: 別ディレクトリに `git clone` し、READMEの手順を上から実行(ポートは8081/5181/3307に変更) | 200、タイトル「TODO App」 ✅ |

## DoD判定

| PBI | 判定 | 根拠 |
|---|---|---|
| #1 Sail環境構築と.env整備 | ✅ | 上記の起動確認。`.env.example` 整備済み(PR #1, #2) |
| #2 README | ✅ | クローン再現テストで手順どおりの起動を実証 |
| #3 GitHub運用開始 | ✅ | パブリックリポジトリ、PR #1/#2 をマージ、ブランチ戦略はqa-logに記録 |

タスクT1〜T10もすべて完了(バックログ: [sprint-backlog.md](sprint-backlog.md))。

## 発生したトラブルと調査・解決の過程(教材の核心)

> 調査の型: **①エラーメッセージの1行目から読む → ②データの通り道を外側から1段ずつ →
> ③思い込みではなく実データで検証**

### トラブル1: `sail:install` が失敗(There are no commands defined in the "sail" namespace.)

- **症状**: `laravel new` 直後の `php artisan sail:install` がエラー
- **調査**: ①1行目 =「sailというコマンド名前空間がない」→ ③思い込み(「sailは同梱のはず」)を
  実データで検証: `composer.json` の `require-dev` を確認 → **`laravel/sail` が入っていない**
- **原因**: 現行のLaravelインストーラはSailを同梱しなくなっていた(仕様変更)
- **解決**: `composer require laravel/sail --dev` を追加してから `sail:install` → 成功
- **学び**: 「昔はこうだった」はあてにならない。エラーが指すものを実ファイルで確認する

### トラブル2: `.env` 変更直後に curl が `000`(接続リセット)

- **症状**: わざと失敗実験でAPP_KEYを空にした直後、500ではなく `000` が返った
- **調査**: ②通り道の一番外側=「そもそも繋がっているか」の問題。`sail ps` はUp。
  直後に再実行すると正常に応答 → タイミングの問題と推定
- **原因**: 開発サーバが `.env` の変更を検知して自動再起動しており、その瞬間に接続した
- **解決**: `.env` 変更後は数秒待ってからアクセスする(実験手順にも待ちを明記)

### トラブル3: マージ後の動作確認で500、しかし `laravel.log` に新しいエラーがない

- **症状**: PR #1マージ後の確認で500。`storage/logs/laravel.log` には古いエラーしかない
- **調査**: ②アプリのログに出ない=**アプリに到達する前**の問題。1段外側の
  コンテナログを確認: `sail logs laravel.test` →
  `Failed opening required '/index.php'`(開発サーバが壊れたパスを参照)
- **原因**: 実験での `.env` 連続書き換え等により、開発サーバのワーカープロセスの
  状態が壊れたと推定(アプリのコードは無関係)
- **解決**: `sail restart` でプロセスを作り直し → 200。READMEとトレースガイドの
  エラー表に「ログに出ない500はコンテナログを見る」を追記
- **学び**: **ログが「ない」ことも手がかり**。どの層のログに出るかで原因の層が絞れる

### トラブル4: クローン再現テストでMySQLの3306が衝突

- **症状**: 再現テスト環境の `sail up -d` が `Bind for 0.0.0.0:3306 failed: port is already allocated`
- **調査**: ①1行目に「3306が確保済み」→ 本体プロジェクトのMySQLが3306を公開していた
- **原因**: `APP_PORT`/`VITE_PORT` はケアしていたが、**MySQLのホスト側ポート
  (`FORWARD_DB_PORT`)を見落としていた**
- **解決**: `FORWARD_DB_PORT` を `.env.example` とREADMEに明示
  ([PR #2](https://github.com/morisaki-yuichi/todo-app-example2/pull/2))
- **学び**: 再現テストは「やったつもり」を潰す。**レビューで実際に欠陥が見つかった**ので、
  この工程はスプリント2以降も継続する

## 成果物リンク

- [PR #1: Sail環境構築とセットアップ手順の整備](https://github.com/morisaki-yuichi/todo-app-example2/pull/1)
- [PR #2: FORWARD_DB_PORTの明示](https://github.com/morisaki-yuichi/todo-app-example2/pull/2)
- [開発トレースガイド スプリント1](../00_project/dev-walkthrough.md#スプリント1-環境構築と土台) /
  [概念解説集(1〜10)](../00_project/laravel-concepts.md)

## 次スプリントへの申し送り

- PBI #4〜#7(todosテーブル・モデル・シーダー・一覧・詳細)に着手する
- 積み残しなし
