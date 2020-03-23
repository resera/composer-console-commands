<?php

namespace Resera\ConsoleCommands\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;

class GenerateService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:service {name} {--subsystem=}';
    protected $absPath;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates services';

    protected $SERVICE_INTERFACE_TEMPLATE = "<?php\n\nnamespace App\Model\Contracts\Interfaces\Services\%1\$s;\n\ninterface %2\$sServiceInterface\n{\n}";
    protected $SERVICE_TEMPLATE = "<?php\n\nnamespace App\Model\Services\%1\$s;\n\nuse App\Model\Contracts\AbstractClasses\Service;\nuse App\Model\Contracts\Interfaces\Services\%1\$s\%2\$sServiceInterface;\n\nclass %2\$sService extends Service implements %2\$sServiceInterface\n{\n\n    public function __construct()\n    {\n\n        \$this->generateResources(__NAMESPACE__);\n\n    }\n\n}";
    protected $SERVICE_PROVIDER_TEMPLATE = "<?php\n\nnamespace App\Model\Providers\Services\%1\$s;\n\nuse Illuminate\Support\ServiceProvider;\n\nclass %2\$sServiceProvider extends ServiceProvider{\n\n\tpublic function boot(){}\n\n\tpublic function register()\n\t{\n\t\t\$this->app->bind('App\Model\Contracts\Interfaces\Services\%1\$s\%2\$sServiceInterface', 'App\Model\Services\%1\$s\%2\$sService');\n\t}\n}";

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

        $this->createServiceInterface();  
        $this->createService();            
        $this->createServiceProvider();      

        $this->updateConfigApp();
        system('composer dump-autoload');      
    }

    private function updateConfigApp()
    {

        //read the entire string
        $str=file_get_contents($this->absPath . 'config/app.php');

        $name = "App\\Model\\Providers\\Services\\" . $this->option('subsystem') . "\\" . $this->argument('name') . "ServiceProvider::class,";

        //replace something in the file string - this is a VERY simple example
        $str=str_replace("/* SERVICES */", "/* SERVICES */\n".$name, $str);

        //write the entire string
        file_put_contents($this->absPath . 'config/app.php', $str);

    }

    public function createServiceInterface()
    {

        $subsystemName = $this->option('subsystem');
        $filename = $this->argument('name') . 'ServiceInterface.php';
        $serviceInterface = $this->absPath . 'app/Model/Contracts/Interfaces/Services/'.$subsystemName.'/'.$filename;

        $this->printWhiteText("Creating " .$filename."... ");

        if(!file_exists($serviceInterface)) {
            $creation = new Process(['touch', $serviceInterface]);
            $creation->run();
            $this->printGreenText($filename." created!");
            $this->printWhiteText($filename." filling with template... ");
            if (!($fp = fopen($serviceInterface, 'w'))) {
                $this->printRedText("Cannot open ".$filename." for writing");
                return;
            }
            fprintf($fp, $this->SERVICE_INTERFACE_TEMPLATE, $this->option('subsystem'), $this->argument('name'));
            $this->printGreenText($filename." filled with template");
        }else{
            $this->printRedText($filename." already exists");
        }

    }

    public function createService()
    {

        $subsystemName = $this->option('subsystem');
        $filename = $this->argument('name') . 'Service.php';
        $service = $this->absPath . 'app/Model/Services/'. $subsystemName . '/' . $filename;
        $this->printWhiteText("Creating " .$filename."... ");
        if(!file_exists($service)) {
            $creation = new Process(['touch', $service]);
            $creation->run();           
            $this->printGreenText($filename." created!");
            $this->printWhiteText($filename." filling with template... ");
            if (!($fp = fopen($service, 'w'))) {
                $this->printRedText("Cannot open ".$filename." for writing");
                return;
            }
            fprintf($fp, $this->SERVICE_TEMPLATE, $this->option('subsystem'), $this->argument('name'));
            $this->printGreenText($filename." filled with template");            
        }else{
            $this->printRedText($filename." already exists");
        }

    } 
    
    public function createServiceProvider()
    {

        $subsystemName = $this->option('subsystem');
        $filename = $this->argument('name') . 'ServiceProvider.php';
        $serviceProvider = $this->absPath . 'app/Model/Providers/Services/'. $subsystemName . '/' . $filename;
        $this->printWhiteText("Creating " .$filename."... ");
        if(!file_exists($serviceProvider)) {
            $creation = new Process(['touch', $serviceProvider]);
            $creation->run();             
            $this->printGreenText($filename." created!");
            $this->printWhiteText($filename." filling with template... ");
            if (!($fp = fopen($serviceProvider, 'w'))) {
                $this->printRedText("Cannot open ".$filename." for writing");
                return;
            }
            fprintf($fp, $this->SERVICE_PROVIDER_TEMPLATE, $this->option('subsystem'), $this->argument('name'));
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