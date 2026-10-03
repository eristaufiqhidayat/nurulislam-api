<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Menu
use App\Repositories\Menu\MenuRepository;
use App\Repositories\Menu\MenuRepositoryInterface;

// Role
use App\Repositories\roles\RoleRepository;
use App\Repositories\roles\RoleRepositoryInterface;

use App\Repositories\Product\ProductRepositoryInterface;
use App\Repositories\Product\ProductRepository;
use App\Repositories\Category\CategoryRepository;
use App\Repositories\Category\CategoryRepositoryInterface;
use App\Repositories\Shop\ShopRepository;
use App\Repositories\Shop\ShopRepositoryInterface;
use App\Repositories\Order\OrderRepository;
use App\Repositories\Order\OrderRepositoryInterface;
use App\Repositories\Cart\CartRepository;
use App\Repositories\Cart\CartRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // MENU
        $this->app->bind(
            MenuRepositoryInterface::class,
            MenuRepository::class
        );

        // ROLE
        $this->app->bind(
            RoleRepositoryInterface::class,
            RoleRepository::class
        );

        $this->app->bind(
            ProductRepositoryInterface::class,
            ProductRepository::class
        );
        $this->app->bind(
            CategoryRepositoryInterface::class,
            CategoryRepository::class
        );
        $this->app->bind(
            ShopRepositoryInterface::class,
            ShopRepository::class
        );
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(
            CartRepositoryInterface::class,
            CartRepository::class
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
