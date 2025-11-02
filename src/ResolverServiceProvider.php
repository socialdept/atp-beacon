<?php

namespace SocialDept\Resolver;

use Illuminate\Support\ServiceProvider;
use SocialDept\Resolver\Cache\LaravelCacheStore;
use SocialDept\Resolver\Contracts\CacheStore;
use SocialDept\Resolver\Contracts\DidResolver;
use SocialDept\Resolver\Contracts\HandleResolver;
use SocialDept\Resolver\Resolvers\AtProtoHandleResolver;
use SocialDept\Resolver\Resolvers\DidResolverManager;

class ResolverServiceProvider extends ServiceProvider
{
    /**
     * Register any package services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/resolver.php', 'resolver');

        // Register cache store
        $this->app->singleton(CacheStore::class, function ($app) {
            return new LaravelCacheStore($app->make('cache')->store());
        });

        // Register DID resolver
        $this->app->singleton(DidResolver::class, function ($app) {
            return new DidResolverManager();
        });

        // Register handle resolver
        $this->app->singleton(HandleResolver::class, function ($app) {
            return new AtProtoHandleResolver();
        });

        // Register Resolver service
        $this->app->singleton('resolver', function ($app) {
            return new Resolver(
                $app->make(DidResolver::class),
                $app->make(HandleResolver::class),
                $app->make(CacheStore::class),
            );
        });

        $this->app->alias('resolver', Resolver::class);
    }

    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<string>
     */
    public function provides(): array
    {
        return ['resolver', Resolver::class];
    }

    /**
     * Console-specific booting.
     */
    protected function bootForConsole(): void
    {
        // Publish config
        $this->publishes([
            __DIR__.'/../config/resolver.php' => config_path('resolver.php'),
        ], 'resolver-config');
    }
}
