<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\User;
use App\Models\Admin;
use App\Models\Cart;
use App\Models\Review;
use App\Policies\UserPolicy;
use App\Policies\AdminPolicy;
use App\Policies\ReviewPolicy;
use App\Policies\CartPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Admin::class => AdminPolicy::class,
        Cart::class => CartPolicy::class,
        CartItem::class => CartPolicy::class,
        Review::class => ReviewPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
