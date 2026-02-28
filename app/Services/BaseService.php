<?php

namespace App\Services;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

abstract class BaseService
{
    protected BaseRepository $repository;

    public function __construct(BaseRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function id()
    {
        return $this->repository->id();
    }

    public function createdAt()
    {
        return $this->repository->createdAt();
    }
    public function updatedAt()
    {
        return $this->repository->updatedAt();
    }

    // functions
    public function all($columns = ['*'])
    {
        return $this->repository->all($columns);
    }

    public function allActive($columns = ['*'])
    {
        return $this->repository->allActive($columns);
    }

    public function count()
    {
        return $this->repository->count();
    }

    public function paginate($perPage = 15)
    {
        return $this->repository->paginate($perPage);
    }

    public function find($id, $columns = ['*'])
    {
        return $this->repository->find($id, $columns);
    }

    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    public function update($id, array $data)
    {
        $record = $this->find($id);
        $record->update($data);

        return $record;
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }

    public function query()
    {
        return $this->repository->query();
    }

    public function datatable(?Builder $query = null, array $params = [], array $config = []): array
    {
        return $this->repository->datatable($query, $params, $config);
    }
}
