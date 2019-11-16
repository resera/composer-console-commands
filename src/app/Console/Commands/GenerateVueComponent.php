<?php

namespace Resera\ConsoleCommands\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;

class GenerateBoilerplate extends Command
{

    protected $TRANSLATION_TEMPLATE = "<i18n src='../../../../assets/%1\$s/translations/%2\$s.json'></i18n>\n\n";
    protected $COMPONENT_TEMPLATE = "<template>\n\n    <div class=\"%1\$s\">\n\n    </div>\n\n</template>\n\n<script>\n\n    export default {\n\n        name: '%1\$s',\n\n    }\n\n</script>\n\n<style lang=\"scss\" scoped>\n\n    @import '../../../../assets/%2\$s/sass/parameters';\n\n</style>\n\n";

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:vue-component {name} {type} {--subsystem=} {--translation=}';
    protected $absPath;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates vue component';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {

        parent::__construct();

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $this->createComponent();
        $this->printGreenText("Vue component code generated successfully!\n");

    }

    private function createComponent()
    {

        $name = $this->argument('name');
        $type = $this->argument('type');
        $subsystem = $this->option('subsystem');
        $translation = $this->option('translation');
        $componentFileFolder = "resources/components/" . $subsystem . "/" . $type . "/" . $name;
        $componentFile = "resources/components/" . $subsystem . "/" . $type . "/" . $name . "/" . $name . ".vue";

        $this->printWhiteText("Creating " . $name . "...");

        if(!file_exists($componentFile)) {

            $this->createDir($componentFileFolder);
            $creation = new Process('touch ' . $componentFile);
            $creation->run();
            $this->printGreenText($name . " created!");
            $this->printWhiteText($name . " filling with template...");

            if(!($fp = fopen($componentFile, 'w'))) {

                $this->printRedText("Cannot open " . $name . " for writing");
                return;

            }

            if($translation) {

                fprintf($fp, $this->TRANSLATION_TEMPLATE, $subsystem, $translation);

            }

            fprintf($fp, $this->COMPONENT_TEMPLATE, $name, $subsystem);
            $this->printGreenText($name . " filled with template");

        } else {

            $this->printRedText($name . " already exists.");

        }

    }

    private function createDir($path)
    {

        $targetPath = $path;
        $this->printWhiteText("Creating " . $targetPath . "...");

        if(file_exists($targetPath)) {

            $this->printRedText($targetPath . " already exists.");
            return;

        }

        $creation = new Process('mkdir ' . $targetPath);
        $creation->run(); 

    }

    private function printGreenText($text)
    {

        echo "\033[32m" . $text . " \033[0m \n";

    }

    private function printRedText($text)
    {

        echo "\033[31m" . $text . " \033[0m \n";

    }    

    private function printWhiteText($text)
    {

        echo $text . "\n";

    }      

}