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
            $this->createDir('app/Model/Services');                
            $this->createDir('app/Model/Validators');
        $this->createDir('resources/views/pages');

        $this->createAbstractClasses();
        $this->createAbstractInterfaces();
        $this->updateConfigApp();

    }

    private function updateConfigApp()
    {

        $str=file_get_contents($this->absPath . 'config/app.php');

        $str=str_replace("App\Providers\RouteServiceProvider::class,", "App\Providers\RouteServiceProvider::class,\n\n/* REPOSITORIES */\n\n/* FORMATTERS */\n\n/* FACADES */\nApp\Model\Providers\Facades\ResponseFacadeProvider::class,\n\n/* SERVICES */\n\n/* VALIDATORS */\n\n",$str);

        file_put_contents($this->absPath . 'config/app.php', $str);

    }

    private function createAbstractInterfaces()
    {

        $this->createRepositoryInterface();
        $this->createFormatterInterface();
        $this->createCrudServiceInterface();

    }

    private function createAbstractClasses()
    {

        $this->createRepository();  
        $this->createFormatter();  
        $this->createValidator();  
        $this->createService();  
        $this->createFilter();
        $this->createController();
        $this->createConfig();
        $this->createResponse();

    }

    private function createResponse()
    {

        $RESPONSE_TEMPLATE = "<?php\n\nnamespace App\Model\Facades;\n\nclass Response\n{\n\n    const STATUS_CODES = [\n        200, 201, 202, 204, 301, 302, 303, 304, 307, 400, 401, 403, 404, 405, 406, 409, 412, 415, 422, 500, 501\n    ];\n\n    /**\n     * Sets response status code.\n     *\n     * @param string \$code\n     * @return void\n     */\n    public static function setStatusCode(\$code)\n    {\n\n        if (in_array(\$code, self::STATUS_CODES)) {\n            config(['response.status_code' => \$code]);\n        }\n    }\n\n    /**\n     * Returns response status code.\n     *\n     * @return int\n     */\n    public static function getStatusCode()\n    {\n\n        return config('response.status_code');\n    }\n}\n";

        $repositoryClass = $this->absPath . 'app/Model/Facades/Response.php';

        $this->printWhiteText("Creating Response.php... ");

        if(!file_exists($repositoryClass)) {
            $creation = new Process(['touch', $repositoryClass]);
            $creation->run();
            $this->printGreenText("Response created!");
            $this->printWhiteText("Response filling with template... ");
            if (!($fp = fopen($repositoryClass, 'w'))) {
                $this->printRedText("Cannot open Response for writing");
                return;
            }
            fprintf($fp, $RESPONSE_TEMPLATE);
            $this->printGreenText("Response filled with template");
        }else{
            $this->printRedText("Response already exists");
        }

        // Provider
        $PROVIDER_TEMPLATE = "<?php\n\nnamespace App\Model\Providers\Facades;\n\nuse Illuminate\Support\ServiceProvider;\n\nclass ResponseFacadeProvider extends ServiceProvider\n{\n\n    public function boot()\n    {\n    }\n\n    public function register()\n    {\n        \$this->app->bind('response', 'App\Model\Facades\ResponseFacade');\n    }\n}\n";

        $repositoryClass = $this->absPath . 'app/Model/Providers/Facades/ResponseFacadeProvider.php';

        $this->printWhiteText("Creating ResponseFacadeProvider.php... ");

        if(!file_exists($repositoryClass)) {
            $creation = new Process(['touch', $repositoryClass]);
            $creation->run();
            $this->printGreenText("ResponseFacadeProvider created!");
            $this->printWhiteText("ResponseFacadeProvider filling with template... ");
            if (!($fp = fopen($repositoryClass, 'w'))) {
                $this->printRedText("Cannot open ResponseFacadeProvider for writing");
                return;
            }
            fprintf($fp, $PROVIDER_TEMPLATE);
            $this->printGreenText("ResponseFacadeProvider filled with template");
        }else{
            $this->printRedText("ResponseFacadeProvider already exists");
        }  

    }

    private function createConfig()
    {

        $CONFIG_TEMPLATE = "<?php\n\nreturn [\n\n    'status_code' => 200,\n\n];";

        $repositoryClass = $this->absPath . 'config/response.php';

        $this->printWhiteText("Creating config/response.php... ");

        if(!file_exists($repositoryClass)) {
            $creation = new Process(['touch', $repositoryClass]);
            $creation->run();
            $this->printGreenText("Response created!");
            $this->printWhiteText("Response filling with template... ");
            if (!($fp = fopen($repositoryClass, 'w'))) {
                $this->printRedText("Cannot open Response for writing");
                return;
            }
            fprintf($fp, $CONFIG_TEMPLATE);
            $this->printGreenText("Response filled with template");
        }else{
            $this->printRedText("Response already exists");
        }      

    }

    private function createController()
    {

        $CONTROLLER_TEMPLATE = "<?php\n\nnamespace App\Http\Controllers;\n\nuse App\Http\Controllers\Controller;\nuse Illuminate\Http\Request;\nuse App\Model\Facades\Response;\n\nclass BaseController extends Controller\n{\n\n    protected \$service;\n\n    public function __construct()\n    {\n\n        Response::setStatusCode(200);\n    }\n\n    /**\n     * Returns response to the requester.\n     *\n     * @param array \$data\n     * @return Response\n     */\n    protected function returnResponse(\$data = [])\n    {\n\n        return response()->json(\$data, Response::getStatusCode());\n    }\n\n    /**\n     * Returns all items and prepares for display.\n     *\n     * @return void\n     */\n    public function index()\n    {\n\n        return \$this->returnResponse(\n            \$this->service->all()\n        );\n    }\n\n    /**\n     * Returns paginated display of items.\n     *\n     * @return void\n     */\n    public function paginate()\n    {\n\n        return \$this->returnResponse(\n            \$this->service->paginate()\n        );\n    }\n\n    /**\n     * Returns single item by id.\n     *\n     * @param integer \$id\n     * @return Response\n     */\n    public function show(\$id)\n    {\n\n        return \$this->returnResponse(\n            \$this->service->getFirstBy('id', \$id, true)\n        );\n    }\n\n    /**\n     * Deletes single item by id.\n     *\n     * @param integer \$id\n     * @return Response\n     */\n    public function destroy(\$id)\n    {\n\n        return \$this->returnResponse(\n            \$this->service->delete(\$id)\n        );\n    }\n\n    /**\n     * Creates new item.\n     *\n     * @param Request \$request\n     * @return Response\n     */\n    public function store(Request \$request)\n    {\n\n        return \$this->returnResponse(\n            \$this->service->create(request()->all())\n        );\n    }\n\n    /**\n     * Updates existing resource.\n     *\n     * @param integer \$id\n     * @param Request \$request\n     * @return Response\n     */\n    public function update(Request \$request, \$id)\n    {\n\n        request()->merge(['id' => \$id]);\n\n        return \$this->returnResponse(\n            \$this->service->update(request()->all())\n        );\n    }\n}\n";

        $repositoryClass = $this->absPath . 'app/Http/Controllers/BaseController.php';

        $this->printWhiteText("Creating BaseController.php... ");

        if(!file_exists($repositoryClass)) {
            $creation = new Process(['touch', $repositoryClass]);
            $creation->run();
            $this->printGreenText("BaseController created!");
            $this->printWhiteText("BaseController filling with template... ");
            if (!($fp = fopen($repositoryClass, 'w'))) {
                $this->printRedText("Cannot open BaseController for writing");
                return;
            }
            fprintf($fp, $CONTROLLER_TEMPLATE);
            $this->printGreenText("BaseController filled with template");
        }else{
            $this->printRedText("BaseController already exists");
        }      

    }

    private function createCrudServiceInterface() 
    {

        $CRUD_SERVICE_INTERFACE_TEMPLATE = "<?php\n\nnamespace App\Model\Contracts\Interfaces\Services;\n\ninterface CrudServiceInterface\n{\n\n    public function paginate();\n\n    public function getFirstBy(\$column, \$value, \$fail);\n\n    public function delete(\$id);\n\n    public function create(\$data);\n\n    public function update(\$data);\n}\n";

        $repositoryClass = $this->absPath . 'app/Model/Contracts/Interfaces/Services/CrudServiceInterface.php';

        $this->printWhiteText("Creating CrudServiceInterface.php... ");

        if(!file_exists($repositoryClass)) {
            $creation = new Process(['touch', $repositoryClass]);
            $creation->run();
            $this->printGreenText("CrudServiceInterface created!");
            $this->printWhiteText("CrudServiceInterface filling with template... ");
            if (!($fp = fopen($repositoryClass, 'w'))) {
                $this->printRedText("Cannot open CrudServiceInterface for writing");
                return;
            }
            fprintf($fp, $CRUD_SERVICE_INTERFACE_TEMPLATE);
            $this->printGreenText("CrudServiceInterface filled with template");
        }else{
            $this->printRedText("CrudServiceInterface already exists");
        }      

    }

    private function createRepository()
    {

        $REPOSITORY_TEMPLATE = "<?php\n\nnamespace App\Model\Contracts\AbstractClasses;\n\nuse App\Model\Contracts\Interfaces\AbstractClasses\RepositoryInterface;\nuse App\Model\Filters\Offset;\nuse App\Model\Filters\Limit;\nuse Illuminate\Pipeline\Pipeline;\n\nclass Repository implements RepositoryInterface\n{\n\n    protected \$model;\n    protected \$modelInstance;\n\n    public function __construct()\n    {\n\n        \$this->makeModel();\n    }\n    \n    public function model()\n    {\n\n        return \$this->modelInstance;\n    }\n    \n    /**\n     * Makes model.\n     *\n     * @return Illuminate\Database\Eloquent\Model\n     */\n    public function makeModel()\n    {\n\n        \$model = \App::make(\$this->model());\n        \n        return \$this->model = \$model->newQuery();\n    }\n    \n    /**\n     * Creates new item.\n     *\n     * @param array \$args\n     * @return Illuminate\Database\Eloquent\Model\n     */\n    public function create(\$args)\n    {\n\n        \$instance = new \$this->modelInstance;\n        \$instance->fill(\$args);\n        \$instance->save();\n\n        return \$instance;\n    }\n\n    /**\n     * Gets item by column value.\n     *\n     * @param string \$column\n     * @param mixed \$value\n     * @param boolean \$fail\n     * @return Illuminate\Database\Eloquent\Model\n     */\n    public function getFirstBy(\$column, \$value, \$fail)\n    {\n\n        \$query = \$this->makeModel()->where(\$column, \$value);\n\n        return \$fail ? \$query->firstOrFail() : \$query->first();\n    }\n\n    /**\n     * Deletes items by column value pair.\n     *\n     * @param string \$column\n     * @param mixed \$value\n     * @return void\n     */\n    public function deleteBy(\$column, \$value)\n    {\n\n        \$this->makeModel()->where(\$column, \$value)->delete();\n    }\n\n    /**\n     * Deletes given item.\n     *\n     * @param integer \$id\n     * @return void\n     */\n    public function delete(\$id)\n    {\n\n        \$this->makeModel()->findOrFail(\$id)->delete();\n    }\n\n    /**\n     * Updates given item.\n     *\n     * @param string \$column\n     * @param string \$value\n     * @param array \$args\n     * @return mixed\n     */\n    public function updateBy(\$column, \$value, \$args)\n    {\n\n        \$model = \$this->makeModel()->where(\$column, \$value)->firstOrFail();\n        \n        if (\$model) {\n            \$model->update(\$args);\n            return \$model;\n        }\n\n        return false;\n    }\n\n    /**\n     * Gets items from the database based on given criterias.\n     *\n     * @return Collection\n     */\n    public function paginate()\n    {\n\n        \$builder = \$this->makeModel();\n        \$builder = app(Pipeline::class)\n            ->send(\$builder)\n            ->through([\n                Limit::class,\n                Offset::class\n            ])\n            ->thenReturn();\n\n        return \$builder->orderBy('id', 'desc')->get();\n    }\n\n    /**\n     * Gets all items from the database.\n     *\n     * @return Collection\n     */\n    public function all()\n    {\n\n        return \$this->makeModel()->get();\n    }\n}\n";

        $repositoryClass = $this->absPath . 'app/Model/Contracts/AbstractClasses/Repository.php';

        $this->printWhiteText("Creating Repository.php... ");

        if(!file_exists($repositoryClass)) {
            $creation = new Process(['touch', $repositoryClass]);
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
            $creation = new Process(['touch', $formatterClass]);
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
            $creation = new Process(['touch', $validatorClass]);
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

        $SERVICE_TEMPLATE = "<?php\n\nnamespace App\Model\Contracts\AbstractClasses;\n\nuse Illuminate\Support\Str;\nuse Illuminate\Support\Arr;\nuse App;\nuse App\Model\Contracts\Interfaces\Services\CrudServiceInterface;\nuse App\Model\Facades\Response;\n\nclass Service implements CrudServiceInterface\n{\n    \n    private \$subsystem;\n    private \$className;\n\n    protected \$validator;\n    protected \$formatter;\n    protected \$repository;\n    protected \$statusCode;\n\n    /**\n     * Generates common resources like repository, formatter, validator.\n     *\n     * @param string \$namespace\n     * @return void\n     */\n    protected function generateResources(\$namespace)\n    {\n\n        \$parts = explode('\\', \$namespace);\n\n        \$this->subsystem = \$parts[count(\$parts)-1];\n        \$this->className = \$this->resourceName();\n\n        \$this->generateRepository();\n        \$this->generateFormatter();\n        \$this->generateValidator();\n    }\n\n    /**\n     * Generates new repository if possible.\n     *\n     * @return void\n     */\n    private function generateRepository()\n    {\n\n        try {\n            \$this->repository = App::make(\n                'App\Model\Contracts\Interfaces\Data\\'.\$this->className.'RepositoryInterface'\n            );\n        } catch (\Exception \$e) {\n        }\n    }\n\n    /**\n     * Generates new formatter if possible.\n     *\n     * @return void\n     */\n    private function generateFormatter()\n    {\n\n        try {\n            \$this->formatter = App::make(\n                'App\Model\Contracts\Interfaces\Formatters\\'\n                .\$this->subsystem\n                .'\\'\n                .\$this->className\n                .'FormatterInterface'\n            );\n        } catch (\Exception \$e) {\n        }\n    }\n\n    /**\n     * Generates new validator if possible.\n     *\n     * @return void\n     */\n    private function generateValidator()\n    {\n\n        try {\n            \$this->validator = App::make(\n                'App\Model\Contracts\Interfaces\Validators\\'\n                .\$this->subsystem\n                .'\\'\n                .\$this->className\n                .'ValidatorInterface'\n            );\n        } catch (\Exception \$e) {\n        }\n    }\n    \n    /**\n     * Returns resource name.\n     *\n     * @return void\n     */\n    private function resourceName()\n    {\n\n        \$name = explode('Service', class_basename(\$this))[0];\n\n        if (strpos(class_basename(\$this), 'ServiceService') !== false) {\n            \$name .= 'Service';\n        }\n\n        return \$name;\n    }\n\n    /**\n     * Gets items and prepares them for display.\n     *\n     * @return array\n     */\n    public function paginate()\n    {\n\n        return \$this->formatter->prepareForDisplay(\n            \$this->repository->paginate()\n        );\n    }\n\n    /**\n     * Gets all items and prepares them for display.\n     *\n     * @return array\n     */\n    public function all()\n    {\n\n        return \$this->formatter->prepareForDisplay(\n            \$this->repository->all()\n        );\n    }\n\n    /**\n     * Gets item by id and prepares it for display.\n     *\n     * @param string \$column\n     * @param string \$value\n     * @param boolean \$fail\n     * @return array\n     */\n    public function getFirstBy(\$column, \$value, \$fail)\n    {\n\n        return \$this->formatter->prepareItemForDisplay(\n            \$this->repository->getFirstBy(\$column, \$value, \$fail)\n        );\n    }\n\n    /**\n     * Deletes item by id.\n     *\n     * @param integer \$id\n     * @return array\n     */\n    public function delete(\$id)\n    {\n\n        \$this->repository->delete(\$id);\n        Response::setStatusCode(204);\n    }\n\n    /**\n     * Creates new item by given data.\n     *\n     * @param array \$data\n     * @return array\n     */\n    public function create(\$data)\n    {\n\n        if (!\$this->validator->validateCreate(\$data)) {\n            return \$this->formatter->prepareForFailedResponse(\$this->validator->getErrors());\n        }\n\n        \$this->repository->create(\n            \$this->formatter->prepareForCreate(\$data)\n        );\n\n        Response::setStatusCode(201);\n\n        return [];\n    }\n\n    /**\n     * Updates existing item by given data.\n     *\n     * @param array \$data\n     * @return array\n     */\n    public function update(\$data)\n    {\n\n        if (!\$this->validator->validateUpdate(\$data)) {\n            return \$this->formatter->prepareForFailedResponse(\$this->validator->getErrors());\n        }\n\n        \$this->repository->updateBy(\n            'id',\n            \$data['id'],\n            \$this->formatter->prepareForUpdate(\$data)\n        );\n\n        Response::setStatusCode(204);\n\n        return [];\n    }\n}\n";

        $serviceClass = $this->absPath . 'app/Model/Contracts/AbstractClasses/Service.php';

        $this->printWhiteText("Creating Service.php... ");

        if(!file_exists($serviceClass)) {
            $creation = new Process(['touch', $serviceClass]);
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
            $creation = new Process(['touch', $filterClass]);
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
            $creation = new Process(['touch', $formatterInterfaceClass]);
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
            $creation = new Process(['touch', $repositoryInterfaceClass]);
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

    private function createDir($path)
    {

        $targetPath = $this->absPath . $path;

        $this->printWhiteText("Creating " . $targetPath . "...");

        if(file_exists($targetPath))
        {

            $this->printRedText($targetPath . " already exists.");
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