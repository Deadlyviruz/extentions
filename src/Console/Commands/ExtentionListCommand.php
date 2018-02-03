<?php

namespace Deadlyviruz\Extentions\Console\Commands;

use Deadlyviruz\Extentions\Extentions;
use Illuminate\Console\Command;

class ExtentionListCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'extention:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all application Extentions';

    /**
     * @var Extention
     */
    protected $extention;

    /**
     * The table headers for the command.
     *
     * @var array
     */
    protected $headers = ['#', 'Name', 'Slug', 'Description', 'Status'];

    /**
     * Create a new command instance.
     *
     * @param Extentions $extention
     */
    public function __construct(Extentions $extention)
    {
        parent::__construct();

        $this->extention = $extention;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $extentions = $this->extention->all();

        if (count($extentions) == 0) {
            return $this->error("Your application doesn't have any modules.");
        }

        $this->displayModules($this->getModules());
    }

    /**
     * Get all Extentions.
     *
     * @return array
     */
    protected function getModules()
    {
        $extentions = $this->extention->all();
        $results = [];

        foreach ($extentions as $extention) {
            $results[] = $this->getModuleInformation($extention);
        }

        return array_filter($results);
    }

    /**
     * Returns extention manifest information.
     *
     * @param string $extention
     *
     * @return array
     */
    protected function getModuleInformation($extention)
    {
        return [
            '#'           => $extention['order'],
            'name'        => isset($extention['name']) ? $extention['name'] : '',
            'slug'        => $extention['slug'],
            'description' => isset($extention['description']) ? $extention['description'] : '',
            'status'      => ($this->extention->isEnabled($extention['slug'])) ? 'Enabled' : 'Disabled',
        ];
    }

    /**
     * Display the extention information on the console.
     *
     * @param array $extentions
     */
    protected function displayModules(array $extentions)
    {
        $this->table($this->headers, $extentions);
    }
}
