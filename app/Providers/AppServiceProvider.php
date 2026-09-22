<?php

namespace App\Providers;

use App\Models\Refund;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Share variabel refund pending ke semua view admin
        View::composer('layouts.admin', function ($view) {
            if (auth()->check() && auth()->user()->isAdmin()) {
                $view->with(
                    'globalRefundPending',
                    Refund::where('status_refund', 'pending')->count()
                );
            } else {
                $view->with('globalRefundPending', 0);
            }
        });
    }
}