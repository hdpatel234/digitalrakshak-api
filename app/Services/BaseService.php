<?php

namespace App\Services;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

abstract class BaseService
{
    public function __construct(protected BaseRepository $repository) {}

    // column constants
    public function id()
    {
        return $this->repository->id();
    }

    public function status()
    {
        return $this->repository->status();
    }

    public function createdBy()
    {
        return $this->repository->createdBy();
    }
    public function updatedBy()
    {
        return $this->repository->updatedBy();
    }
    public function deletedBy()
    {
        return $this->repository->deletedBy();
    }
    public function createdAt()
    {
        return $this->repository->createdAt();
    }
    public function updatedAt()
    {
        return $this->repository->updatedAt();
    }
    public function deletedAt()
    {
        return $this->repository->deletedAt();
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
