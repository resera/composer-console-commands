<?php

namespace Resera\ConsoleCommands\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;

class GenerateValidator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:validator {name} {--subsystem=}';
    protected $absPath;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $VALIDATOR_INTERFACE_TEMPLATE = "<?php\n\nnamespace App\Model\Contracts\Interfaces\Validators\%1\$s;\n\ninterface %2\$sValidatorInterface\n{\n}";
    protected $VALIDATOR_TEMPLATE = "<?php\n\nnamespace App\Model\Validators\%1\$s;\n\nuse App\Model\Contracts\Interfaces\Validators\%1\$s\%2\$sValidatorInterface;\n\nclass %2\$sValidator implements %2\$sValidatorInterface\n{\n}";
    protected $VALIDATOR_PROVIDER_TEMPLATE = "<?php\n\nnamespace App\Model\Providers\Validators\%1\$s;\n\nuse Illuminate\Support\ServiceProvider;\n\nclass %2\$sValidatorProvider extends ServiceProvider{\n\n\tpublic function boot(){}\n\n\tpublic function register()\n\t{\n\t\t\$this->app->bind('App\Model\Contracts\Interfaces\Validators\%1\$s\%2\$sValidatorInterface', 'App\Model\Validators\%1\$s\%2\$sValidator');\n\t}\n}";
    

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $this->absPath = env('PATH_TO_PUBLIC');
        $this->createValidatorInterface();  
        $this->createValidator();            
        $this->createValidatorProvider();
        
        $this->updateConfigApp();
        system('composer dump-autoload');
        echo "Add provider to config/app.php to make it work.\n";         
    }

    private function updateConfigApp()
    {

        //read the entire string
        $str=file_get_contents($this->absPath . 'config/app.php');

        $name = "App\\Model\\Providers\\Validators\\" . $this->option('subsystem') . "\\" . $this->argument('name') . "ValidatorProvider::class";

        //replace something in the file string - this is a VERY simple example
        $str=str_replace("/* VALIDATORS */", "/* VALIDATORS */\n".$name, $str);

        //write the entire string
        file_put_contents($this->absPath . 'config/app.php', $str);

    }

    public function createValidatorInterface()
    {

        $subsystemName = $this->option('subsystem');
        $filename = $this->argument('name') . 'ValidatorInterface.php';
        $validatorInterface = $this->absPath . 'app/Model/Contracts/Interfaces/Validators/'.$subsystemName.'/'.$filename;

        $this->printWhiteText("Creating " .$filename."... ");

        if(!file_exists($validatorInterface)) {
            $creation = new Process('touch '.$validatorInterface);
            $creation->run();
            $this->printGreenText($filename." created!");
            $this->printWhiteText($filename." filling with template... ");
            if (!($fp = fopen($validatorInterface, 'w'))) {
                $this->printRedText("Cannot open ".$filename." for writing");
                return;
            }
            fprintf($fp, $this->VALIDATOR_INTERFACE_TEMPLATE, $this->option('subsystem'), $this->argument('name'));
            $this->printGreenText($filename." filled with template");
        }else{
            $this->printRedText($filename." already exists");
        }

    }

    public function createValidator()
    {

        $subsystemName = $this->option('subsystem');
        $filename = $this->argument('name') . 'Validator.php';
        $validator = $this->absPath . 'app/Model/Validators/'. $subsystemName . '/' . $filename;
        $this->printWhiteText("Creating " .$filename."... ");
        if(!file_exists($validator)) {
            $creation = new Process('touch '.$validator);
            $creation->run();           
            $this->printGreenText($filename." created!");
            $this->printWhiteText($filename." filling with template... ");
            if (!($fp = fopen($validator, 'w'))) {
                $this->printRedText("Cannot open ".$filename." for writing");
                return;
            }
            fprintf($fp, $this->VALIDATOR_TEMPLATE, $this->option('subsystem'), $this->argument('name'));
            $this->printGreenText($filename." filled with template");            
        }else{
            $this->printRedText($filename." already exists");
        }

    } 
    
    public function createValidatorProvider()
    {

        $subsystemName = $this->option('subsystem');
        $filename = $this->argument('name') . 'ValidatorProvider.php';
        $validatorProvider = $this->absPath . 'app/Model/Providers/Validators/'. $subsystemName . '/' . $filename;
        $this->printWhiteText("Creating " .$filename."... ");
        if(!file_exists($validatorProvider)) {
            $creation = new Process('touch '.$validatorProvider);
            $creation->run();             
            $this->printGreenText($filename." created!");
            $this->printWhiteText($filename." filling with template... ");
            if (!($fp = fopen($validatorProvider, 'w'))) {
                $this->printRedText("Cannot open ".$filename." for writing");
                return;
            }
            fprintf($fp, $this->VALIDATOR_PROVIDER_TEMPLATE, $this->option('subsystem'), $this->argument('name'));
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