<?php

namespace App\Http\Controllers;

use App\Contracts\TransactionServiceInterface;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TransactionController extends Controller
{
    public function __construct(
        private readonly TransactionServiceInterface $transactionService
    ) {}

    public function index(): JsonResponse
    {
        $transactions = $this->transactionService->getAll();

        return response()->json(['data' => $transactions, 'message' => 'Your transactions!']);
    }

    public function show(int $id): JsonResponse
    {
        $transaction = $this->transactionService->get($id);
        if (! $transaction) {
            return response()->json(['message' => 'Transaction not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['data' => $transaction]);
    }

    public function store(StoreTransactionRequest $request): JsonResponse
    {
        $transaction = $this->transactionService->create($request->validated());

        return response()->json(['data' => $transaction, 'message' => 'Transaction created successfully'], Response::HTTP_CREATED);
    }

    public function update(UpdateTransactionRequest $request, int $id): JsonResponse
    {
        $transaction = $this->transactionService->update($id, $request->validated());
        if (! $transaction) {
            return response()->json(['message' => 'Transaction not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['data' => $transaction, 'message' => 'Transaction updated successfully']);
    }

    public function delete(int $id): JsonResponse
    {
        $transaction = $this->transactionService->delete($id);

        if (! $transaction) {
            return response()->json(['message' => 'Transaction not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'Transaction deleted successfully']);
    }
}
