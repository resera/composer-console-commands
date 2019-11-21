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

        $this->printGreenText("Boilerplate code generated successfully!");

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

        $this->createAbstractClasses();

    }

    private function createAbstractClasses()
    {

        $REPOSITORY_TEMPLATE = "<?php\n\nnamespace App\Model\Contracts\AbstractClasses;\n\nuse App\Model\Contracts\Interfaces\AbstractClasses\RepositoryInterface;\n\nclass Repository implements RepositoryInterface\n{\n\n    protected \$model;\n    protected \$modelInstance;\n\n    public function __construct() \n\t{\n\n        \$this->makeModel();\n        \n    }\n    \n    public function model()\n    {\n\n        return \$this->modelInstance;\n\n    }\n    \n\t/**\n\t * Makes model.\n     * \n\t * @return Illuminate\Database\Eloquent\Model\n\t */\n\tpublic function makeModel() \n\t{\n\n        \$model = \App::make(\$this->model());\n        \n        return \$this->model = \$model->newQuery();\n        \n    }\n    \n    /**\n     * Creates new item.\n     *\n     * @param array \$args\n     * @return Illuminate\Database\Eloquent\Model\n     */\n    public function create(\$args)\n    {\n\n        \$instance = new \$this->modelInstance;\n        \$instance->fill(\$args);\n        \$instance->save();\n\n        return \$instance;\n\n    }\n\n    /**\n     * Gets item by an ID.\n     *\n     * @param integer \$id\n     * @return Illuminate\Database\Eloquent\Model\n     */\n    public function get(\$id)\n    {\n\n        return \$this->makeModel()->findOrFail(\$id);\n\n    }\n\n    /**\n     * Deletes given item.\n     *\n     * @param integer \$id\n     * @return void\n     */\n    public function delete(\$id)\n    {\n\n        \$this->makeModel()->delete(\$id);\n\n    }\n\n    /**\n     * Updates given item.\n     *\n     * @param integer \$id\n     * @param array \$args\n     * @return boolean\n     */\n    public function update(\$id, \$args)\n    {\n\n        \$model = \$this->makeModel()->findOrFail(\$id);\n        \n        if(\$model)\n        {\n\n            \$model->update(\$args);\n            return true;\n\n        }\n\n        return false;\n\n    }\n\n}";

        $repositoryClass = $this->absPath . 'app/Model/Contracts/AbstractClasses/Repository.php';

        $this->printWhiteText("Creating Repository.php... ");

        if(!file_exists($repositoryClass)) {
            $creation = new Process('touch '.$repositoryClass);
            $creation->run();
            $this->printGreenText("Repository created!");
            $this->printWhiteText("Repository filling with template... ");
            if (!($fp = fopen($repositoryClass, 'w'))) {
                $this->printRedText("Cannot open Repository for writing");
                return;
            }
            fprintf($fp, $REPOSITORY_TEMPLATE);
            $this->printGreenText("Repository filled with template");
        }else{
            $this->printRedText("Repository already exists");
        }        


    }

    private function createDir($path)
    {

        $targetPath = $this->absPath . $path;

        $this->printWhiteText("Creating " . $targetPath . "...");

        if(file_exists($targetPath))
        {

            $this->printRedText($targetPath . " already exists.");
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