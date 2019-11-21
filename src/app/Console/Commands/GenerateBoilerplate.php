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
                    $this->createDir('app/Model/Contracts/Interfaces/AbstractClasses');
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
        $this->createAbstractInterfaces();
        $this->updateConfigApp();

    }

    private function updateConfigApp()
    {

        //read the entire string
        $str=file_get_contents($this->absPath . 'config/app.php');

        //replace something in the file string - this is a VERY simple example
        $str=str_replace("App\Providers\RouteServiceProvider::class,", "App\Providers\RouteServiceProvider::class,\n\n/* REPOSITORIES */\n\n/* FORMATTERS */\n\n/* SERVICES */\n\n/* VALIDATORS */\n\n",$str);

        //write the entire string
        file_put_contents($this->absPath . 'config/app.php', $str);

    }

    private function createAbstractInterfaces()
    {

        $this->createRepositoryInterface();
        $this->createFormatterInterface();

    }

    private function createAbstractClasses()
    {

        $this->createRepository();  
        $this->createFormatter();  
        $this->createValidator();  
        $this->createService();  
        $this->createFilter();

    }

    private function createRepository()
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

    private function createFormatter()
    {

        $FORMATTER_TEMPLATE = "<?php\n\nnamespace App\Model\Contracts\AbstractClasses;\n\nuse App\Model\Contracts\Interfaces\AbstractClasses\FormatterInterface;\n\nabstract class Formatter implements FormatterInterface\n{\n\n    /**\n     * Prepares failed response with given errors.\n     *\n     * @param mixed \$errors\n     * @return array\n     */\n    public function prepareForFailedResponse(\$errors)\n    {\n\n        \$response = [\n            'success' => false\n        ];\n\n        if(is_array(\$errors))\n        {\n\n            \$response['errors'] = \$errors;\n\n        }else{\n\n            \$response['errors'] = [\$errors];\n\n        }\n\n        return \$response;\n\n    }\n\n    /**\n     * Prepares successfully response with given message.\n     *\n     * @param mixed \$message\n     * @return array\n     */\n    public function prepareForSuccessfulResponse(\$message = null)\n    {\n\n        \$response = [\n            'success' => true,\n            'message' => \$message\n        ];\n\n        return \$response;\n\n    }\n\n    /**\n     * Prepares items for display.\n     *\n     * @param Illuminate\Support\Collection \$items\n     * @return Illuminate\Support\Collection\n     */\n    public function prepareForDisplay(\$items)\n    {\n\n        return \$items->map(function(\$item, \$key) {\n\n            return \$this->prepareItemForDisplay(\$item);\n\n        });\n\n    }\n\n    protected abstract function prepareItemForDisplay(\$item);\n\n}";

        $formatterClass = $this->absPath . 'app/Model/Contracts/AbstractClasses/Formatter.php';

        $this->printWhiteText("Creating Formatter.php... ");

        if(!file_exists($formatterClass)) {
            $creation = new Process('touch '.$formatterClass);
            $creation->run();
            $this->printGreenText("Formatter created!");
            $this->printWhiteText("Formatter filling with template... ");
            if (!($fp = fopen($formatterClass, 'w'))) {
                $this->printRedText("Cannot open Formatter for writing");
                return;
            }
            fprintf($fp, $FORMATTER_TEMPLATE);
            $this->printGreenText("Formatter filled with template");
        }else{
            $this->printRedText("Formatter already exists");
        }      

    }

    private function createValidator()
    {

        $VALIDATOR_TEMPLATE = "<?php \n\nnamespace App\Model\Contracts\AbstractClasses;\n\nclass Validator\n{\n    \n\tprotected \$validator;\n\n    /**\n     * Returns errors of currently failed validator.\n     *\n     * @return array\n     */\n\tpublic function getErrors()\n\t{\n\n        if(!\$this->validator) \n        {\n            \n            return null;\n\n        }\n\n        return \$this->validator->errors()->toArray();\n        \n\t}\t\n\t\n}\t";

        $validatorClass = $this->absPath . 'app/Model/Contracts/AbstractClasses/Validator.php';

        $this->printWhiteText("Creating Validator.php... ");

        if(!file_exists($validatorClass)) {
            $creation = new Process('touch '.$validatorClass);
            $creation->run();
            $this->printGreenText("Validator created!");
            $this->printWhiteText("Validator filling with template... ");
            if (!($fp = fopen($validatorClass, 'w'))) {
                $this->printRedText("Cannot open Validator for writing");
                return;
            }
            fprintf($fp, $VALIDATOR_TEMPLATE);
            $this->printGreenText("Validator filled with template");
        }else{
            $this->printRedText("Validator already exists");
        }      

    }

    private function createService()
    {

        $SERVICE_TEMPLATE = "<?php \n\nnamespace App\Model\Contracts\AbstractClasses;\n\nuse Illuminate\Support\Str;\nuse App;\n\nclass Service\n{\n    \n    private \$subsystem;\n    private \$className;\n\n\tprotected \$validator;\n\tprotected \$formatter;\n    protected \$repository;\n\n    /**\n     * Generates common resources like repository, formatter, validator.\n     *\n     * @param string \$namespace\n     * @return void\n     */\n    protected function generateResources(\$namespace)\n    {\n\n\n        \$parts = explode('\\', \$namespace);\n\n        \$this->subsystem = \$parts[count(\$parts)-1];\n        \$this->className = \$this->resourceName();\n\n        \$this->generateRepository();\n        \$this->generateFormatter();\n        \$this->generateValidator();\n\n    }\n\n    /**\n     * Generates new repository if possible.\n     *\n     * @return void\n     */\n    private function generateRepository()\n    {\n\n        try {\n            \n            \$this->repository = App::make('App\Model\Contracts\Interfaces\Data\\'.\$this->className.'RepositoryInterface');\n\n\n        }catch(\Exception \$e){}\n\n    }\n\n    /**\n     * Generates new formatter if possible.\n     *\n     * @return void\n     */    \n    private function generateFormatter()\n    {\n\n        try {\n\n            \$this->formatter = App::make('App\Model\Contracts\Interfaces\Formatters\\'.\$this->subsystem.'\\'.\$this->className.'FormatterInterface');\n\n        }catch(\Exception \$e){}\n\n    }\n\n    /**\n     * Generates new validator if possible.\n     *\n     * @return void\n     */\n    private function generateValidator()\n    {\n\n        try {\n\n            \$this->validator = App::make('App\Model\Contracts\Interfaces\Validators\\'.\$this->subsystem.'\\'.\$this->className.'ValidatorInterface');\n\n        }catch(\Exception \$e){}\n\n    }\n    \n    /**\n     * Returns resource name.\n     *\n     * @return void\n     */    \n    private function resourceName()\n    {\n\n        return explode('Service', class_basename(\$this))[0];\n\n    }\n\t\n}\t";

        $serviceClass = $this->absPath . 'app/Model/Contracts/AbstractClasses/Service.php';

        $this->printWhiteText("Creating Service.php... ");

        if(!file_exists($serviceClass)) {
            $creation = new Process('touch '.$serviceClass);
            $creation->run();
            $this->printGreenText("Service created!");
            $this->printWhiteText("Service filling with template... ");
            if (!($fp = fopen($serviceClass, 'w'))) {
                $this->printRedText("Cannot open Service for writing");
                return;
            }
            fprintf($fp, $SERVICE_TEMPLATE);
            $this->printGreenText("Service filled with template");
        }else{
            $this->printRedText("Service already exists");
        }      

    }  
    
    private function createFilter()
    {

        $FILTER_TEMPLATE = "<?php\n\nnamespace App\Model\Contracts\AbstractClasses;\n\nuse Str;\n\nabstract class Filter \n{\n\n    /**\n     * Handles a single filter.\n     *\n     * @param Illuminate\Database\Query\Builder \$request\n     * @param App\Model\Contracts\AbstractClasses\Filter \$next\n     * @return App\Model\Contracts\AbstractClasses\Filter\n     */\n    public function handle(\$request, \$next)\n    {\n\n        \$filterName = \$this->filterName();\n\n        if(!request()->has(\$filterName) || !request()->\$filterName)\n        {\n\n            return \$next(\$request);\n\n        }\n\n        return \$this->applyFilter(\$next(\$request));\n\n    }\n\n    /**\n     * Converts class name to snake case.\n     *\n     * @return string\n     */\n    protected function filterName()\n    {\n\n        return Str::snake(class_basename(\$this));\n\n    }\n\n    protected abstract function applyFilter(\$builder);\n\n}";

        $filterClass = $this->absPath . 'app/Model/Contracts/AbstractClasses/Filter.php';

        $this->printWhiteText("Creating Filter.php... ");

        if(!file_exists($filterClass)) {
            $creation = new Process('touch '.$filterClass);
            $creation->run();
            $this->printGreenText("Filter created!");
            $this->printWhiteText("Filter filling with template... ");
            if (!($fp = fopen($filterClass, 'w'))) {
                $this->printRedText("Cannot open Filter for writing");
                return;
            }
            fprintf($fp, $FILTER_TEMPLATE);
            $this->printGreenText("Filter filled with template");
        }else{
            $this->printRedText("Filter already exists");
        }      

    }   
    
    private function createFormatterInterface()
    {

        $FORMATTER_INTERFACE_TEMPLATE = "<?php\n\nnamespace App\Model\Contracts\Interfaces\AbstractClasses;\n\ninterface FormatterInterface\n{\n\n    public function prepareForFailedResponse(\$errors);\n\n    public function prepareForSuccessfulResponse(\$message);\n\n    public function prepareForDisplay(\$items);\n\n}";

        $formatterInterfaceClass = $this->absPath . 'app/Model/Contracts/Interfaces/AbstractClasses/FormatterInterface.php';

        $this->printWhiteText("Creating FormatterInterface.php... ");

        if(!file_exists($formatterInterfaceClass)) {
            $creation = new Process('touch '.$formatterInterfaceClass);
            $creation->run();
            $this->printGreenText("FormatterInterface created!");
            $this->printWhiteText("FormatterInterface filling with template... ");
            if (!($fp = fopen($formatterInterfaceClass, 'w'))) {
                $this->printRedText("Cannot open FormatterInterface for writing");
                return;
            }
            fprintf($fp, $FORMATTER_INTERFACE_TEMPLATE);
            $this->printGreenText("FormatterInterface filled with template");
        }else{
            $this->printRedText("FormatterInterface already exists");
        }      

    }  

    private function createRepositoryInterface()
    {

        $REPOSITORY_INTERFACE_TEMPLATE = "<?php\n\nnamespace App\Model\Contracts\Interfaces\AbstractClasses;\n\ninterface RepositoryInterface\n{\n\n    public function create(\$args);\n\n    public function update(\$id, \$args);\n\n    public function delete(\$id);\n\n    public function get(\$id);\n\n}";

        $repositoryInterfaceClass = $this->absPath . 'app/Model/Contracts/Interfaces/AbstractClasses/RepositoryInterface.php';

        $this->printWhiteText("Creating RepositoryInterface.php... ");

        if(!file_exists($repositoryInterfaceClass)) {
            $creation = new Process('touch '.$repositoryInterfaceClass);
            $creation->run();
            $this->printGreenText("RepositoryInterface created!");
            $this->printWhiteText("RepositoryInterface filling with template... ");
            if (!($fp = fopen($repositoryInterfaceClass, 'w'))) {
                $this->printRedText("Cannot open RepositoryInterface for writing");
                return;
            }
            fprintf($fp, $REPOSITORY_INTERFACE_TEMPLATE);
            $this->printGreenText("RepositoryInterface filled with template");
        }else{
            $this->printRedText("RepositoryInterface already exists");
        }      

    }  
    
    private function createFormatterInterface()
    {

        $FORMATTER_INTERFACE_TEMPLATE = "<?php\n\nnamespace App\Model\Contracts\Interfaces\AbstractClasses;\n\ninterface FormatterInterface\n{\n\n    public function prepareForFailedResponse(\$errors);\n\n    public function prepareForSuccessfulResponse(\$message);\n\n    public function prepareForDisplay(\$items);\n\n}";

        $formatterInterfaceClass = $this->absPath . 'app/Model/Contracts/Interfaces/AbstractClasses/FormatterInterface.php';

        $this->printWhiteText("Creating FormatterInterface.php... ");

        if(!file_exists($formatterInterfaceClass)) {
            $creation = new Process('touch '.$formatterInterfaceClass);
            $creation->run();
            $this->printGreenText("FormatterInterface created!");
            $this->printWhiteText("FormatterInterface filling with template... ");
            if (!($fp = fopen($formatterInterfaceClass, 'w'))) {
                $this->printRedText("Cannot open FormatterInterface for writing");
                return;
            }
            fprintf($fp, $FORMATTER_INTERFACE_TEMPLATE);
            $this->printGreenText("FormatterInterface filled with template");
        }else{
            $this->printRedText("FormatterInterface already exists");
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