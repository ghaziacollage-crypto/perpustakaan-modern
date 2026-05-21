<?php

declare(strict_types=1);

namespace App\Http\Requests\Borrowings;

use Illuminate\Foundation\Http\FormRequest;

class StoreReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'return_date' => ['required', 'date'],
            'detail_ids'  => ['nullable', 'string'], // accept comma-separated string
            'condition'    => ['nullable', 'string', 'max:255'],
            'notes'        => ['nullable', 'string'],
        ];
    }

    #[\ReturnTypeWillChange]
    public function validated($key = null, $default = null): mixed
    {
        if ($key !== null) {
            return parent::validated($key, $default);
        }

        $validated = parent::validated($key, $default);

        // Convert comma-separated string to array of integers
        $validated['detail_ids'] = isset($validated['detail_ids'])
            ? array_map('intval', array_filter(array_map('trim', explode(',', (string) $validated['detail_ids']))))
            : [];

        /** @var array<string, mixed> */
        return $validated;
    }
}