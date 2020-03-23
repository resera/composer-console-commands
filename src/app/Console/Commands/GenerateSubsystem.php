<?php

namespace Resera\ConsoleCommands\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;

class GenerateSubsystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:subsystem {name}';
    protected $absPath;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates subsystem code';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->absPath = env('PATH_TO_PUBLIC');
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        if(!$this->argument('name'))
        {

            $this->printRedText("NAME ARGUMENT IS REQUIRED");
            return;

        }

        $this->createDirectories();
        $this->printGreenText("Subsystem code generated successfully! \n");

    }

    private function createDirectories()
    {

        $this->createDir('app/Model/Contracts/Interfaces/Formatters/'.$this->argument('name'));
        $this->createDir('app/Model/Contracts/Interfaces/Validators/'.$this->argument('name'));
        $this->createDir('app/Model/Contracts/Interfaces/Services/'.$this->argument('name'));
        $this->createDir('app/Model/Formatters/'.$this->argument('name'));
        $this->createDir('app/Model/Events/'.$this->argument('name'));
        $this->createDir('app/Model/Listeners/'.$this->argument('name'));
        $this->createDir('app/Model/Services/'.$this->argument('name'));
        $this->createDir('app/Model/Validators/'.$this->argument('name'));
        $this->createDir('app/Model/Providers/Formatters/'.$this->argument('name'));
        $this->createDir('app/Model/Providers/Services/'.$this->argument('name'));
        $this->createDir('app/Model/Providers/Validators/'.$this->argument('name'));
        $this->createDir('app/Model/Providers/ViewComposers/'.$this->argument('name'));
        $this->createDir('app/Http/Controllers/'.$this->argument('name'));
        $this->createDir('app/Http/Middleware/'.$this->argument('name'));
        $this->createDir('resources/views/pages/'.$this->argument('name'));
        $this->createDir('resources/lang/en/'.$this->argument('name'));

    }

    private function createDir($path)
    {

        $targetPath = $this->absPath . $path;

        $this->printWhiteText("Creating " . $targetPath . "... \n");

        if(file_exists($targetPath))
        {

            $this->printRedText($targetPath . " already exists. \n");
            return;

        }

        $creation = new Process(['mkdir', $targetPath]);
        $creation->run(); 

    }

    private function printGreenText($text)
    {
        echo "\033[32m".$text." \033[0m \n";
    }

    private function printRedText($text)
    {
        echo "\033[31m".$text." \033[0m \n";
    }    

    private function printWhiteText($text)
    {
        echo $text;
    }      

}