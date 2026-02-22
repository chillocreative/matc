<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\Contracts\MeetingRepositoryInterface;
use App\Repositories\Contracts\MemberRepositoryInterface;
use App\Repositories\Eloquent\MeetingRepository;
use App\Repositories\Eloquent\MemberRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(MemberRepositoryInterface::class, MemberRepository::class);
        $this->app->bind(MeetingRepositoryInterface::class, MeetingRepository::class);
    }
}
