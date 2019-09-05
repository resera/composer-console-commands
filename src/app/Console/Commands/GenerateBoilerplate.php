<?php

namespace Resera\ConsoleCommands\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;

class GenerateBoilerplate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:boilerplate';
    protected $absPath;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates boilerplate code';

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

        $this->createDirectories();

        $this->printGreenText("Boilerplate code generated successfully! \n");

    }

    private function createDirectories()
    {

        $this->createDir('app/Model');
            $this->createDir('app/Model/Contracts');
                $this->createDir('app/Model/Contracts/AbstractClasses');
                $this->createDir('app/Model/Contracts/Interfaces');
                    $this->createDir('app/Model/Contracts/Interfaces/Data');
                    $this->createDir('app/Model/Contracts/Interfaces/Formatters');
                    $this->createDir('app/Model/Contracts/Interfaces/Validators');
                    $this->createDir('app/Model/Contracts/Interfaces/Services');
            $this->createDir('app/Model/Data');
                $this->createDir('app/Model/Data/Models');
                $this->createDir('app/Model/Data/Repositories');
            $this->createDir('app/Model/Events');
            $this->createDir('app/Model/Listeners');
            $this->createDir('app/Model/Facades');
            $this->createDir('app/Model/Formatters');
            $this->createDir('app/Model/Observers');
            $this->createDir('app/Model/Providers');
                $this->createDir('app/Model/Providers/Data');
                $this->createDir('app/Model/Providers/Facades');
                $this->createDir('app/Model/Providers/Formatters');
                $this->createDir('app/Model/Providers/Services');
                $this->createDir('app/Model/Providers/Validators');
                $this->createDir('app/Model/Providers/ViewComposers');
            $this->createDir('app/Model/Services');                
            $this->createDir('app/Model/Validators');                
            $this->createDir('app/Model/ViewComposers');                

        $this->createDir('resources/views/pages');

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

        $creation = new Process('mkdir '.$targetPath);
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