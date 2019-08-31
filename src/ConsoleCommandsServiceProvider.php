<?php

namespace Buzz\LaravelGoogleCaptcha;

use Illuminate\Support\ServiceProvider;

class ConsoleCommandsServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/app/Console/Commands' => base_path('app/Console/Commands'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

    }

}