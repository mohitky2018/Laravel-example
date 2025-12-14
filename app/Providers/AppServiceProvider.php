<?php

declare(strict_types=1);

namespace App\Providers;

use App\Core\Interfaces\OrderRepositoryInterface;
use App\Core\Interfaces\ProductRepositoryInterface;
use App\Core\Interfaces\UserRepositoryInterface;
use App\Infrastructure\Repositories\EloquentOrderRepository;
use App\Infrastructure\Repositories\EloquentProductRepository;
use App\Infrastructure\Repositories\EloquentUserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind UserRepositoryInterface to EloquentUserRepository
        $this->app->bind(
            UserRepositoryInterface::class,
            EloquentUserRepository::class
        );

        // Bind ProductRepositoryInterface to EloquentProductRepository
        $this->app->bind(
            ProductRepositoryInterface::class,
            EloquentProductRepository::class
        );

        // Bind OrderRepositoryInterface to EloquentOrderRepository
        $this->app->bind(
            OrderRepositoryInterface::class,
            EloquentOrderRepository::class
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

