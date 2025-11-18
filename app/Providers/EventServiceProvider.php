<?php
// app/Providers/EventServiceProvider.php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Models\Accounts;
use App\Observers\AccountsObserver;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [];

    public function boot(): void
    {
        // Register Accounts Observer
        Accounts::observe(AccountsObserver::class);
    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}