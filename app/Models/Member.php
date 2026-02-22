<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CategoryType;
use App\Support\IcHasher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'category_type',
        'name',
        'ic_number',
        'ic_number_hash',
        'phone_number',
        'address',
        'position_type',
        'position_name',
    ];

    protected function casts(): array
    {
        return [
            'category_type' => CategoryType::class,
            'ic_number' => 'encrypted',
            'phone_number' => 'encrypted',
            'created_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Member $member): void {
            $member->ic_number_hash = IcHasher::hash($member->ic_number);
        });

        static::updating(function (Member $member): void {
            if ($member->isDirty('ic_number')) {
                $member->ic_number_hash = IcHasher::hash($member->ic_number);
            }
        });
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
