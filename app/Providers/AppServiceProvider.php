<?php

namespace App\Providers;

use App\Infrastructure\Doctrine\Repositories as Doctrine;
use Illuminate\Support\ServiceProvider;
use WorkshopBackoffice\Repositories;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Implementation bindings.
     *
     * @var string[]|array[]
     */
    private array $classBindings = [
        //Generic Repositories
        Repositories\PersistRepository::class => Doctrine\DoctrinePersistRepository::class,

        //Read Repositories
        Repositories\GuestCategoryRepository::class => Doctrine\DoctrineGuestCategoryRepository::class,

        /* Example for environment specific implementations
        ExampleRepo::class => [
            'production' => ProductionExampleRepo::class,
            'qa' => QAExampleRepo::class,
            'default' => DefaultExampleRepo::class,
        ],
        */
    ];

    /**
     * Register any application services.
     */
    public function register()
    {
        foreach ($this->classBindings as $abstract => $concrete) {
            if (is_array($concrete)) {
                $concrete = $concrete[$this->app->environment()] ?? $concrete['default'];
            }

            $this->app->bind($abstract, $concrete);
        }

        if (config('app.debug')) {
            $this->app->register(\Arcanedev\LogViewer\LogViewerServiceProvider::class);
            $this->app->register(\Arcanedev\LogViewer\Providers\DeferredServicesProvider::class);
            $this->app->register(\PrettyRoutes\ServiceProvider::class);
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }

        $this->configureMonologSentryHandler();
    }

    private function configureMonologSentryHandler(): void
    {
        if (env('SENTRY_ENABLED')) {
            $this->app->register(\Sentry\Laravel\ServiceProvider::class);
        }
    }
}
