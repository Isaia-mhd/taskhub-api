<?php

namespace App\Providers;

use App\Interfaces\FileStorageInterface;
use App\Services\Storage\LocalFileStorage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            FileStorageInterface::class,
            LocalFileStorage::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
