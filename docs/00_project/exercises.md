# 演習編:考えて書いて、答え合わせする

読むだけ・写すだけでは「自分で書ける」ようにはなりません。この演習編は、
**①問題を読む → ②自分で書く → ③模範解答で答え合わせ → ④解説で深掘り**という
フィードバックループを回すための教材です(希望者は模範解答の代わりに、または加えて
テストで客観チェックもできます)。

> **これはサンプル版(第1弾)です。** 形式を確かめてもらうために代表的な数問だけを
> 収録しています。よい手応えであれば、テーマ・難易度を広げて増やしていきます。

## 📍 位置づけ:テーマごとに「いつやるか」が違う

- **テーマA(PHPの言語ドリル)**: 写経を**始める前でもできます**。
  [PHPキャッチアップ](php-for-polyglots.md)を読んだ直後の腕試しに。
  アプリのコードには依存しません(純粋なPHPだけ)
- **テーマB以降(Laravelの演習)**: **写経(開発トレースガイド)を終えてから**取り組みます。
  実際のアプリのモデルやテスト基盤を使うので、本編を一通りやった人向けです

## 答え合わせの2つのやり方

**① 模範解答(全問に常設・これが基本)**: 各問の `▶ 模範解答` の折りたたみを開くと、
解答+解説+落とし穴が読めます。**まず自分で書いてから**開いてください。

**② テストで客観チェック(任意)**: Laravelの演習には、希望者向けに「答え合わせ用のテスト」も
用意しています。指定の場所にテストを置き `./vendor/bin/sail test` が緑になれば正解
(スプリント6で作ったテスト基盤の活用)。**やらなくてもOK** — 模範解答と見比べるだけでも
十分です。「機械的に○×を知りたい」人向けの追加ルートです。

## 使い方

1. **先に模範解答を開かない**。自分の答えを書いてから開く
2. **手を動かす場所**:
   - テーマA(純PHP): 使い捨てファイル(例: `scratch.php`)や `./vendor/bin/sail tinker`
   - テーマB(Laravel): 実際のアプリのファイルを編集。任意でテストを置いて答え合わせ
3. **詰まったら**: [PHPキャッチアップ](php-for-polyglots.md)(構文)と
   [概念解説集](laravel-concepts.md)(なぜ)を先に見てOK。答えを見るのは最後

難易度: ★(易)/ ★★(中)/ ★★★(難)

---

## テーマA:PHPの言語ドリル

[PHPキャッチアップ](php-for-polyglots.md)で読んだ構文を、実際に書いて定着させます。
各問、まず `scratch.php` に関数を書き、末尾の「答え合わせ」を実行してください。

```bash
# scratch.php を作って書いたら、こう実行する(<関数名>は各問参照)
./vendor/bin/sail php scratch.php
```

### A-1 ★ null合体演算子で「期限なし」を出す

`?string`(文字列またはnull)を受け取り、**nullなら `'(期限なし)'`、あればその文字列を
そのまま返す**関数 `displayDue()` を書いてください。`if` を使わず、null合体演算子 `??`
一発で書けます([キャッチアップ 1-2](php-for-polyglots.md#1-2-null-と-null安全-演算子--))。

```php
<?php
function displayDue(?string $date): string
{
    // ここを書く
}

// 答え合わせ(true が2つ出れば正解)
var_dump(displayDue(null) === '(期限なし)');
var_dump(displayDue('2026-08-01') === '2026-08-01');
```

<details>
<summary>▶ 模範解答</summary>

```php
function displayDue(?string $date): string
{
    return $date ?? '(期限なし)';
}
```

**解説**: `$a ?? $b` は「$aがnullなら$b、そうでなければ$a」。JS/TSの `??` と同じです。
このリポジトリでは [`todos/show.blade.php`](../../resources/views/todos/show.blade.php) の
`{{ $todo->description ?? '(内容はありません)' }}` で実際に使われています。

**落とし穴**: `?:`(短絡三項)と混同しがち。`$a ?: $b` は「$aが**falsy**なら$b」で、
`''`(空文字)や `0` もfalsy扱いになります。**「nullのときだけ」なら必ず `??`** を使いましょう
(空文字を弾きたくない場面で `?:` を使うと事故ります)。
</details>

### A-2 ★★ クロージャの `use` でキーワード絞り込み

文字列の配列と検索語を受け取り、**検索語を含む要素だけの配列**を返す関数
`filterByKeyword()` を書いてください。`array_filter` と**クロージャ**を使い、
外側の `$keyword` は `use` で持ち込みます([キャッチアップ 2-1](php-for-polyglots.md#2-1-無名関数と-アロー関数-fn))。
判定には `str_contains($haystack, $needle)` が使えます。

```php
<?php
function filterByKeyword(array $titles, string $keyword): array
{
    // ここを書く(結果は array_values で添字を振り直すと比較しやすい)
}

// 答え合わせ(true が出れば正解)
$result = filterByKeyword(['牛乳を買う', '部屋の掃除', '牛を見る'], '牛');
var_dump($result === ['牛乳を買う', '牛を見る']);
```

<details>
<summary>▶ 模範解答</summary>

```php
function filterByKeyword(array $titles, string $keyword): array
{
    return array_values(array_filter($titles, function ($title) use ($keyword) {
        return str_contains($title, $keyword);
    }));
}
```

短く書くならアロー関数 `fn`(外の変数を自動で取り込むので `use` 不要):

```php
function filterByKeyword(array $titles, string $keyword): array
{
    return array_values(array_filter($titles, fn ($t) => str_contains($t, $keyword)));
}
```

**解説**: PHPの通常のクロージャは、外側の変数を**自動では見ません**。
`use ($keyword)` と書いて初めて中で使えます(Python/JSが自動で閉じ込めるのと違う)。
一方 `fn () => ...` は自動取込なので、JSのアロー関数に近い感覚です。
`array_values` を付けるのは、`array_filter` が**元のキー(添字)を保持する**ため。
`[0=>'牛乳を買う', 2=>'牛を見る']` のように歯抜けになるのを、0,1,2…へ振り直しています。

**このリポジトリでの類例**: [`TodoController::index()`](../../app/Http/Controllers/TodoController.php)
のキーワード絞り込みが、まさに `use ($keyword)` のクロージャです
(ただし本物はDB検索なので `array_filter` ではなく Eloquent の `where` を使います →
[SQLインジェクション](laravel-concepts.md#32-sqlインジェクションとプレースホルダ))。
</details>

---

## テーマB:Laravel(写経を終えた人向け)

> ⚠️ **このテーマは[開発トレースガイド](dev-walkthrough.md)を一通りやってから**取り組んでください。
> 実際のアプリのモデルやテスト基盤を使います。

ここからは実際のアプリのコードを書きます。答え合わせは**模範解答(折りたたみ)を主**とし、
「客観的に○×を確かめたい人向け」に**任意でテスト**も用意しています。

### B-1 ★★ モデルに「もうすぐ期限」判定を追加する

`Todo` モデルに、**期限が近いか**を返すメソッド `isDueSoon(): bool` を追加してください。
仕様は次のとおり:

- 期限(`due_date`)が設定されていて、
- **未完了**で、
- 期限が **今日から3日以内(今日・明日…3日後まで)** なら `true`
- それ以外は `false`(期限なし・完了済み・4日以上先・過去 はすべて `false`)

**手を動かす場所**: [`app/Models/Todo.php`](../../app/Models/Todo.php)。
既にある `isOverdue()`(期限切れ判定)がそっくりのお手本です。まず自分で書いてみましょう。

**期待する振る舞い**(頭の中で・または紙で答え合わせ):

| 入力 | 期待 |
|---|---|
| 2日後・未完了 | `true` |
| 昨日・未完了(過去) | `false` |
| 10日後・未完了 | `false` |
| 明日・完了済み | `false` |
| 期限なし | `false` |

<details>
<summary>▶ 模範解答</summary>

`app/Models/Todo.php` の `isOverdue()` の隣に追加:

```php
/**
 * もうすぐ期限か?(期限があり・未完了で・今日から3日以内)
 */
public function isDueSoon(): bool
{
    return $this->due_date !== null
        && ! $this->completed
        && $this->due_date->between(today(), today()->addDays(3));
}
```

**解説**:
- `due_date` はモデルの `casts()` で `date` 指定なので、**Carbon(日付オブジェクト)**として
  扱えます。だから `->between(...)` や `->addDays(3)` が使えます
  (→ [casts](laravel-concepts.md#16-casts型キャスト))
- `between($from, $to)` は Carbon の範囲判定で、**両端を含みます**。
  だから「今日〜3日後」に今日・3日後ちょうども含まれます
- 判定を**モデルのメソッドに置く**のがポイント。ビューやコントローラにベタ書きすると
  同じ基準が散らばって事故ります(`isOverdue()` と同じ設計。→ [DRY](laravel-concepts.md#29-dry))

**落とし穴**:
- 条件の順番。`$this->due_date !== null` を**先に**書かないと、nullに対して
  `->between()` を呼んで `Call to a member function ... on null` になります
  (短絡評価: 左がfalseなら右は評価されない、を利用している)
- 「過去(期限切れ)」も3日以内では?と迷いますが、`between(today(), ...)` は
  今日より前を含まないので、昨日は自動的に `false`。これで「もうすぐ=これから来る」に絞れます

**発展**: これを一覧で使うなら、[`todos/index.blade.php`](../../resources/views/todos/index.blade.php)
で `@if ($todo->isDueSoon())` を足し、CSSで色を付けてみましょう(→ 演習C系で扱う予定)。
</details>

<details>
<summary>▶ 任意:テストで客観チェックしたい人へ</summary>

「機械的に○×を確かめたい」場合は、次のテストを置いて緑にできれば正解です
(やらなくてもOK。上の模範解答と見比べるだけでも十分)。

```php
// tests/Unit/TodoDueSoonTest.php を新規作成
<?php

namespace Tests\Unit;

use App\Models\Todo;
use Tests\TestCase;   // dateキャストにアプリ起動が要るので Tests\TestCase を継承

class TodoDueSoonTest extends TestCase
{
    public function test_within_3_days_and_incomplete_is_due_soon(): void
    {
        $todo = new Todo(['due_date' => today()->addDays(2), 'completed' => false]);
        $this->assertTrue($todo->isDueSoon());
    }

    public function test_past_due_is_not_due_soon(): void
    {
        $todo = new Todo(['due_date' => today()->subDay(), 'completed' => false]);
        $this->assertFalse($todo->isDueSoon());
    }

    public function test_far_future_is_not_due_soon(): void
    {
        $todo = new Todo(['due_date' => today()->addDays(10), 'completed' => false]);
        $this->assertFalse($todo->isDueSoon());
    }

    public function test_completed_is_not_due_soon(): void
    {
        $todo = new Todo(['due_date' => today()->addDay(), 'completed' => true]);
        $this->assertFalse($todo->isDueSoon());
    }

    public function test_no_due_date_is_not_due_soon(): void
    {
        $todo = new Todo(['due_date' => null, 'completed' => false]);
        $this->assertFalse($todo->isDueSoon());
    }
}
```

```bash
# 実装前に流すと「赤」(まだ isDueSoon が無い)→ 実装して「緑」にする
./vendor/bin/sail test --filter=TodoDueSoonTest
```

> 演習用に作ったこのテストは、本体リポジトリには含めません(自分の手元だけで使う想定)。
</details>

---

## この先の演習(予定)

第1弾はここまでです。手応え次第で、次のような演習を足していきます:

- **テーマC(小さな機能追加)**: 「TODOに優先度(priority)を追加せよ」を、
  マイグレーション→モデル→バリデーション→表示→テストまで一気通貫で。
  第2部で学んだ「追加開発の型」の総合演習(模範解答は参考ブランチで提示)
- **テーマD(認可・テスト)**: 「他人のTODOを完了にできてしまうバグ」を仕込んだ状態から、
  Policyとテストで塞ぐ(バグ修正の追体験)
- **難易度別のヒント段階化**(ヒント1→ヒント2→模範解答)

## 設計メモ(この演習編の方針)

次弾を増やすときの指針として、合意した設計を残しておきます。

- **位置づけは役割で分ける**: テーマA(純PHPドリル)は写経の**前**でもできる自己完結型。
  テーマB以降(Laravel)は写経を**終えた人向け**で、実アプリ+テスト基盤を活かす
- **答え合わせは模範解答が主・テストは任意**: 折りたたみ模範解答+解説を全問に常設し、
  テストでの客観チェックは「やりたい人向け」の追加ルートに留める(テスト環境を前提にしない)
- **各問に**: 難易度(★)・落とし穴・別解・(前段階教材なら)キャッチアップへの逆リンクを付ける
