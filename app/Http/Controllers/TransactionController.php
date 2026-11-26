<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TransactionController extends Controller
{
    public function index(): JsonResponse
    {
        $transactions = Transaction::all();

        return response()->json(['data' => $transactions, 'message' => 'Your transactions!']);
    }

    public function show(int $id): JsonResponse
    {
        $transaction = Transaction::find($id);
        if (! $transaction) {
            return response()->json(['message' => 'Transaction not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['data' => $transaction]);
    }

    public function store(StoreTransactionRequest $request): JsonResponse
    {
        $transaction = Transaction::create($request->validated());

        return response()->json(['data' => $transaction, 'message' => 'Transaction created succefully'], Response::HTTP_CREATED);
    }

    public function update(UpdateTransactionRequest $request, int $id): JsonResponse
    {
        $transaction = Transaction::find($id);
        if(! $transaction) {
            return response()->json(['message' => 'Transaction not found', Response::HTTP_NOT_FOUND]);
        }

        $transaction->update($request->validated());

        return response()->json(['data' => $transaction, 'message' => 'Transaction updated successfully']);
    }

    public function delete(int $id): JsonResponse
    {
        $transaction = Transaction::find($id);

        if(! $transaction) {
            return response()->json(['message' => 'Transaction not found', Response::HTTP_NOT_FOUND]);
        }

        $transaction->delete();

        return response()->json(['message' =>  'Transaction deleted successfully']);
    }
}
