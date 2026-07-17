<?php

namespace App\Repositories;

use App\Models\Client;

class ClientRepository extends BaseRepository
{
    public function __construct(Client $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function companyName()
    {
        return Client::COMPANY_NAME;
    }

    public function email()
    {
        return Client::EMAIL;
    }

    public function phone()
    {
        return Client::PHONE;
    }

    public function gstNumber()
    {
        return Client::GST_NUMBER;
    }

    public function panNumber()
    {
        return Client::PAN_NUMBER;
    }

    public function address()
    {
        return Client::ADDRESS;
    }

    public function countryID()
    {
        return Client::COUNTRY_ID;
    }

    public function stateID()
    {
        return Client::STATE_ID;
    }

    public function cityId()
    {
        return Client::CITY_ID;
    }

    public function pincode()
    {
        return Client::PINCODE;
    }

    public function currency()
    {
        return Client::CURRENCY;
    }

    public function creditLimit()
    {
        return Client::CREDIT_LIMIT;
    }

    public function creditBalance()
    {
        return Client::CREDIT_BALANCE;
    }

    public function paymentTerms()
    {
        return Client::PAYMENT_TERMS;
    }

    public function defaultSupportConfigId()
    {
        return Client::DEFAULT_SUPPORT_CONFIG_ID;
    }

    public function defualtDocumentConfigId()
    {
        return Client::DEFAULT_DOCUMENT_CONFIG_ID;
    }

    public function status()
    {
        return Client::STATUS;
    }

    public function createdBy()
    {
        return Client::CREATED_BY;
    }

    public function updatedBy()
    {
        return Client::UPDATED_BY;
    }

    public function deletedBy()
    {
        return Client::DELETED_BY;
    }
    // functions
    public function getClientsQuery(array $data)
    {
        $query = $this->query();

        // Search filtering
        if (isset($data['search']) && !empty($data['search'])) {
            $search = $data['search'];
            $query->where(function ($q) use ($search) {
                $q->where($this->companyName(), 'LIKE', "%{$search}%")
                  ->orWhere($this->email(), 'LIKE', "%{$search}%")
                  ->orWhere($this->phone(), 'LIKE', "%{$search}%");
            });
        }

        // Status filtering
        if (isset($data['status']) && $data['status'] !== 'all') {
            $query->where($this->status(), $data['status']);
        }

        // Sorting
        $sortBy = $data['sort_by'] ?? $this->createdAt();
        $sortDirection = $data['sort_direction'] ?? 'desc';
        $query->orderBy($sortBy, $sortDirection);

        return $query;
    }

    public function countByStatus(string $status)
    {
        return $this->query()->where($this->status(), $status)->count();
    }

    public function countBetweenDates($start, $end)
    {
        return $this->query()->whereBetween($this->createdAt(), [$start, $end])->count();
    }
}
