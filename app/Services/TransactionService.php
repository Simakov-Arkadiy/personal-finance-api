<?php

namespace App\Services;

use App\Contracts\TransactionServiceInterface;
use App\Dto\TransactionDTO;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;
use Override;

class TransactionService implements TransactionServiceInterface
{
    #[Override]
    public function create(TransactionDTO $data): Transaction
    {
        return Transaction::create([
            'amount' => $data->amount,
            'type' => $data->type,
            'category_id' => $data->categoryId,
            'description' => $data->description,
        ]);
    }

    #[Override]
    public function getAll(): Collection
    {
        return Transaction::with('category')->get();
    }

    #[Override]
    public function get(int $id): Transaction
    {
        return Transaction::with('category')->findOrFail($id);
    }

    #[Override]
    public function update(int $id, TransactionDTO $data): Transaction
    {
        $transaction = Transaction::with('category')->findOrFail($id);

        $transaction->update([
            'amount' => $data->amount,
            'type' => $data->type,
            'category_id' => $data->categoryId,
            'description' => $data->description,
        ]);

        return $transaction;
    }

    #[Override]
    public function delete(int $id): Transaction
    {
        $transaction = Transaction::findOrFail($id);

        $transaction->delete();

        return $transaction;
    }
}
