<?php

declare(strict_types=1);

namespace App\Http\Requests\Member;

use App\Enums\CategoryType;
use App\Models\Member;
use App\Support\IcHasher;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Member::class);
    }

    protected function prepareForValidation(): void
    {
        if ($this->ic_number) {
            $this->merge([
                'ic_number' => preg_replace('/[^0-9]/', '', $this->ic_number),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'category_type' => ['required', Rule::enum(CategoryType::class)],
            'name' => ['required', 'string', 'max:255'],
            'ic_number' => [
                'required',
                'string',
                'max:20',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $hash = IcHasher::hash($value);
                    if (Member::where('ic_number_hash', $hash)->exists()) {
                        $fail('No. IC ini sudah berdaftar.');
                    }
                },
            ],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'position_type' => ['nullable', 'string', 'max:255'],
            'position_name' => ['nullable', 'string', 'max:255'],
        ];
    }
}
