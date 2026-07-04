# 演習編:考えて書いて、答え合わせする

読むだけ・写すだけでは「自分で書ける」ようにはなりません。この演習編は、
**①問題を読む → ②自分で書く → ③答え合わせ → ④模範解答 → ⑤解説**という
フィードバックループを回すための教材です。

> **これはサンプル版(第1弾)です。** 形式を確かめてもらうために代表的な数問だけを
> 収録しています。よい手応えであれば、テーマ・難易度を広げて増やしていきます。

## 使い方(大事)

1. **先に模範解答を開かない**。模範解答は `▶ 模範解答` の折りたたみの中にあります。
   自分の答えを書いてから開いてください
2. **手を動かす場所**:
   - 純粋なPHPのドリルは、使い捨てファイル(例: `scratch.php`)や
     `./vendor/bin/sail tinker` で試せます
   - Laravelの演習は、**答え合わせ用のテスト**を用意しています。
     指定の場所にテストを置き、`./vendor/bin/sail test` が緑になれば正解です
     (スプリント6で作ったテスト基盤=フィードバックの装置)
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

## テーマB:Laravel(テストで答え合わせ)

ここからは実際のアプリのコードを書きます。**答え合わせはテスト**です。
提示するテストを置いて、`./vendor/bin/sail test` が緑になれば正解。
まさにスプリント6以降で使ってきた「赤→自分のコード→緑」のループです。

### B-1 ★★ モデルに「もうすぐ期限」判定を追加する

`Todo` モデルに、**期限が近いか**を返すメソッド `isDueSoon(): bool` を追加してください。
仕様は次のとおり:

- 期限(`due_date`)が設定されていて、
- **未完了**で、
- 期限が **今日から3日以内(今日・明日…3日後まで)** なら `true`
- それ以外は `false`(期限なし・完了済み・4日以上先・過去 はすべて `false`)

既にある [`isOverdue()`](../../app/Models/Todo.php)(期限切れ判定)が良いお手本です。
まず**答え合わせ用のテスト**を作ります:

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
# まず「赤」を見る(まだ isDueSoon が無いので失敗する)
./vendor/bin/sail test --filter=TodoDueSoonTest
# → メソッドを実装して「緑」にする
```

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

---

## この先の演習(予定)

第1弾はここまでです。手応え次第で、次のような演習を足していきます:

- **テーマC(小さな機能追加)**: 「TODOに優先度(priority)を追加せよ」を、
  マイグレーション→モデル→バリデーション→表示→テストまで一気通貫で。
  第2部で学んだ「追加開発の型」の総合演習(模範解答は参考ブランチで提示)
- **テーマD(認可・テスト)**: 「他人のTODOを完了にできてしまうバグ」を仕込んだ状態から、
  Policyとテストで塞ぐ(バグ修正の追体験)
- **難易度別のヒント段階化**(ヒント1→ヒント2→模範解答)

## フィードバックの観点(あなたへ)

このサンプルを試したら、次を教えてください。次弾の設計に反映します。

- 難易度の刻みは適切か(易すぎ/難しすぎ)
- 「テストで答え合わせ」は手応えとして良いか(それとも `<details>` 解答だけで十分か)
- 純PHPドリルとLaravel演習の比率
- 1テーマあたりの問題数
