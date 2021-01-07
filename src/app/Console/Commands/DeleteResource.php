<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;

class DeleteResource extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:resource {name} {--table=} {--subsystem=}';
    protected $absPath;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes all classes.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->absPath = env('PATH_TO_PUBLIC');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
 
        $this->deleteSeeder();
        $this->deleteModel();
        $this->deleteRepositoryInterface();  
        $this->deleteRepository();            
        $this->deleteRepositoryProvider();      

        $this->deleteServiceInterface();  
        $this->deleteService();            
        $this->deleteServiceProvider();  

        $this->deleteFormatterInterface();  
        $this->deleteFormatter();            
        $this->deleteFormatterProvider();      

        $this->deleteValidatorInterface();  
        $this->deleteValidator();            
        $this->deleteValidatorProvider();

        $this->updateConfigApp();
        system('composer dump-autoload');

    }
    public function deleteValidatorInterface()
    {

        $subsystemName = $this->option('subsystem');
        $filename = $this->argument('name') . 'ValidatorInterface.php';
        $validatorInterface = $this->absPath . 'app/Model/Contracts/Interfaces/Validators/'.$subsystemName.'/'.$filename;

        $this->printWhiteText("Deleting " .$filename."... ");

        if(file_exists($validatorInterface)) {
            unlink($validatorInterface);
            $this->printGreenText($filename." deleted!");
        }else{
            $this->printRedText($filename." does not exists");
        }

    }

    public function deleteValidator()
    {

        $subsystemName = $this->option('subsystem');
        $filename = $this->argument('name') . 'Validator.php';
        $validator = $this->absPath . 'app/Model/Validators/'. $subsystemName . '/' . $filename;
        $this->printWhiteText("Deleting " .$filename."... ");
        if(file_exists($validator)) {
            unlink($validator);
            $this->printGreenText($filename." deleted!");
        }else{
            $this->printRedText($filename." does not exists");
        }

    } 
    
    public function deleteValidatorProvider()
    {

        $subsystemName = $this->option('subsystem');
        $filename = $this->argument('name') . 'ValidatorProvider.php';
        $validatorProvider = $this->absPath . 'app/Model/Providers/Validators/'. $subsystemName . '/' . $filename;
        $this->printWhiteText("Deleting " .$filename."... ");
        if(file_exists($validatorProvider)) {
            unlink($validatorProvider);
            $this->printGreenText($filename." deleted!");
        }else{
            $this->printRedText($filename." does not exists");
        }

    }  

    public function deleteFormatterInterface()
    {

        $subsystemName = $this->option('subsystem');
        $filename = $this->argument('name') . 'FormatterInterface.php';
        $formatterInterface = $this->absPath . 'app/Model/Contracts/Interfaces/Formatters/'.$subsystemName.'/'.$filename;

        $this->printWhiteText("Deleting " .$filename."... ");

        if(file_exists($formatterInterface)) {
            unlink($formatterInterface);
            $this->printGreenText($filename." deleted!");
        }else{
            $this->printRedText($filename." does not exists");
        }

    }

    public function deleteFormatter()
    {

        $subsystemName = $this->option('subsystem');
        $filename = $this->argument('name') . 'Formatter.php';
        $formatter = $this->absPath . 'app/Model/Formatters/'. $subsystemName . '/' . $filename;
        $this->printWhiteText("Deleting " .$filename."... ");
        if(file_exists($formatter)) {
            unlink($formatter);
            $this->printGreenText($filename." deleted!");
        }else{
            $this->printRedText($filename." does not exists");
        }

    } 
    
    public function deleteFormatterProvider()
    {

        $subsystemName = $this->option('subsystem');
        $filename = $this->argument('name') . 'FormatterProvider.php';
        $formatterProvider = $this->absPath . 'app/Model/Providers/Formatters/'. $subsystemName . '/' . $filename;
        $this->formatterProvider = $formatterProvider;
        $this->printWhiteText("Deleting " .$filename."... ");
        if(file_exists($formatterProvider)) {
            unlink($formatterProvider);
            $this->printGreenText($filename." deleted!");
        }else{
            $this->printRedText($filename." does not exists");
        }

    }

    public function deleteServiceInterface()
    {

        $subsystemName = $this->option('subsystem');
        $filename = $this->argument('name') . 'ServiceInterface.php';
        $serviceInterface = $this->absPath . 'app/Model/Contracts/Interfaces/Services/'.$subsystemName.'/'.$filename;

        $this->printWhiteText("Deleting " .$filename."... ");

        if(file_exists($serviceInterface)) {
            unlink($serviceInterface);
            $this->printGreenText($filename." deleted!");
        }else{
            $this->printRedText($filename." does not exists");
        }

    }

    public function deleteService()
    {

        $subsystemName = $this->option('subsystem');
        $filename = $this->argument('name') . 'Service.php';
        $service = $this->absPath . 'app/Model/Services/'. $subsystemName . '/' . $filename;
        $this->printWhiteText("Deleting " .$filename."... ");
        if(file_exists($service)) {
            unlink($service);
            $this->printGreenText($filename." deleted!");
        }else{
            $this->printRedText($filename." does not exists");
        }

    } 
    
    public function deleteServiceProvider()
    {

        $subsystemName = $this->option('subsystem');
        $filename = $this->argument('name') . 'ServiceProvider.php';
        $serviceProvider = $this->absPath . 'app/Model/Providers/Services/'. $subsystemName . '/' . $filename;
        $this->printWhiteText("Deleting " .$filename."... ");
        if(file_exists($serviceProvider)) {
            unlink($serviceProvider);
            $this->printGreenText($filename." deleted!");
        }else{
            $this->printRedText($filename." does not exists");
        }

    }        

    private function updateConfigApp()
    {

        //read the entire string
        $str=file_get_contents($this->absPath . 'config/app.php');

        $name = "App\\Model\\Providers\\Data\\" . $this->argument('name') . "RepositoryProvider::class,";

        //replace something in the file string - this is a VERY simple example
        $str=str_replace($name."\n", "", $str);

        $name = "App\\Model\\Providers\\Services\\" . $this->option('subsystem') . "\\" . $this->argument('name') . "ServiceProvider::class,";
        //replace something in the file string - this is a VERY simple example
        $str=str_replace($name."\n", "", $str);

        $name = "App\\Model\\Providers\\Formatters\\" . $this->option('subsystem') . "\\" . $this->argument('name') . "FormatterProvider::class,";
        //replace something in the file string - this is a VERY simple example
        $str=str_replace($name."\n", "", $str);

        $name = "App\\Model\\Providers\\Validators\\" . $this->option('subsystem') . "\\" . $this->argument('name') . "ValidatorProvider::class,";
        //replace something in the file string - this is a VERY simple example
        $str=str_replace($name."\n", "", $str);

        //write the entire string
        file_put_contents($this->absPath . 'config/app.php', $str);

    }

    public function deleteModel()
    {

        $tableName = null !== $this->option('table') ? $this->option('table') : strtolower($this->argument('name')).'s';
        $filename = $this->argument('name') . '.php';
        $modelFile = $this->absPath . 'app/Model/Data/Models/'. $filename;
        $this->printWhiteText("Deleting " .$filename."... ");
        if(file_exists($modelFile)) {
            unlink($modelFile);
            $this->printGreenText($filename." deleted!");
        }else{
            $this->printRedText($filename." does not exists");
        }

    }

    /* Creates repository interface and fills it with template */
    public function deleteRepositoryInterface()
    {

        $filename = $this->argument('name') . 'RepositoryInterface.php';
        $repositoryInterface = $this->absPath . 'app/Model/Contracts/Interfaces/Data/'.$filename;

        $this->printWhiteText("Deleting " .$filename."... ");

        if(file_exists($repositoryInterface)) {
            unlink($repositoryInterface);
            $this->printGreenText($filename." deleted!");
        }else{
            $this->printRedText($filename." does not exists");
        }

    }

    public function deleteRepository()
    {

        $filename = $this->argument('name') . 'Repository.php';
        $repository = $this->absPath . 'app/Model/Data/Repositories/'. $filename;
        $this->printWhiteText("Deleting " .$filename."... ");
        if(file_exists($repository)) {
            unlink($repository);        
            $this->printGreenText($filename." deleted!");  
        }else{
            $this->printRedText($filename." does not exists");
        }

    } 
    
    public function deleteRepositoryProvider()
    {

        $filename = $this->argument('name') . 'RepositoryProvider.php';
        $repositoryProvider = $this->absPath . 'app/Model/Providers/Data/'. $filename;
        $this->printWhiteText("Deleting " .$filename."... ");
        if(file_exists($repositoryProvider)) {
            unlink($repositoryProvider);        
            $this->printGreenText($filename." deleted!");            
        }else{
            $this->printRedText($filename." does not exists");
        }

    }      

    public function deleteSeeder()
    {

        $tableName = null !== $this->option('table') ? $this->option('table') : strtolower($this->argument('name')).'s';
        $tableName = ucfirst($tableName);
        $filename = $tableName .'TableSeeder.php';
        $filenameWithoutExtension = $tableName .'TableSeeder';
        $seederFile = $this->absPath . '/database/seeds/'. $filename;
        $this->printWhiteText("Deleting " .$filename."... ");
        if(file_exists($seederFile)) {
            unlink($seederFile);
            $this->printGreenText($filename." deleted!");
        }else{
            $this->printRedText($filename." does not exist exists");
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