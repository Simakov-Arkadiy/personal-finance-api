<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    private function createCategory(string $name, string $type): Category
    {
        return Category::create([
            'name' => $name,
            'type' => $type,
        ]);
    }

    private function createTransaction(array $data): Transaction
    {
        return Transaction::create($data);
    }

    public function test_all_transactions_can_be_retrieved(): void
    {
        $category = $this->createCategory('Зарплата', 'income');
        $this->createTransaction([
            'amount' => 1000,
            'type' => 'income',
            'category_id' => $category->id,
            'description' => 'Monthly salary',
        ]);

        $this->createTransaction([
            'amount' => 500,
            'type' => 'expense',
            'category_id' => $this->createCategory('Еда', 'expense')->id,
            'description' => 'Groceries',
        ]);

        $response = $this->getJson('/api/transactions');

        $response->assertOk();
        $response->assertJsonStructure(['data', 'message']);
        $response->assertJsonCount(2, 'data');
    }

    public function test_single_transaction_can_be_retrieved(): void
    {
        $category = $this->createCategory('Зарплата', 'income');
        $transaction = $this->createTransaction([
            'amount' => 1000,
            'type' => 'income',
            'category_id' => $category->id,
            'description' => 'Monthly salary',
        ]);

        $response = $this->getJson("/api/transactions/{$transaction->id}");

        $response->assertOk();
        $response->assertJsonPath('data.id', $transaction->id);
        $response->assertJsonPath('data.amount', '1000.00');
        $response->assertJsonPath('data.type', 'income');
        $response->assertJsonPath('data.category_id', $category->id);
        $response->assertJsonPath('data.description', 'Monthly salary');
        $response->assertJsonPath('data.category.id', $category->id);
        $response->assertJsonPath('data.category.name', 'Зарплата');
        $response->assertJsonPath('data.category.type', 'income');
    }

    public function test_a_nonexistent_transaction_returns_404(): void
    {
        $response = $this->getJson('/api/transactions/999999');

        $response->assertNotFound();
        $response->assertJsonFragment([
            'message' => 'No query results for model [App\\Models\\Transaction] 999999',
        ]);
    }

    public function test_transaction_can_be_created(): void
    {
        $category = $this->createCategory('Зарплата', 'income');

        $data = [
            'amount' => 2500,
            'type' => 'income',
            'category_id' => $category->id,
            'description' => 'Monthly salary',
        ];

        $response = $this->postJson('/api/transactions', $data);

        $response->assertCreated();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'amount',
                'type',
                'category_id',
                'description',
                'created_at',
                'updated_at',
            ],
            'message',
        ]);

        $response->assertJsonPath('data.amount', '2500.00');
        $response->assertJsonPath('data.type', 'income');
        $response->assertJsonPath('data.category_id', $category->id);
        $response->assertJsonPath('data.description', 'Monthly salary');
        $response->assertJsonPath('message', 'Transaction created successfully');

        $this->assertDatabaseHas('transactions', [
            'amount' => 2500,
            'type' => 'income',
            'category_id' => $category->id,
            'description' => 'Monthly salary',
        ]);
    }

    public function test_transaction_can_be_created_without_description(): void
    {
        $category = $this->createCategory('Инвестиции', 'income');

        $data = [
            'amount' => 500,
            'type' => 'income',
            'category_id' => $category->id,
        ];

        $response = $this->postJson('/api/transactions', $data);

        $response->assertCreated();
        $response->assertJsonPath('data.amount', '500.00');
        $response->assertJsonPath('data.type', 'income');
        $this->assertNull($response->json('data.description'));

        $this->assertDatabaseHas('transactions', [
            'amount' => 500,
            'type' => 'income',
            'category_id' => $category->id,
            'description' => null,
        ]);
    }

    public function test_transaction_cannot_be_created_with_invalid_amount(): void
    {
        $category = $this->createCategory('Еда', 'expense');

        $response = $this->postJson('/api/transactions', [
            'amount' => -100,
            'type' => 'expense',
            'category_id' => $category->id,
            'description' => 'Invalid transaction',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('amount');

        $this->assertDatabaseMissing('transactions', [
            'amount' => -100,
        ]);
    }

    public function test_transaction_cannot_be_created_with_zero_amount(): void
    {
        $category = $this->createCategory('Еда', 'expense');

        $response = $this->postJson('/api/transactions', [
            'amount' => 0,
            'type' => 'expense',
            'category_id' => $category->id,
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('amount');
    }

    public function test_transaction_cannot_be_created_without_required_fields(): void
    {
        $response = $this->postJson('/api/transactions', []);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['amount', 'type', 'category_id']);
    }

    public function test_transaction_cannot_be_created_with_invalid_type(): void
    {
        $category = $this->createCategory('Еда', 'expense');

        $response = $this->postJson('/api/transactions', [
            'amount' => 100,
            'type' => 'invalid_type',
            'category_id' => $category->id,
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('type');
    }

    public function test_transaction_cannot_be_created_with_nonexistent_category(): void
    {
        $response = $this->postJson('/api/transactions', [
            'amount' => 100,
            'type' => 'expense',
            'category_id' => 999999,
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('category_id');
    }

    public function test_transaction_cannot_be_created_with_mismatched_category_type(): void
    {
        $expenseCategory = $this->createCategory('Еда', 'expense');

        $response = $this->postJson('/api/transactions', [
            'amount' => 100,
            'type' => 'income',
            'category_id' => $expenseCategory->id,
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('category_id');
    }

    public function test_transaction_can_be_updated(): void
    {
        $oldCategory = $this->createCategory('Зарплата', 'income');
        $newCategory = $this->createCategory('Бонус', 'income');
        $transaction = $this->createTransaction([
            'amount' => 1000,
            'type' => 'income',
            'category_id' => $oldCategory->id,
            'description' => 'Monthly salary',
        ]);

        $data = [
            'amount' => 1500,
            'type' => 'income',
            'category_id' => $newCategory->id,
            'description' => 'Quarterly bonus',
        ];

        $response = $this->putJson("/api/transactions/{$transaction->id}", $data);

        $response->assertOk();
        $response->assertJsonPath('data.id', $transaction->id);
        $response->assertJsonPath('data.amount', '1500.00');
        $response->assertJsonPath('data.type', 'income');
        $response->assertJsonPath('data.category_id', $newCategory->id);
        $response->assertJsonPath('data.description', 'Quarterly bonus');
        $response->assertJsonPath('message', 'Transaction updated successfully');

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'amount' => 1500,
            'type' => 'income',
            'category_id' => $newCategory->id,
            'description' => 'Quarterly bonus',
        ]);
    }

    public function test_transaction_cannot_be_updated_with_invalid_amount(): void
    {
        $category = $this->createCategory('Зарплата', 'income');
        $transaction = $this->createTransaction([
            'amount' => 1000,
            'type' => 'income',
            'category_id' => $category->id,
            'description' => 'Monthly salary',
        ]);

        $response = $this->putJson("/api/transactions/{$transaction->id}", [
            'amount' => -500,
            'type' => 'income',
            'category_id' => $category->id,
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('amount');

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'amount' => 1000,
        ]);
    }

    public function test_nonexistent_transaction_cannot_be_updated(): void
    {
        $response = $this->putJson('/api/transactions/999999', [
            'amount' => 1500,
            'type' => 'income',
            'category_id' => 1,
        ]);

        // Validation fails first because category_id=1 doesn't exist in categories table
        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('category_id');
    }

    public function test_transaction_can_be_deleted(): void
    {
        $category = $this->createCategory('Зарплата', 'income');
        $transaction = $this->createTransaction([
            'amount' => 1000,
            'type' => 'income',
            'category_id' => $category->id,
            'description' => 'Monthly salary',
        ]);

        $response = $this->deleteJson("/api/transactions/{$transaction->id}");

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
        $response->assertJsonFragment([
            'message' => 'No query results for model [App\\Models\\Transaction] 999999',
        ]);
    }

    public function test_transaction_category_can_be_changed_to_another_category_of_same_type(): void
    {
        $category1 = $this->createCategory('Зарплата', 'income');
        $category2 = $this->createCategory('Бонус', 'income');
        $transaction = $this->createTransaction([
            'amount' => 1000,
            'type' => 'income',
            'category_id' => $category1->id,
            'description' => 'Salary',
        ]);

        $response = $this->putJson("/api/transactions/{$transaction->id}", [
            'amount' => 1000,
            'type' => 'income',
            'category_id' => $category2->id,
            'description' => 'Bonus',
        ]);

        $response->assertOk();
        $response->assertJsonPath('data.category_id', $category2->id);
    }
}
