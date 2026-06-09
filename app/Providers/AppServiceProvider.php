<?php

namespace App\Providers;

use App\Models\User;
use App\View\Composers\LayoutComposer;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('canDeliverPackage', fn (User $user) => $user->canDeliverPackage());

        View::composer('layouts.app', LayoutComposer::class);
    }
}
