<?php

namespace App\Providers;

use App\Interfaces\ArchiveInterface;
use App\Interfaces\CategoryInterface;
use App\Services\ArchiveService;
use App\Services\CategoryService;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(ArchiveInterface::class, ArchiveService::class);
        $this->app->bind(CategoryInterface::class, CategoryService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void {}
}
