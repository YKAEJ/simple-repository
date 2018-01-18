<?php

namespace Ykaej\Repository\Creators\Creators;

use Illuminate\Support\Facades\Config;

/**
 * Class CriteriaCreator
 * @package Ykaej\Repository\Creators\Creators
 */
class CriteriaCreator extends Creator
{
    /**
     * @var string
     */
    protected $stub = 'criteria.stub';
    /**
     * @var string
     */
    protected $directory = 'repository.criteria_path';


    /**
     * @return int
     */
    protected function createClass()
    {
        $filename = (!strpos($this->class, 'Criteria')) ? $this->class . 'Criteria' : $this->class;

        $path = $this->directory . DIRECTORY_SEPARATOR . $filename . '.php';
        $criteria_namespace = Config::get('repository.criteria_namespace');

        $popular_data = [
            '$criteria_namespace$' => $criteria_namespace,
            '$criteria_class$' => $filename
        ];
        $stub = $this->getStub();

        foreach ($popular_data as $key => $value) {
            $stub = str_replace($key,$value,$stub);
        }
        return $this->files->put($path,$stub);
    }
}