<?php

namespace Ykaej\Repository\Console\Commands;

use Illuminate\Console\Command;
use Ykaej\Repository\Creators\Creators\RepositoryCreator;

class MakeRepositoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository 
                                  {name : The repository name}
                                  {--model= : The model name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository class';


    protected $creator;
    protected $composer;
    public function __construct(RepositoryCreator $creator)
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

        $options = $this->options();

        $this->writeRepository($argument,$options);

        $this->composer->dumpAutoloads();
    }

    protected function writeRepository($argument, $options)
    {
        $model = $options['model'];
        if ($this->creator->create($argument,$model)){
            $this->info('Successfully created the Repository class');
        }
    }
}
