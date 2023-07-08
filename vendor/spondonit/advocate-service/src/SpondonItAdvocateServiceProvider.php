<?php

namespace SpondonIt\AdvocateService;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Http\Kernel;
use SpondonIt\AdvocateService\Middleware\AdvocateService;

class SpondonItAdvocateServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $kernel = $this->app->make(Kernel::class);
        $kernel->pushMiddleware(AdvocateService::class);

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'advocate');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'advocate');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
