<?php

namespace App\Providers;

use App\Interfaces\ArchiveInterface;
use App\Interfaces\CategoryInterface;
use App\Interfaces\UserInterface;
use App\Services\ArchiveService;
use App\Services\CategoryService;
use App\Services\UserService;
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
        $this->app->bind(UserInterface::class, UserService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void {}
}
