<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    // column constants
    public function id()
    {
        return $this->model->getKeyName();
    }

    public function createdAt()
    {
        return $this->model::CREATED_AT;
    }
    public function updatedAt()
    {
        return $this->model::UPDATED_AT;
    }

    // functions
    public function all($columns = ['*'])
    {
        return $this->model->get($columns);
    }

    public function paginate($perPage = 15)
    {
        return $this->model->paginate($perPage);
    }

    public function find($id, $columns = ['*'])
    {
        return $this->model->findOrFail($id, $columns);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $record = $this->find($id);
        $record->update($data);

        return $record;
    }

    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    public function query()
    {
        return $this->model->query();
    }
}
