<?php

namespace App\Providers;

use BaseApi\Container\ServiceProvider;
use BaseApi\Container\ContainerInterface;
use App\Auth\SimpleUserProvider;
use App\Services\EmailService;
use BaseApi\Auth\UserProvider;

/**
 * Application service provider.
 * 
 * Register application-specific services here.
 */
class AppServiceProvider extends ServiceProvider
{
    public function register(ContainerInterface $container): void
    {
        // Register the user provider
        $container->singleton(UserProvider::class, SimpleUserProvider::class);
        
        // Register the email service as singleton
        $container->singleton(EmailService::class);
        
        // Example: Register a custom service with manual configuration
        // $container->singleton(SomeService::class, function (ContainerInterface $c) {
        //     return new SomeService($c->make(SomeDependency::class));
        // });
    }

    public function boot(ContainerInterface $container): void
    {
        // Boot services after registration
        // Example: Configure services that depend on other services
        
        // Set the user provider in the App
        \BaseApi\App::setUserProvider($container->make(UserProvider::class));
    }
}
