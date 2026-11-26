<?php

namespace Tests\Feature;

use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_all_transaction_can_be_retrieved(): void
    {
        Transaction::create([
            'amount' => 1000,
            'type' => 'income',
            'category' => 'Salary',
            'description' => 'Monthly salary',
        ]);

        Transaction::create([
            'amount' => 500,
            'type' => 'expense',
            'category' => 'Food',
            'description' => 'Groceries',
        ]);

        $response = $this->getJson('/api/transactions');
        $response->assertOk();
        $response->assertJsonStructure(['data', 'message']);
        $response->assertJsonIsArray('data');
        $response->assertJsonCount(2, 'data');
    }

    public function test_single_transaction_can_be_retrieved(): void
    {
        $transaction = Transaction::create([
            'amount' => 1000,
            'type' => 'income',
            'category' => 'Salary',
            'description' => 'Monthly salary',
        ]);
        $response = $this->getjson('api/transactions/'.$transaction->id);
        $response->assertOk();
        $response->assertJsonPath('data.id', $transaction->id);
        $response->assertJsonPath('data.amount', '1000.00');
        $response->assertJsonPath('data.type', 'income');
        $response->assertJsonPath('data.category', 'Salary');
        $response->assertJsonPath('data.description', 'Monthly salary');
    }

    public function test_a_nonexistent_transaction_returns_404(): void
    {
        $response = $this->getJson('/api/transactions/999999');

        $response->assertNotFound();
        $response->assertJson([
            'message' => 'Transaction not found',
        ]);
    }

    public function test_transaction_can_be_created(): void
    {
        $data = [
            'amount' => 2500,
            'type' => 'income',
            'category' => 'Salary',
            'description' => 'Monthly salary',
        ];

        $response = $this->postJson('/api/transactions', $data);

        $response->assertCreated();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'amount',
                'type',
                'category',
                'description',
                'created_at',
                'updated_at',
            ],
            'message',
        ]);

        $response->assertJsonPath('data.amount', '2500.00');
        $response->assertJsonPath('data.type', 'income');
        $response->assertJsonPath('data.category', 'Salary');
        $response->assertJsonPath('data.description', 'Monthly salary');

        $this->assertDatabaseHas('transactions', [
            'amount' => 2500,
            'type' => 'income',
            'category' => 'Salary',
            'description' => 'Monthly salary',
        ]);
    }

    public function test_transaction_cannot_be_created_with_invalid_data(): void
    {
        $data = [
            'amount' => -100,
            'type' => 'income',
            'category' => 'Salary',
            'description' => 'Invalid transaction',
        ];

        $response = $this->postJson('/api/transactions', $data);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['amount']);

        $this->assertDatabaseMissing('transactions', [
            'amount' => -100,
            'type' => 'income',
            'category' => 'Salary',
            'description' => 'Invalid transaction',
        ]);
    }

    public function test_transaction_can_be_updated(): void
    {
        $transaction = Transaction::create([
            'amount' => 1000,
            'type' => 'income',
            'category' => 'Salary',
            'description' => 'Monthly salary',
        ]);

        $data = [
            'amount' => 1500,
            'type' => 'income',
            'category' => 'Bonus',
            'description' => 'Quarterly bonus',
        ];

        $response = $this->putJson('/api/transactions/'.$transaction->id, $data);

        $response->assertOk();
        $response->assertJsonPath('data.id', $transaction->id);
        $response->assertJsonPath('data.amount', '1500.00');
        $response->assertJsonPath('data.category', 'Bonus');
        $response->assertJsonPath('data.description', 'Quarterly bonus');
        $response->assertJsonPath('message', 'Transaction updated successfully');

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'amount' => 1500,
            'type' => 'income',
            'category' => 'Bonus',
            'description' => 'Quarterly bonus',
        ]);
    }

    public function test_transaction_cannot_be_updated_with_invalid_data(): void
    {
        $transaction = Transaction::create([
            'amount' => 1000,
            'type' => 'income',
            'category' => 'Salary',
            'description' => 'Monthly salary',
        ]);

        $data = [
            'amount' => -500,
            'type' => 'expense',
            'category' => 'Food',
            'description' => 'Groceries',
        ];

        $response = $this->putJson('/api/transactions/'.$transaction->id, $data);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['amount']);

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'amount' => 1000,
            'type' => 'income',
            'category' => 'Salary',
            'description' => 'Monthly salary',
        ]);
    }

    public function test_nonexistent_transaction_cannot_be_updated(): void
    {
        $data = [
            'amount' => 1500,
            'type' => 'income',
            'category' => 'Salary',
            'description' => 'Monthly salary',
        ];

        $response = $this->putJson('/api/transactions/999999', $data);

        $response->assertNotFound();
        $response->assertJson([
            'message' => 'Transaction not found',
        ]);
    }

    public function test_transaction_can_be_deleted(): void
    {
        $transaction = Transaction::create([
            'amount' => 1000,
            'type' => 'income',
            'category' => 'Salary',
            'description' => 'Monthly salary',
        ]);

        $response = $this->deleteJson('/api/transactions/'.$transaction->id);

        $response->assertOk();
        $response->assertJson([
            'message' => 'Transaction deleted successfully',
        ]);

        $this->assertDatabaseMissing('transactions', [
            'id' => $transaction->id,
        ]);
    }

    public function test_nonexistent_transaction_cannot_be_deleted(): void
    {
        $response = $this->deleteJson('/api/transactions/999999');

        $response->assertNotFound();
        $response->assertJson([
            'message' => 'Transaction not found',
        ]);
    }
}
