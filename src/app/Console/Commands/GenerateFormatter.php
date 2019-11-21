<?php

namespace Resera\ConsoleCommands\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;

class GenerateFormatter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:formatter {name} {--subsystem=}';
    protected $absPath;

    private $formatterProvider;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $FORMATTER_INTERFACE_TEMPLATE = "<?php\n\nnamespace App\Model\Contracts\Interfaces\Formatters\%1\$s;\n\ninterface %2\$sFormatterInterface\n{\n}";
    protected $FORMATTER_TEMPLATE = "<?php\n\nnamespace App\Model\Formatters\%1\$s;\n\nuse App\Model\Contracts\AbstractClasses\Formatter;\nuse App\Model\Contracts\Interfaces\Formatters\%1\$s\%2\$sFormatterInterface;\n\nclass %2\$sFormatter extends Formatter implements %2\$sFormatterInterface\n{\n\n\t/**\n     * Prepares single item for display.\n     *\n     * @param App\Model\Data\Models\ \$item\n     * @return array\n     */\n    protected function prepareItemForDisplay(\$item){}\n\n}";
    protected $FORMATTER_PROVIDER_TEMPLATE = "<?php\n\nnamespace App\Model\Providers\Formatters\%1\$s;\n\nuse Illuminate\Support\ServiceProvider;\n\nclass %2\$sFormatterProvider extends ServiceProvider{\n\n\tpublic function boot(){}\n\n\tpublic function register()\n\t{\n\t\t\$this->app->bind('App\Model\Contracts\Interfaces\Formatters\%1\$s\%2\$sFormatterInterface', 'App\Model\Formatters\%1\$s\%2\$sFormatter');\n\t}\n}";
    

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $this->absPath = env('PATH_TO_PUBLIC');
        $this->createFormatterInterface();  
        $this->createFormatter();            
        $this->createFormatterProvider();      

        $this->updateConfigApp();
        system('composer dump-autoload');       
    }

    private function updateConfigApp()
    {

        $str=file_get_contents($this->absPath . 'config/app.php');
        $name = "App\\Model\\Providers\\Formatters\\" . $this->option('subsystem') . "\\" . $this->argument('name') . "FormatterProvider::class,";
        $str=str_replace("/* FORMATTERS */", "/* FORMATTERS */\n".$name, $str);

        file_put_contents($this->absPath . 'config/app.php', $str);

    }

    public function createFormatterInterface()
    {

        $subsystemName = $this->option('subsystem');
        $filename = $this->argument('name') . 'FormatterInterface.php';
        $formatterInterface = $this->absPath . 'app/Model/Contracts/Interfaces/Formatters/'.$subsystemName.'/'.$filename;

        $this->printWhiteText("Creating " .$filename."... ");

        if(!file_exists($formatterInterface)) {
            $creation = new Process('touch '.$formatterInterface);
            $creation->run();
            $this->printGreenText($filename." created!");
            $this->printWhiteText($filename." filling with template... ");
            if (!($fp = fopen($formatterInterface, 'w'))) {
                $this->printRedText("Cannot open ".$filename." for writing");
                return;
            }
            fprintf($fp, $this->FORMATTER_INTERFACE_TEMPLATE, $this->option('subsystem'), $this->argument('name'));
            $this->printGreenText($filename." filled with template");
        }else{
            $this->printRedText($filename." already exists");
        }

    }

    public function createFormatter()
    {

        $subsystemName = $this->option('subsystem');
        $filename = $this->argument('name') . 'Formatter.php';
        $formatter = $this->absPath . 'app/Model/Formatters/'. $subsystemName . '/' . $filename;
        $this->printWhiteText("Creating " .$filename."... ");
        if(!file_exists($formatter)) {
            $creation = new Process('touch '.$formatter);
            $creation->run();           
            $this->printGreenText($filename." created!");
            $this->printWhiteText($filename." filling with template... ");
            if (!($fp = fopen($formatter, 'w'))) {
                $this->printRedText("Cannot open ".$filename." for writing");
                return;
            }
            fprintf($fp, $this->FORMATTER_TEMPLATE, $this->option('subsystem'), $this->argument('name'));
            $this->printGreenText($filename." filled with template");            
        }else{
            $this->printRedText($filename." already exists");
        }

    } 
    
    public function createFormatterProvider()
    {

        $subsystemName = $this->option('subsystem');
        $filename = $this->argument('name') . 'FormatterProvider.php';
        $formatterProvider = $this->absPath . 'app/Model/Providers/Formatters/'. $subsystemName . '/' . $filename;
        $this->formatterProvider = $formatterProvider;
        $this->printWhiteText("Creating " .$filename."... ");
        if(!file_exists($formatterProvider)) {
            $creation = new Process('touch '.$formatterProvider);
            $creation->run();             
            $this->printGreenText($filename." created!");
            $this->printWhiteText($filename." filling with template... ");
            if (!($fp = fopen($formatterProvider, 'w'))) {
                $this->printRedText("Cannot open ".$filename." for writing");
                return;
            }
            fprintf($fp, $this->FORMATTER_PROVIDER_TEMPLATE, $this->option('subsystem'), $this->argument('name'));
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