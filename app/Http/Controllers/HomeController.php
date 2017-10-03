<?php

namespace App\Http\Controllers;

use App\Services\Calculator;

class HomeController extends Controller
{
    public function index()
    {
        $calculator = new Calculator('-(7+2)');
        echo 'your result is: ' . ($calculator->result());
    }
}
