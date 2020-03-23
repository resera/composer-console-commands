<?php

namespace Resera\ConsoleCommands\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;

class GenerateFacade extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:facade {name}';
    protected $absPath;

    private $facadeProvider;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $FACADE_TEMPLATE = "<?php\n\nnamespace App\Model\Facades;\n\nuse Illuminate\Support\Facades\Facade;\n\nclass %1\$sFacade extends Facade \n{\n\n\tprotected static function getFacadeAccessor() { return '%2\$s'; }\n\n}";
    protected $FACADE_PROVIDER_TEMPLATE = "<?php\n\nnamespace App\Model\Providers\Facades;\n\nuse Illuminate\Support\ServiceProvider;\n\nclass %1\$sFacadeProvider extends ServiceProvider{\n\n\tpublic function boot(){}\n\n\tpublic function register()\n\t{\n\t\t\$this->app->bind('%2\$s', 'App\Model\Facades\%1\$sFacade');\n\t}\n}";
    protected $FACADE_CLASS_TEMPLATE = "<?php\n\nnamespace App\Model\Facades;\n\nclass %1\$s {}";
    

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $this->absPath = env('PATH_TO_PUBLIC');
        $this->createFacadeTemplate();  
        $this->createFacadeClass();            
        $this->createFacadeProvider();      

        $this->updateConfigApp();
        system('composer dump-autoload');       
    }

    private function updateConfigApp()
    {

        $str=file_get_contents($this->absPath . 'config/app.php');
        $name = "App\\Model\\Providers\\Facades\\" . $this->argument('name') . "FacadeProvider::class,";
        $str=str_replace("/* FACADES */", "/* FACADES */\n".$name, $str);

        file_put_contents($this->absPath . 'config/app.php', $str);

    }

    public function createFacadeTemplate()
    {

        $filename = $this->argument('name') . 'Facade.php';
        $facade = $this->absPath . 'app/Model/Facades/'.$filename;

        $this->printWhiteText("Creating " .$filename."... ");

        if(!file_exists($facade)) {
            $creation = new Process(['touch', $facade]);
            $creation->run();
            $this->printGreenText($filename." created!");
            $this->printWhiteText($filename." filling with template... ");
            if (!($fp = fopen($formatterInterface, 'w'))) {
                $this->printRedText("Cannot open ".$filename." for writing");
                return;
            }
            fprintf($fp, $this->FACADE_TEMPLATE, $this->argument('name'), strtolower($this->argument('name')));
            $this->printGreenText($filename." filled with template");
        }else{
            $this->printRedText($filename." already exists");
        }

    }

    public function createFacadeClass()
    {

        $filename = $this->argument('name') . '.php';
        $facade = $this->absPath . 'app/Model/Facades/'. $filename;
        $this->printWhiteText("Creating " .$filename."... ");
        if(!file_exists($facade)) {
            $creation = new Process(['touch', $facade]);
            $creation->run();           
            $this->printGreenText($filename." created!");
            $this->printWhiteText($filename." filling with template... ");
            if (!($fp = fopen($facade, 'w'))) {
                $this->printRedText("Cannot open ".$filename." for writing");
                return;
            }
            fprintf($fp, $this->FACADE_CLASS_TEMPLATE, $this->argument('name'));
            $this->printGreenText($filename." filled with template");            
        }else{
            $this->printRedText($filename." already exists");
        }

    } 
    
    public function createFacadeProvider()
    {

        $filename = $this->argument('name') . 'FacadeProvider.php';
        $facadeProvider = $this->absPath . 'app/Model/Providers/Facades/'. $filename;
        $this->facadeProvider = $facadeProvider;
        $this->printWhiteText("Creating " .$filename."... ");
        if(!file_exists($facadeProvider)) {
            $creation = new Process(['touch', $facadeProvider]);
            $creation->run();             
            $this->printGreenText($filename." created!");
            $this->printWhiteText($filename." filling with template... ");
            if (!($fp = fopen($facadeProvider, 'w'))) {
                $this->printRedText("Cannot open ".$filename." for writing");
                return;
            }
            fprintf($fp, $this->FORMATTER_PROVIDER_TEMPLATE, $this->argument('name'), strtolower($this->argument('name')));
            $this->printGreenText($filename." filled with template");            
        }else{
            $this->printRedText($filename." already exists");
        }

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