<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        return response()->json(["data"=>[], "message"=>"Your transactions!",]);
    }
}
