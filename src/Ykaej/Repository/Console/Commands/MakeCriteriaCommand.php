<?php

namespace Ykaej\Repository\Console\Commands;

use Illuminate\Console\Command;
use Ykaej\Repository\Creators\Creators\CriteriaCreator;

class MakeCriteriaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:criteria 
                                {name : The criteria name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new criteria class';

    /**
     * @var CriteriaCreator
     */
    protected $creator;

    /**
     * @var Composer
     */
    protected $composer;

    public function __construct(CriteriaCreator $creator)
    {
        parent::__construct();

        $this->creator = $creator;

        $this->composer = app()['composer'];
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $argument = $this->argument('name');

        $this->writeRepository($argument);

        $this->composer->dumpAutoloads();
    }

    protected function writeRepository($argument)
    {
        if ($this->creator->create($argument, '')) {
            $this->info('Successfully created the criteria class');
        }
    }

}
