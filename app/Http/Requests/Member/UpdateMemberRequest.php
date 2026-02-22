<?php

declare(strict_types=1);

namespace App\Http\Requests\Member;

use App\Enums\CategoryType;
use App\Models\Member;
use App\Support\IcHasher;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('member'));
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
                    $exists = Member::where('ic_number_hash', $hash)
                        ->where('id', '!=', $this->route('member')->id)
                        ->exists();
                    if ($exists) {
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
