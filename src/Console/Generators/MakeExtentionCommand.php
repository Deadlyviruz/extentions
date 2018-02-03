<?php

namespace Deadlyviruz\Extentions\Console\Generators;

use Deadlyviruz\Extentions\Extentions;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Helper\ProgressBar;

class MakeExtentionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:extention
        {slug : The slug of the Extention}
        {--Q|quick : Skip the make:extention wizard and use default values}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Extention and bootstrap it';

    /**
     * The modules instance.
     *
     * @var Extentions
     */
    protected $extention;

    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * Array to store the configuration details.
     *
     * @var array
     */
    protected $container;

    /**
     * Create a new command instance.
     *
     * @param Filesystem $files
     * @param Extentions    $extention
     */
    public function __construct(Filesystem $files, Extentions $extention)
    {
        parent::__construct();

        $this->files = $files;
        $this->extention = $extention;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->container['slug'] = str_slug($this->argument('slug'));
        $this->container['name'] = studly_case($this->container['slug']);
        $this->container['version'] = '1.0';
        $this->container['description'] = 'This is the description for the '.$this->container['name'].' extention.';

        if ($this->option('quick')) {
            $this->container['basename']    = studly_case($this->container['slug']);
            $this->container['namespace']   = config('extentions.namespace').$this->container['basename'];
            return $this->generate();
        }

        $this->displayHeader('make_extention_introduction');

        $this->stepOne();
    }

    /**
     * Step 1: Configure module manifest.
     *
     * @return mixed
     */
    protected function stepOne()
    {
        $this->displayHeader('make_extention_step_1');

        $this->container['name'] = $this->ask('Please enter the name of the extention:', $this->container['name']);
        $this->container['slug'] = $this->ask('Please enter the slug for the extention:', $this->container['slug']);
        $this->container['version'] = $this->ask('Please enter the extention version:', $this->container['version']);
        $this->container['description'] = $this->ask('Please enter the description of the extention:', $this->container['description']);
        $this->container['basename'] = studly_case($this->container['slug']);
        $this->container['namespace'] = config('extentions.namespace').$this->container['basename'];

        $this->comment('You have provided the following manifest information:');
        $this->comment('Name:                       '.$this->container['name']);
        $this->comment('Slug:                       '.$this->container['slug']);
        $this->comment('Version:                    '.$this->container['version']);
        $this->comment('Description:                '.$this->container['description']);
        $this->comment('Basename (auto-generated):  '.$this->container['basename']);
        $this->comment('Namespace (auto-generated): '.$this->container['namespace']);

        if ($this->confirm('If the provided information is correct, type "yes" to generate.')) {
            $this->comment('Thanks! That\'s all we need.');
            $this->comment('Now relax while your extention is generated.');

            $this->generate();
        } else {
            return $this->stepOne();
        }

        return true;
    }

    /**
     * Generate the extentions.
     */
    protected function generate()
    {
        $steps = [
            'Generating extention...'       => 'generateExtetntions',
            'Optimizing extention cache...' => 'optimizeExtentions',
        ];

        $progress = new ProgressBar($this->output, count($steps));
        $progress->start();

        foreach ($steps as $message => $function) {
            $progress->setMessage($message);

            $this->$function();

            $progress->advance();
        }

        $progress->finish();

        event($this->container['slug'].'.extention.made');

        $this->info("\nExtention generated successfully.");
    }

    /**
     * Generate defined extention folders.
     */
    protected function generateModule()
    {
        if (!$this->files->isDirectory(extention_path())) {
            $this->files->makeDirectory(extention_path());
        }

        $pathMap = config('extentions.pathMap');
        $directory = extention_path(null, $this->container['basename']);
        $source = __DIR__.'/../../../resources/stubs/extention';

        $this->files->makeDirectory($directory);

        $sourceFiles = $this->files->allFiles($source, true);

        if (!empty($pathMap)) {
            $search = array_keys($pathMap);
            $replace = array_values($pathMap);
        }

        foreach ($sourceFiles as $file) {
            $contents = $this->replacePlaceholders($file->getContents());
            $subPath = $file->getRelativePathname();

            if (!empty($pathMap)) {
                $subPath = str_replace($search, $replace, $subPath);
            }

            $filePath = $directory.'/'.$subPath;
            $dir = dirname($filePath);

            if (!$this->files->isDirectory($dir)) {
                $this->files->makeDirectory($dir, 0755, true);
            }

            $this->files->put($filePath, $contents);
        }
    }

    /**
     * Reset module cache of enabled and disabled modules.
     */
    protected function optimizeExtentions()
    {
        return $this->callSilent('extention:optimize');
    }

    /**
     * Pull the given stub file contents and display them on screen.
     *
     * @param string $file
     * @param string $level
     *
     * @return mixed
     */
    protected function displayHeader($file = '', $level = 'info')
    {
        $stub = $this->files->get(__DIR__.'/../../../resources/stubs/console/'.$file.'.stub');

        return $this->$level($stub);
    }

    protected function replacePlaceholders($contents)
    {
        $find = [
            'DummyBasename',
            'DummyNamespace',
            'DummyName',
            'DummySlug',
            'DummyVersion',
            'DummyDescription',
        ];

        $replace = [
            $this->container['basename'],
            $this->container['namespace'],
            $this->container['name'],
            $this->container['slug'],
            $this->container['version'],
            $this->container['description'],
        ];

        return str_replace($find, $replace, $contents);
    }
}
