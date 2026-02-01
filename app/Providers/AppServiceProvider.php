<?php

declare(strict_types=1);

namespace App\Providers;

use App\Core\Interfaces\OrderRepositoryInterface;
use App\Core\Interfaces\ProductRepositoryInterface;
use App\Core\Interfaces\UserRepositoryInterface;
use App\Domains\Order\Repositories\OrderRepositoryInterface as DomainOrderRepositoryInterface;
use App\Domains\Product\Repositories\ProductRepositoryInterface as DomainProductRepositoryInterface;
use App\Domains\User\Repositories\UserRepositoryInterface as DomainUserRepositoryInterface;
use App\Infrastructure\Repositories\DDD\EloquentOrderDomainRepository;
use App\Infrastructure\Repositories\DDD\EloquentProductDomainRepository;
use App\Infrastructure\Repositories\DDD\EloquentUserDomainRepository;
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
        // Legacy SOLID Architecture Bindings
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

        // DDD Architecture Bindings
        // Bind Domain User Repository
        $this->app->bind(
            DomainUserRepositoryInterface::class,
            EloquentUserDomainRepository::class
        );

        // Bind Domain Product Repository
        $this->app->bind(
            DomainProductRepositoryInterface::class,
            EloquentProductDomainRepository::class
        );

        // Bind Domain Order Repository
        $this->app->bind(
            DomainOrderRepositoryInterface::class,
            EloquentOrderDomainRepository::class
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
