<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::all();
        return response()->json(["data"=>$transactions, "message"=>"Your transactions!",]);
    }

    public function show(int $id)
    {
        $transaction = Transaction::find($id);
        if(!$transaction){
            return response()->json(["message"=>"Transaction not found"], 404);
        }
        return response()->json(["data"=>$transaction]);
    }
}
