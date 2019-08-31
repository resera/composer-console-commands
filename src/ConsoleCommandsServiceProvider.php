<?php

namespace Resera\ConsoleCommands;

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
            __DIR__.'/app/Console/Commands/GenerateBoilerplate.php' => base_path('app/Console/Commands/GenerateBoilerplate.php'),
            __DIR__.'/app/Console/Commands/GenerateSubsystem.php' => base_path('app/Console/Commands/GenerateSubsystem.php')
        ], 'resera-console-commands');
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
