<?php

namespace App\Http\Requests;

use App\TransactionType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'gt:0'],
            'type' => ['required', Rule::enum(TransactionType::class)],
            'category_id' => [
                'required',
                'integer',
                Rule::exists('categories', 'id')
                    ->where('type', $this->input('type')),
            ],
            'description' => ['nullable', 'string'],
        ];
    }
}
