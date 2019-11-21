<?php

namespace Resera\ConsoleCommands\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;

class GenerateResource extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:resource {name} {--table=} {--subsystem=}';
    protected $absPath;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates all classes.';

    protected $REPOSITORY_INTERFACE_TEMPLATE = "<?php\n\nnamespace App\Model\Contracts\Interfaces\Data;\n\ninterface %sRepositoryInterface\n{\n}";
    protected $REPOSITORY_TEMPLATE = "<?php\n\nnamespace App\Model\Data\Repositories;\n\nuse App\Model\Contracts\AbstractClasses\Repository;\nuse App\Model\Contracts\Interfaces\Data\%1\$sRepositoryInterface;\nuse App\Model\Data\Models\%1\$s;\n\nclass %1\$sRepository extends Repository implements %1\$sRepositoryInterface\n{\n\nprotected \$modelInstance = %1\$s::class;\n\n}";
    protected $REPOSITORY_PROVIDER_TEMPLATE = "<?php\n\nnamespace App\Model\Providers\Data;\n\nuse Illuminate\Support\ServiceProvider;\n\nclass %1\$sRepositoryProvider extends ServiceProvider{\n\n\tpublic function boot(){}\n\n\tpublic function register()\n\t{\n\t\t\$this->app->bind('App\Model\Contracts\Interfaces\Data\%1\$sRepositoryInterface', 'App\Model\Data\Repositories\%1\$sRepository');\n\t}\n}";
    protected $MODEL_TEMPLATE = "<?php\n\nnamespace App\Model\Data\Models;\n\nuse Illuminate\Database\Eloquent\Model;\n\nclass %1\$s extends Model{\n\n\tprotected \$guarded = ['id'];\n\tprotected \$table = '%2\$s';\n\n}";

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

        $this->createMigration();        
        $this->createSeeder();
        $this->createModel();
        $this->createRepositoryInterface();  
        $this->createRepository();            
        $this->createRepositoryProvider();      

        $this->updateConfigApp();
        system('composer dump-autoload');

    }

    private function updateConfigApp()
    {

        //read the entire string
        $str=file_get_contents($this->absPath . 'config/app.php');

        $name = "App\\Model\\Providers\\Data\\" . $this->argument('name') . "RepositoryProvider::class,";

        //replace something in the file string - this is a VERY simple example
        $str=str_replace("/* REPOSITORIES */", "/* REPOSITORIES */\n".$name, $str);

        //write the entire string
        file_put_contents($this->absPath . 'config/app.php', $str);

    }

    public function createModel()
    {

        $tableName = null !== $this->option('table') ? $this->option('table') : strtolower($this->argument('name')).'s';
        $filename = $this->argument('name') . '.php';
        $modelFile = $this->absPath . 'app/Model/Data/Models/'. $filename;
        $this->printWhiteText("Creating " .$filename."... ");
        if(!file_exists($modelFile)) {
            $creation = new Process('touch '.$modelFile);
            $creation->run();            
            $this->printGreenText($filename." created!");
            $this->printWhiteText($filename." filling with template... ");
            if (!($fp = fopen($modelFile, 'w'))) {
                $this->printRedText("Cannot open ".$filename." for writing");
                return;
            }
            fprintf($fp, $this->MODEL_TEMPLATE, $this->argument('name'), $tableName);
            $this->printGreenText($filename." filled with template");
        }else{
            $this->printRedText($filename." already exists");
        }

    }

    /* Creates repository interface and fills it with template */
    public function createRepositoryInterface()
    {

        $filename = $this->argument('name') . 'RepositoryInterface.php';
        $repositoryInterface = $this->absPath . 'app/Model/Contracts/Interfaces/Data/'.$filename;

        $this->printWhiteText("Creating " .$filename."... ");

        if(!file_exists($repositoryInterface)) {
            $creation = new Process('touch '.$repositoryInterface);
            $creation->run();
            $this->printGreenText($filename." created!");
            $this->printWhiteText($filename." filling with template... ");
            if (!($fp = fopen($repositoryInterface, 'w'))) {
                $this->printRedText("Cannot open ".$filename." for writing");
                return;
            }
            fprintf($fp, $this->REPOSITORY_INTERFACE_TEMPLATE, $this->argument('name'));
            $this->printGreenText($filename." filled with template");
        }else{
            $this->printRedText($filename." already exists");
        }

    }

    public function createRepository()
    {

        $filename = $this->argument('name') . 'Repository.php';
        $repository = $this->absPath . 'app/Model/Data/Repositories/'. $filename;
        $this->printWhiteText("Creating " .$filename."... ");
        if(!file_exists($repository)) {
            $creation = new Process('touch '.$repository);
            $creation->run();           
            $this->printGreenText($filename." created!");
            $this->printWhiteText($filename." filling with template... ");
            if (!($fp = fopen($repository, 'w'))) {
                $this->printRedText("Cannot open ".$filename." for writing");
                return;
            }
            fprintf($fp, $this->REPOSITORY_TEMPLATE, $this->argument('name'));
            $this->printGreenText($filename." filled with template");            
        }else{
            $this->printRedText($filename." already exists");
        }

    } 
    
    public function createRepositoryProvider()
    {

        $filename = $this->argument('name') . 'RepositoryProvider.php';
        $repositoryProvider = $this->absPath . 'app/Model/Providers/Data/'. $filename;
        $this->printWhiteText("Creating " .$filename."... ");
        if(!file_exists($repositoryProvider)) {
            $creation = new Process('touch '.$repositoryProvider);
            $creation->run();             
            $this->printGreenText($filename." created!");
            $this->printWhiteText($filename." filling with template... ");
            if (!($fp = fopen($repositoryProvider, 'w'))) {
                $this->printRedText("Cannot open ".$filename." for writing");
                return;
            }
            fprintf($fp, $this->REPOSITORY_PROVIDER_TEMPLATE, $this->argument('name'));
            $this->printGreenText($filename." filled with template");            
        }else{
            $this->printRedText($filename." already exists");
        }

    }      

    public function createMigration()
    {

        $tableName = null !== $this->option('table') ? $this->option('table') : strtolower($this->argument('name')).'s';
        $migrationFile = $this->absPath."/database/migrations/*_".$tableName."_*";
        $this->printWhiteText("Creating migration create_" . $tableName . "_table.php... ");
        foreach (glob($migrationFile) as $filefound) {
            $this->printRedText("Migration already exists");
            return;
        }        
        Artisan::call('make:migration',
            [
                'name' => 'create_'.$tableName.'_table'
            ]
        );
        $this->printGreenText("Migration created");

    }

    public function createSeeder()
    {

        $tableName = null !== $this->option('table') ? $this->option('table') : strtolower($this->argument('name')).'s';
        $tableName = ucfirst($tableName);
        $filename = $tableName .'TableSeeder.php';
        $filenameWithoutExtension = $tableName .'TableSeeder';
        $seederFile = $this->absPath . '/database/seeds/'. $filename;
        $this->printWhiteText("Creating " .$filename."... ");
        if(!file_exists($seederFile)) {
            Artisan::call('make:seeder',
                [
                    'name' => $filenameWithoutExtension
                ]
            );
            $this->printGreenText($filename." created!");
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