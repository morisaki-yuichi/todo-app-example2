# スプリント6 レビュー記録(自動テストとCI)

- 実施日: 2026-07-04
- スプリントゴール: **CRUD・絞り込みの主要な振る舞いをFeatureテストで自動検証でき、
  PRを出すとGitHub Actionsがそのテストを自動実行する**
- 判定: **達成** ✅

## 確認した「動くもの」(マージ後mainで再確認)

| 確認 | 結果 |
|---|---|
| `sail test`(ローカル) | **24テスト・56アサーション 全緑** |
| Featureテストの範囲 | 一覧・詳細・作成・更新・トグル・削除・絞り込み・期限切れ・不正値 |
| Unitテスト | isOverdue()の4境界(過去日/完了/期限なし/今日) |
| CI(GitHub Actions) | PR #7で緑。MySQLサービス+artisan test |
| テストの分離 | RefreshDatabaseはtesting DBに作用、開発DB(todo_app)は無傷 |

## DoD判定

| PBI | 判定 | 根拠 |
|---|---|---|
| #18 Featureテスト+ファクトリ | ✅ | CRUD全操作のテストがローカルで緑(24本) |
| #19 GitHub Actions CI | ✅ | PRでテストが自動実行され、Checksに結果が出る |

## 発生したトラブル・実験の記録

### 実験6-A: red→green(Try T-12)

- わざと `assertTrue($todo->completed)` で赤(`false is true`)→ `assertFalse` で緑
- 学び: **一度赤を見ないと、そのテストが本当に検証しているか分からない**

### 実験6-B: CIが赤くなるのを見て直す

- **症状**: ローカルは緑なのに初回CIが赤
- **調査**: `gh run view --log-failed` の1行目 = `Test directory "tests/Unit" not found`
- **原因**: ExampleTest削除で `tests/Unit` が空になり、**gitが空ディレクトリを
  追跡しない**ためCIのチェックアウトに存在しなかった
- **解決**: isOverdue()のUnitテストでディレクトリを実体化(.gitkeepより有意義)
- **学び**: 「手元では通る」は環境差で崩れる。**CIはまっさら環境で再現し
  隠れた依存を暴く** — これがCIの価値そのもの

### 実録トラブル: assertDontSeeの部分文字列誤反応

- 「未完了タスク」は「完了タスク」を部分文字列として含むため、
  `assertDontSee('完了タスク')` が誤反応
- 対処: テストデータは部分文字列で重ならない語(牛乳を買う/部屋の掃除)にする

### 発見: 既定ExampleTestがスプリント4の変更で失敗していた

- `/` に200を期待する自動生成テストが、`/`→302リダイレクト化で失敗
- 「意味を失った自動生成テスト」を消し、302を検証するテストに置換

### 発見: UnitテストもLaravel起動が要ることがある

- 素の `PHPUnit\Framework\TestCase` だとdateキャストが `connection() on null` で落ちた
- モデルのキャストはアプリ起動を前提とするため、`Tests\TestCase` を継承して解決

## 成果物リンク

- [PR #7](https://github.com/morisaki-yuichi/todo-app-example2/pull/7)
- [トレースガイド スプリント6](../00_project/dev-walkthrough.md#スプリント6-自動テストとci) /
  [概念解説集 34〜36](../00_project/laravel-concepts.md#第2部スプリント6で登場した概念)

## 次スプリントへの申し送り

- スプリント7(認証)へ。**この24テストが認証改修の安全網**になる。
  認証導入で既存テストが赤くなったら、それは「未ログインで弾くようになった」
  という設計変更の現れ — テストも一緒に更新する(テストは仕様の写し鏡)
- 認証は全ページに影響するので、まず「どのテストが赤くなるか」を予想してから着手する
