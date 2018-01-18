<?php

namespace Ykaej\Repository\Providers;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Illuminate\Support\ServiceProvider;
use Ykaej\Repository\Console\Commands\MakeCriteriaCommand;
use Ykaej\Repository\Console\Commands\MakeRepositoryCommand;
use Ykaej\Repository\Creators\Creators\CriteriaCreator;
use Ykaej\Repository\Creators\Creators\RepositoryCreator;

class RepositoryProvider extends ServiceProvider
{
    public function boot()
    {
        $config_path = __DIR__ . '/../../../../config/repository.php';

        $this->publishes(
            [$config_path => config_path('repository.php')],
            'repository'
        );
    }

    public function register()
    {
        // Register bindings.
        $this->registerBindings();

        // Register the make:criteria command.
        $this->commands(MakeCriteriaCommand::class);
        $this->commands(MakeRepositoryCommand::class);

        $config_path = __DIR__ . '/../../../../config/repository.php';
        $this->mergeConfigFrom(
            $config_path,
            'repository'
        );
    }

    protected function registerBindings()
    {
        // FileSystem.
        $this->app->instance('FileSystem', new Filesystem());

        // Composer.
        $this->app->bind('Composer', function ($app) {
            return new Composer($app['FileSystem']);
        });

        $this->app->singleton('CriteriaCreator',function ($app){
            return new CriteriaCreator($app['FileSystem']);
        });

        $this->app->singleton('RepositoryCreator',function ($app){
            return new RepositoryCreator($app['FileSystem']);
        });
    }
}