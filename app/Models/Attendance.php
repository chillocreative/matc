<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AttendanceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'meeting_id',
        'member_id',
        'ic_number_hash',
        'category_type',
        'status',
        'absence_reason',
    ];

    protected function casts(): array
    {
        return [
            'status' => AttendanceStatus::class,
            'created_at' => 'datetime',
        ];
    }

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
