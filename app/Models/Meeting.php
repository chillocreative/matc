<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MeetingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Meeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'date',
        'time',
        'location',
        'status',
        'suggestion_enabled',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'status' => MeetingStatus::class,
            'suggestion_enabled' => 'boolean',
        ];
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
