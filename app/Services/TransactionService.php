<?php

namespace App\Services;

use App\Contracts\TransactionServiceInterface;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;
use Override;

class TransactionService implements TransactionServiceInterface
{
    #[Override]
    public function create(array $data): Transaction
    {
        return Transaction::create($data);
    }

    #[Override]
    public function getAll(): Collection
    {
        return Transaction::with('category')->get();
    }

    #[Override]
    public function get(int $id): ?Transaction
    {
        return Transaction::with('category')->find($id);
    }

    #[Override]
    public function update(int $id, array $data): Transaction
    {
        $transaction = Transaction::find($id);

        $transaction->update($data);

        return $transaction;
    }

    #[Override]
    public function delete(int $id): ?Transaction
    {
        $transaction = Transaction::find($id);

        if (! $transaction) {
            return null;
        }

        $transaction->delete();

        return $transaction;
    }
}
