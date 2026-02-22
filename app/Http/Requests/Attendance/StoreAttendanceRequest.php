<?php

declare(strict_types=1);

namespace App\Http\Requests\Attendance;

use App\Enums\AttendanceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Attendance::class);
    }

    public function rules(): array
    {
        return [
            'meeting_id' => ['required', 'integer', 'exists:meetings,id'],
            'attendances' => ['required', 'array'],
            'attendances.*.member_id' => ['required', 'integer', 'exists:members,id'],
            'attendances.*.status' => ['required', Rule::enum(AttendanceStatus::class)],
        ];
    }
}
