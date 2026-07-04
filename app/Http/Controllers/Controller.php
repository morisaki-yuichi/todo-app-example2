<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller
{
    // $this->authorize(...) を全コントローラで使えるようにする。
    // Laravel 11以降はデフォルトで外れているため、明示的に取り込む
    use AuthorizesRequests;
}
