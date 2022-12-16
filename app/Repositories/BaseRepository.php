<?php

namespace App\Repositories;

use App\Exceptions\GeneralException;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements BaseContract
{
    /**
     * @var Illuminate\Database\Eloquent\Model
     */
    protected $model;

    abstract public function model();

    public function __construct()
    {
        $this->makeModel();
    }

    public function makeModel()
    {
        $model = resolve($this->model());
        if ($model instanceof Model) {
            $this->model = $model;
        } else {
            throw new GeneralException("Class {$this->model()} is not a subtype of type Model");
        }
    }

    public function all()
    {
        return $this->model->all();
    }

    public function show($id)
    {
        return $this->model->find($id);
    }
}
