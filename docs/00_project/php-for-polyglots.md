# 他言語経験者のためのPHP・Laravelクイックキャッチアップ

このドキュメントは、**PythonかJavaScriptの経験がある人が、このリポジトリのコードを
読めるようになる**ための最短ルートです。PHPを一から学ぶのではなく、
「他言語で知っている概念が、PHPだとどう書かれるか」の**対応表**として使ってください。

- 各項目は **PHP / Python / JavaScript** の3列で対比します
- 型に関わる箇所は、素のJSにない概念なので **TypeScript(TS)注記** を添えます
- フレームワークの概念(Eloquent等)は Ruby on Rails 由来なので、
  **Rails経験者向けの一言**を必要に応じて添えます(🚂 マーク)

> このリポジトリでは `match` 式・enum・非同期などは使っていないため、ここでは扱いません。
> 「実際に使っている機能」だけに絞っています。

## 📍 この文書の位置づけ:写経を「始める前」の入口

これは**写経(開発トレースガイド)を始める前**に読む、独立した入口教材です。

- **前提知識は要りません**。アプリをまだ作っていなくても読めます。載っているコードは
  すべて**それ単体で意味が分かる説明用スニペット**で、本編を終えている必要はありません
- 本文中の 🔎 **本編での登場** は「この構文を、写経のどこで実際に書くか」の**予告**です。
  今は開かなくてOK。写経中に「あの記号だ」と思い出す索引として使ってください
- [概念解説集](laravel-concepts.md) へのリンクは「もっと深く知りたい人向け」の**任意の寄り道**です

読み終えたら、[開発トレースガイド](dev-walkthrough.md)のスプリント1から写経を始めましょう。
この文書は**構文の読み方**、トレースガイドは**作り方**、概念解説集は**なぜそうするか**を担当します。

---

## 0. まず1分:第一印象の対応表

TodoControllerの一部を例に、記号の意味を一望します。

```php
namespace App\Http\Controllers;      // このファイルの所属(名前空間)

use App\Models\Todo;                 // 他のクラスを読み込む(import)

class TodoController extends Controller
{
    public function show(Todo $todo)  // 引数 $todo は Todo型
    {
        $this->authorize('view', $todo);   // $this = 自分自身のインスタンス
        return view('todos.show', ['todo' => $todo]);
    }
}
```

| 見た目 | PHPでの意味 | Python | JavaScript |
|---|---|---|---|
| `$todo` | 変数には必ず `$` を付ける | `todo` | `todo` / `const todo` |
| `->` | インスタンスのメソッド/プロパティ呼び出し | `.` | `.` |
| `::` | 静的メソッド/定数(クラス自体に対して) | `.`(クラス経由) | `.`(クラス経由) |
| `$this` | 自分自身のインスタンス | `self`(第1引数) | `this` |
| `namespace` / `use` | 所属の宣言 / 読み込み | `import` | `import` |
| `;` `{ }` | 文末セミコロン・波括弧ブロック | なし(インデント) | あり |
| `Todo $todo` | 引数の型宣言 | 型ヒント `todo: Todo` | なし(TSなら `todo: Todo`) |

**最重要**: PHPでは**変数に必ず `$`**、**メソッド呼び出しは `->`**。この2つに慣れれば、
コードの8割は読めるようになります。

---

## 1. 変数・型・null

### 1-1. 型宣言(引数・戻り値・プロパティ)

このリポジトリは型を積極的に書きます。PythonやTSの型ヒントに近いですが、
PHPの型は**実行時にもチェックされる**(違反すると例外)点が特徴です。

```php
public function view(User $user, Todo $todo): bool   // 引数2つに型、戻り値はbool
{
    return $user->id === $todo->user_id;
}
```

| PHP | Python | JavaScript / TS |
|---|---|---|
| `function view(User $user): bool` | `def view(user: User) -> bool:` | JS: 型なし / **TS**: `view(user: User): boolean` |
| `?string`(stringかnull) | `Optional[str]` / `str \| None` | **TS**: `string \| null` |
| `protected int $id;`(型付きプロパティ) | `id: int`(dataclass等) | **TS**: `id: number` |

- 🔎 本編での登場: [`TodoPolicy`](../../app/Policies/TodoPolicy.php) の全メソッド、
  [`StoreTodoRequest::authorize(): bool`](../../app/Http/Requests/StoreTodoRequest.php)
- 🚂 Ruby経験者へ: Rubyは型を書かないので、ここはむしろTS/Pythonの型ヒント感覚が近いです

### 1-2. null と「null安全」演算子 `?->` `??`

PHPの `null` は Pythonの `None`、JSの `null` に相当します。
このリポジトリでよく出る2つの演算子は、**JSと綴りまで同じ**です。

```php
$todo->due_date?->format('Y-m-d') ?? '(期限なし)'
```

これは「`due_date` が null でなければ `format(...)` を呼ぶ。結果が null なら
`'(期限なし)'` を使う」という意味です。

| PHP | Python | JavaScript |
|---|---|---|
| `$x?->foo()`(nullなら呼ばずnull) | `x.foo() if x else None` | `x?.foo()`(**同じ**) |
| `$a ?? $b`(aがnullならb) | `a if a is not None else b` | `a ?? b`(**同じ**) |

- 🔎 本編での登場: [`todos/show.blade.php`](../../resources/views/todos/show.blade.php) の期限日表示
- なぜ null安全が事故を防ぐかは [ルートモデルバインディングと404](laravel-concepts.md#20-ルートモデルバインディングと404) も参照

---

## 2. 関数・クロージャ(無名関数)

### 2-1. 無名関数と「アロー関数 `fn`」

PHPには2種類の無名関数があります。`function () {...}` と、短い `fn () => ...` です。

```php
// 通常のクロージャ:外の変数を使うには use ($keyword) で明示的に持ち込む
$query->where(function ($q) use ($keyword) {
    $q->where('title', 'like', "%{$keyword}%");
});

// アロー関数:外の変数を自動で取り込む(1式のみ)
$this->state(fn (array $attributes) => ['completed' => true]);
```

| PHP | Python | JavaScript |
|---|---|---|
| `function ($x) use ($y) { return $x + $y; }` | `lambda x: x + y` | `(x) => x + y` |
| `fn ($x) => $x + $y`(外の変数は自動取込) | `lambda x: x + y` | `(x) => x + y`(**近い**) |

**ここがPHP独特**: 通常のクロージャは、外側の変数を**自動では見ません**。
`use ($keyword)` と書いて初めて中で使えます。Python/JSが外のスコープを自動で
閉じ込める(クロージャ)のと違い、PHPは**明示的**です。
一方 `fn`(アロー関数)は自動で取り込むので、JSのアロー関数に近い感覚です。

- 🔎 本編での登場: [`TodoController::index()`](../../app/Http/Controllers/TodoController.php) の絞り込みクロージャ、
  [`UserFactory`](../../database/factories/UserFactory.php) の `fn`

### 2-2. 名前付き引数・配列での引数

このリポジトリでは、設定を**連想配列**で渡すスタイルが多用されます
(次章の配列を参照)。例: `$request->validate([...])`、`view('todos.show', ['todo' => $todo])`。
Pythonのキーワード引数やJSのオブジェクト引数に相当する役割を、PHPは連想配列で担うことが多いです。

---

## 3. 配列(PHPの主役)

PHPの「配列」は、**リストと辞書(dict/object)が一体化**した型です。
`['a', 'b']`(添字配列)も `['title' => '...']`(連想配列)も同じ `array`。

```php
return [
    'title' => ['required', 'string', 'max:100'],   // キー => 値
    'description' => ['nullable', 'string'],
];
```

| PHP | Python | JavaScript |
|---|---|---|
| `['a', 'b']`(添字配列) | `['a', 'b']`(list) | `['a', 'b']`(Array) |
| `['k' => 'v']`(連想配列) | `{'k': 'v'}`(dict) | `{ k: 'v' }`(object) |
| `=>`(キーと値の区切り) | `:` | `:` |
| `$arr['k']` | `arr['k']` | `arr.k` / `arr['k']` |
| `[$a, $b] = $pair;`(分解) | `a, b = pair` | `const [a, b] = pair` |

**混乱ポイント**: Python/JSは「リスト」と「辞書/オブジェクト」が別の型ですが、
**PHPはどちらも `array`**。`=>` が出てきたら「辞書的な使い方」だと思ってください。

- 🔎 本編での登場: [`StoreTodoRequest::rules()`](../../app/Http/Requests/StoreTodoRequest.php) の返り値

---

## 4. クラスとオブジェクト指向

### 4-1. 名前空間とインポート(`namespace` / `use`)

PHPの `namespace` はディレクトリ構造に対応し(`App\Http\Controllers` ≒ `app/Http/Controllers/`)、
`use` で他のクラスを読み込みます。

```php
namespace App\Http\Controllers;   // このファイルの住所
use App\Models\Todo;              // Todoクラスを読み込む
```

| PHP | Python | JavaScript |
|---|---|---|
| `namespace App\Http\Controllers;` | (ディレクトリ=パッケージ) | (ファイル=モジュール) |
| `use App\Models\Todo;` | `from app.models import Todo` | `import { Todo } from '...'` |
| `\`(名前空間の区切り) | `.` | `/`(パス) |

### 4-2. `$this`・可視性・静的呼び出し `::`

```php
class TodoController extends Controller
{
    public function show(Todo $todo)      // public/protected/private = 可視性
    {
        $this->authorize('view', $todo);  // $this = 自分自身
    }
}

Todo::factory()->create();                // :: = クラスに対する静的呼び出し
```

| PHP | Python | JavaScript |
|---|---|---|
| `$this->foo()` | `self.foo()` | `this.foo()` |
| `public` / `protected` / `private` | 慣習(`_name`) | `#private` など |
| `Todo::factory()`(静的) | `Todo.factory()` | `Todo.factory()` |
| `extends Controller` | `class X(Controller):` | `extends Controller` |

**ポイント**: `->` は「インスタンスに対して」、`::` は「クラス自体に対して(静的)」。
`$todo->title`(このTODOのタイトル)と `Todo::factory()`(Todoクラスの工場)を見分けられれば十分です。

### 4-3. トレイト(`use` のもう一つの意味)

⚠️ **`use` はPHPで2つの全く違う意味を持ちます。** これは他言語経験者が必ず戸惑う点です。

```php
use App\Models\Todo;      // (A) ファイル冒頭:クラスのインポート

class Todo extends Model
{
    use HasFactory;       // (B) クラスの中:トレイトの取り込み(ミックスイン)
}
```

- **(A) ファイルの一番上の `use`** = 他のクラスを読み込む(import)
- **(B) クラスの中の `use`** = **トレイト**を混ぜ込む。トレイトは「メソッドの部分集合を
  複数クラスで共有する」仕組み(多重継承の代わり)

| PHP | Python | JavaScript |
|---|---|---|
| トレイト `use HasFactory;` | ミックスイン(多重継承 `class X(A, B)`) | ミックスイン(関数で合成) |

🚂 Ruby経験者へ: トレイトは **`include Module`(Rubyのモジュールのmixin)とほぼ同じ**です。

- 🔎 本編での登場: [`Todo`](../../app/Models/Todo.php) の `use HasFactory;`、
  テストの `use RefreshDatabase;`

### 4-4. 属性(アノテーション)`#[...]`

`#[Fillable([...])]` のような `#[...]` は **属性(Attribute)**。クラスやプロパティに
メタ情報を付けます。

```php
#[Fillable(['name', 'email', 'password'])]
class User extends Authenticatable { /* ... */ }
```

| PHP | Python | JavaScript |
|---|---|---|
| `#[Fillable([...])]` | **デコレータ** `@fillable(...)` | **デコレータ** `@fillable()`(TS/実験的) |

**Python経験者に一番刺さる対応**: `#[Attr]` は**デコレータ `@decorator` とほぼ同じ位置づけ**です
(構文は違いますが「宣言に付ける印」という役割が同じ)。

- 🔎 本編での登場: [`User`](../../app/Models/User.php) の `#[Fillable]` / `#[Hidden]`
- ※ このリポジトリの `Todo` モデルは、属性ではなく `protected $fillable = [...]` という
  **プロパティ形式**も使っています。どちらも同じ「一括代入の許可リスト」で、書き方の違いです
  (→ [$fillableとマスアサインメント](laravel-concepts.md#15-fillableとマスアサインメント))

---

## 5. 文字列

```php
"%{$keyword}%"                        // ダブルクォートは変数を展開する
'そのまま {$x} は展開されない'         // シングルクォートは展開しない
'ようこそ、' . $user->name . 'さん'    // . が文字列連結(+ ではない)
```

| PHP | Python | JavaScript |
|---|---|---|
| `.`(連結) | `+` | `+` |
| `"Hi {$name}"`(展開) | `f"Hi {name}"` | `` `Hi ${name}` `` |
| `'...'`(展開しない) | `'...'` / `"..."` | `'...'` / `"..."` |

**要注意**: PHPの文字列連結は `+` ではなく **`.`**。`+` は数値の足し算専用です
(JS/Pythonの `+` 連結の癖が事故になりやすい)。

---

## 6. Blade テンプレートの構文

ビュー(`.blade.php`)はPHPを埋め込んだHTMLです。`{{ }}` と `@ディレクティブ` を覚えれば読めます。

```blade
@if ($todos->isEmpty())
    <p>TODOがありません。</p>
@else
    @foreach ($todos as $todo)
        <li>{{ $todo->title }}</li>   {{-- {{ }} は「自動エスケープして出力」 --}}
    @endforeach
@endif

@auth {{ auth()->user()->name }} @endauth   {{-- ログイン中だけ表示 --}}
```

| Blade | Python(Jinja2) | JavaScript(JSX等) |
|---|---|---|
| `{{ $x }}`(自動エスケープ出力) | `{{ x }}` | `{x}` |
| `@if / @foreach / @endif` | `{% if %} / {% endif %}` | `{cond && ...}` / `.map()` |
| `{{-- コメント --}}` | `{# #}` | `{/* */}` |

🚂 Rails経験者へ: Bladeは **ERB(`.erb`)にほぼ相当**します。`{{ }}` が `<%= %>`、
`@if` が `<% if %>` です。`{{ }}` の自動エスケープは Rails の `<%= %>` と同じ発想
(→ [Blade と XSS対策](laravel-concepts.md#19-bladeテンプレートextendsyield--のxss対策))。

---

## 7. Laravelで戸惑う「イディオム」

言語そのものではないが、フレームワーク特有で初見だと魔法に見える書き方をまとめます。

### 7-1. ヘルパ関数とファサード

`auth()`、`config('app.name')`、`view(...)`、`redirect()` のような**グローバル関数**が
どこからでも使えます。「importしていないのに呼べる」のはLaravelが用意しているためです。

| 書き方 | 意味 |
|---|---|
| `auth()->user()` | 現在ログイン中のユーザー |
| `config('app.name')` | 設定値の取得(→ [設定の2段構え](laravel-concepts.md#28-設定の2段構え環境変数configアプリ)) |
| `redirect()->route('todos.index')` | 名前付きルートへリダイレクト |

### 7-2. メソッドチェーン

`$query->where(...)->orderByDesc(...)->paginate(5)` のように**点々とつなぐ**書き方。
各メソッドが自分自身を返すので繋げられます(Pythonのメソッドチェーンや、
JSの配列メソッド `.filter().map()` と同じ発想)。

🚂 Rails経験者へ: `Todo.where(...).order(...)` と**ほぼ同じ**。Eloquentは ActiveRecord に
相当します(→ [Eloquentと設定より規約](laravel-concepts.md#14-eloquentと設定より規約))。

### 7-3. 「設定より規約」

`Todo` モデルが自動で `todos` テーブルに対応するなど、**命名規約に従うと設定が要らない**。
Rails出身者には馴染み深い思想です(→ 同上リンク)。

---

## 8. これだけ覚えれば読める:チートシート

| 記号/キーワード | 一言 |
|---|---|
| `$x` | 変数(必ず `$`) |
| `->` | インスタンスのメソッド/プロパティ |
| `::` | 静的(クラス自体に対して) |
| `$this` | 自分自身(≒ self / this) |
| `=>` | 配列のキー=>値、またはアロー関数 `fn () => ...` |
| `.` | 文字列連結(足し算ではない) |
| `??` / `?->` | null合体 / null安全(JSと同じ) |
| `use`(冒頭) | import |
| `use`(クラス内) | トレイトの取り込み(mixin) |
| `#[Attr]` | 属性(≒ デコレータ/アノテーション) |
| `{{ }}` / `@if` | Bladeの出力/制御構文 |

## 次に読むもの

- [開発トレースガイド](dev-walkthrough.md): 実際にコードを追いながら手を動かす本編
- [概念解説集](laravel-concepts.md): 各機能の「なぜ必要か(事故例つき)」
- まだPHP/Laravelを触ったことがなければ、まず [README](../../README.md) の手順で
  環境を起動し、`./vendor/bin/sail tinker` でこの文書の対応を実際に打ってみるのが速いです
