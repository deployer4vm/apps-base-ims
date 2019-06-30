<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;

class ApplicationController extends Controller
{
    public function __invoke()
    {
        // dd(config('appconfig'));
        // $tmpPackage = json_decode(file_get_contents(__DIR__.'/../../MainApp/config/package.json'),true);
        // dd($tmpPackage);
        return view('layouts.admin.main');
    }
}
