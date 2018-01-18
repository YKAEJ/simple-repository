<?php

namespace Ykaej\Repository\Creators\Creators;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;

/**
 * Class Creator
 * @package Ykaej\Repository\Creators\Creators
 */
abstract class Creator
{
    /**
     * @var Filesystem
     */
    protected $files;
    /**
     * @var string modelName
     */
    protected $model;
    /**
     * @var string className
     */
    protected $class;

    /**
     * Creator constructor.
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->files = $filesystem;
    }


    /**
     * Create the criteria.
     * @param $class
     * @param $model
     * @return mixed
     */
    public function create($class, $model)
    {
        $this->model = $model;
        $this->class = $class;

        $this->createDirectory();

        return $this->createClass();
    }

    /**
     * get stub
     * @return string
     */
    protected function getStub()
    {
        $stub = $this->files->get($this->getStubPath());

        return $stub;
    }

    /**
     * @return string
     */
    protected function getStubPath()
    {
        $path = __DIR__ . '/../stubs/' . $this->stub;
        return $path;
    }

    /**
     * create  directory
     */
    protected function createDirectory()
    {
        $this->directory = Config::get($this->directory);
        // Create the directory if not.
        if (!$this->files->isDirectory($this->directory)) {
            $this->files->makeDirectory($this->directory, 0755, true);
        }
    }

    /**
     * @return mixed
     */
    protected abstract function createClass();
}