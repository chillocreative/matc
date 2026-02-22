<?php

declare(strict_types=1);

namespace App\Http\Requests\Attendance;

use App\Models\Member;
use App\Support\IcHasher;
use Illuminate\Foundation\Http\FormRequest;

class VerifyAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Attendance::class);
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
            'meeting_id' => ['required', 'integer', 'exists:meetings,id'],
            'ic_number' => [
                'required',
                'string',
                'max:20',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $hash = IcHasher::hash($value);
                    if (! Member::where('ic_number_hash', $hash)->exists()) {
                        $fail('No. IC ini tidak berdaftar dalam sistem.');
                    }
                },
            ],
        ];
    }
}
