<?php

namespace SocialDept\Beacon;

use Illuminate\Support\ServiceProvider;
use SocialDept\Beacon\Cache\LaravelCacheStore;
use SocialDept\Beacon\Contracts\CacheStore;
use SocialDept\Beacon\Contracts\DidResolver;
use SocialDept\Beacon\Contracts\HandleResolver;
use SocialDept\Beacon\Resolvers\AtProtoHandleResolver;
use SocialDept\Beacon\Resolvers\DidResolverManager;

class BeaconServiceProvider extends ServiceProvider
{
    /**
     * Register any package services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/beacon.php', 'beacon');

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

        // Register Beacon service
        $this->app->singleton('beacon', function ($app) {
            return new Beacon(
                $app->make(DidResolver::class),
                $app->make(HandleResolver::class),
                $app->make(CacheStore::class),
            );
        });

        $this->app->alias('beacon', Beacon::class);
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
        return ['beacon', Beacon::class];
    }

    /**
     * Console-specific booting.
     */
    protected function bootForConsole(): void
    {
        // Publish config
        $this->publishes([
            __DIR__.'/../config/beacon.php' => config_path('beacon.php'),
        ], 'beacon-config');
    }
}
