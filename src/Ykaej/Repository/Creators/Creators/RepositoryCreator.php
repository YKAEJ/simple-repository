<?php

namespace Ykaej\Repository\Creators\Creators;


use Doctrine\Common\Inflector\Inflector;
use Illuminate\Support\Facades\Config;

/**
 * Class RepositoryCreator
 * @package Ykaej\Repository\Creators\Creators
 */
class RepositoryCreator extends Creator
{
    /**
     * @var string
     */
    protected $stub = 'repository.stub';
    /**
     * @var string
     */
    protected $directory = 'repository.repository_path';

    /**
     * @return int
     */
    protected function createClass()
    {
        $filename = (!strpos($this->class, 'Repository')) ? $this->class . 'Repository' : $this->class;

        $path = $this->directory . DIRECTORY_SEPARATOR . $filename . '.php';

        $popular_data = [
            '$repository_namespace$' => Config::get('repository.repository_namespace'),
            '$repository_class$' => $filename,
            '$model_namespace$' => Config::get('repository.model_namespace'),
            '$model_class$' => $this->getModelName(),
        ];

        $stub = $this->getStub();

        foreach ($popular_data as $key => $value) {
            $stub = str_replace($key, $value, $stub);
        }

        return $this->files->put($path, $stub);
    }

    /**
     * @return string
     */
    protected function getModelName()
    {
        $model = $this->model;
        if (isset($model) && !empty($model)) {
            $model_name = $model;
        } else {
            // Set the model name by the stripped repository name.
            $model_name = Inflector::singularize($this->stripRepositoryName());
        }
        return $model_name;
    }

    /**
     * Get the stripped repository name.
     * @return string
     */
    protected function stripRepositoryName()
    {
        // Remove repository from the string.
        $stripped = str_replace('repository', '', strtolower($this->class));

        // Uppercase repository name.
        $result = ucfirst($stripped);

        return $result;
    }
}