<?php
namespace App\Http\Controllers;

use App\Core\App;

class PageController
{
    public function home(): void
    {
        App::view('home');
    }
}
