<?php

namespace Resera\ConsoleCommands;

use Illuminate\Support\ServiceProvider;
use Resera\ConsoleCommands\App\Console\Commands\GenerateBoilerplate;
use Resera\ConsoleCommands\App\Console\Commands\GenerateSubsystem;
use Resera\ConsoleCommands\App\Console\Commands\GenerateService;
use Resera\ConsoleCommands\App\Console\Commands\GenerateFormatter;
use Resera\ConsoleCommands\App\Console\Commands\GenerateValidator;
use Resera\ConsoleCommands\App\Console\Commands\GenerateResource;
use Resera\ConsoleCommands\App\Console\Commands\GenerateVueComponent;

class ConsoleCommandsServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

        if($this->app->runningInConsole()) {

            $this->commands([

                GenerateBoilerplate::class,
                GenerateSubsystem::class,
                GenerateFormatter::class,
                GenerateService::class,
                GenerateValidator::class,
                GenerateResource::class,
                GenerateVueComponent::class,

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