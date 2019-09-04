<?php

namespace Resera\ConsoleCommands;

use Illuminate\Support\ServiceProvider;
use Resera\ConsoleCommands\App\Console\Commands\GenerateBoilerplate;
use Resera\ConsoleCommands\App\Console\Commands\GenerateSubsystem;

class ConsoleCommandsServiceProvider extends ServiceProvider
{
 
    /**  
     * Bootstrap the application services.
     * 
     * @return void
     */ 
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateBoilerplate::class,
                GenerateSubsystem::class
            ]);
        }        
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
