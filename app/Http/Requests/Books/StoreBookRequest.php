<?php

declare(strict_types=1);

namespace App\Http\Requests\Books;

use App\Enums\BookCondition;
use App\Enums\BookStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'book_code' => ['required', 'string', 'max:50', 'unique:books,book_code'],
            'isbn' => ['nullable', 'string', 'max:50'],
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'author' => ['nullable', 'string', 'max:255'],
            'publisher' => ['nullable', 'string', 'max:255'],
            'year' => ['nullable', 'integer', 'min:1900', 'max:' . date('Y')],
            'stock' => ['required', 'integer', 'min:0'],
            'rack_location' => ['nullable', 'string', 'max:100'],
            'status' => ['required', Rule::enum(BookStatus::class)],
            'kondisi' => ['required', Rule::enum(BookCondition::class)],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }
}
