<?php
namespace App\Http\Controllers;

use App\Core\App;
use App\Core\DB;

class InstallerController
{
    public function form(): void
    {
        App::view('installer');
    }
}
