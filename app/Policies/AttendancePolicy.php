<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Attendance;
use App\Models\User;

class AttendancePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Attendance $attendance): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->is_admin;
    }

    public function update(User $user, Attendance $attendance): bool
    {
        return $user->is_admin;
    }

    public function delete(User $user, Attendance $attendance): bool
    {
        return $user->is_admin;
    }
}
