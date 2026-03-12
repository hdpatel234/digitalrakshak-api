<?php

namespace App\Services;

use App\Enums\CandidateEvent;
use App\Enums\CandidateSource;
use App\Enums\CandidateStatus;
use App\Repositories\CandidatesLogRepository;
use App\Repositories\CandidateManagerRepository;
use App\Repositories\CandidateRepository;
use App\Repositories\CityRepository;
use App\Repositories\CountryRepository;
use App\Repositories\StateRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CandidateService extends BaseService
{
    protected CandidateManagerRepository $candidateManagerRepository;
    protected CandidatesLogRepository $candidatesLogRepository;
    protected CountryRepository $countryRepository;
    protected StateRepository $stateRepository;
    protected CityRepository $cityRepository;

    public function __construct(
        CandidateRepository $repository,
        CandidateManagerRepository $candidateManagerRepository,
        CandidatesLogRepository $candidatesLogRepository,
        CountryRepository $countryRepository,
        StateRepository $stateRepository,
        CityRepository $cityRepository
    )
    {
        parent::__construct($repository);
        $this->candidateManagerRepository = $candidateManagerRepository;
        $this->candidatesLogRepository = $candidatesLogRepository;
        $this->countryRepository = $countryRepository;
        $this->stateRepository = $stateRepository;
        $this->cityRepository = $cityRepository;
    }

    // column constants
    public function clientId()
    {
        return $this->repository->clientId();
    }

    public function firstName()
    {
        return $this->repository->firstName();
    }

    public function lastName()
    {
        return $this->repository->lastName();
    }

    public function email()
    {
        return $this->repository->email();
    }

    public function phone()
    {
        return $this->repository->phone();
    }

    public function alternatePhone()
    {
        return $this->repository->alternatePhone();
    }

    public function address()
    {
        return $this->repository->address();
    }

    public function countryId()
    {
        return $this->repository->countryId();
    }
    public function stateId()
    {
        return $this->repository->stateId();
    }
    public function cityId()
    {
        return $this->repository->cityId();
    }
    public function locationVerifed()
    {
        return $this->repository->locationVerifed();
    }

    public function locationVerifiedAt()
    {
        return $this->repository->locationVerifiedAt();
    }

    public function city()
    {
        return $this->repository->city();
    }

    public function state()
    {
        return $this->repository->state();
    }

    public function pincode()
    {
        return $this->repository->pincode();
    }

    public function country()
    {
        return $this->repository->country();
    }

    public function dateOfBirth()
    {
        return $this->repository->dateOfBirth();
    }

    public function gender()
    {
        return $this->repository->gender();
    }

    public function source()
    {
        return $this->repository->source();
    }

    public function status()
    {
        return $this->repository->status();
    }

    public function invitationSentAt()
    {
        return $this->repository->invitationSentAt();
    }

    public function invitationAcceptedAt()
    {
        return $this->repository->invitationAcceptedAt();
    }

    public function lastOrderId()
    {
        return $this->repository->lastOrderId();
    }

    public function totalOrders()
    {
        return $this->repository->totalOrders();
    }

    public function totalOrderValue()
    {
        return $this->repository->totalOrderValue();
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

    // functions
    public function createWithAssociations(array $payload, int $clientId, ?string $ipAddress = null)
    {
        return DB::transaction(function () use ($payload, $clientId, $ipAddress) {
            $countryId = $this->toNullableInt($payload['country'] ?? null);
            $stateId = $this->toNullableInt($payload['state'] ?? null);
            $cityId = $this->toNullableInt($payload['city'] ?? null);

            $countryName = $this->resolveLocationName($this->countryRepository, $countryId, 'country');
            $stateName = $this->resolveLocationName($this->stateRepository, $stateId, 'state');
            $cityName = $this->resolveLocationName($this->cityRepository, $cityId, 'city');

            $candidate = $this->create([
                $this->clientId() => $clientId,
                $this->firstName() => trim((string) ($payload['first_name'] ?? '')),
                $this->lastName() => trim((string) ($payload['last_name'] ?? '')),
                $this->email() => strtolower(trim((string) ($payload['email'] ?? ''))),
                $this->phone() => $this->buildPhone(
                    (string) ($payload['dialCode'] ?? ''),
                    (string) ($payload['phoneNumber'] ?? '')
                ),
                $this->address() => $this->nullIfEmpty((string) ($payload['address'] ?? '')),
                $this->countryId() => $countryId,
                $this->stateId() => $stateId,
                $this->cityId() => $cityId,
                $this->country() => $countryName,
                $this->state() => $stateName,
                $this->city() => $cityName,
                $this->pincode() => $this->nullIfEmpty((string) ($payload['postcode'] ?? '')),
                $this->source() => $payload['source'] ?? CandidateSource::CREATE_FORM->value,
                $this->status() => CandidateStatus::CREATED->value,
            ]);

            $managerEmails = $this->normalizeManagerEmails($payload['managerEmails'] ?? []);

            foreach ($managerEmails as $email) {
                $this->candidateManagerRepository->create([
                    $this->candidateManagerRepository->candidateId() => $candidate->{$this->id()},
                    $this->candidateManagerRepository->email() => $email,
                    $this->candidateManagerRepository->status() => CandidateStatus::ACTIVE->value,
                ]);
            }

            $this->candidatesLogRepository->create([
                $this->candidatesLogRepository->candidateId() => $candidate->{$this->id()},
                $this->candidatesLogRepository->action() => CandidateEvent::CREATED->value,
                $this->candidatesLogRepository->ipAddress() => $this->nullIfEmpty((string) $ipAddress),
                $this->candidatesLogRepository->status() => CandidateStatus::CREATED->value,
            ]);

            return [
                'candidate' => $candidate->fresh(),
                'manager_emails' => $managerEmails,
            ];
        });
    }

    protected function resolveLocationName($repository, ?int $id, string $locationType): ?string
    {
        if (!$id) {
            return null;
        }

        $record = $repository->query()->find($id);

        if (!$record) {
            throw ValidationException::withMessages([
                $locationType => [__('Invalid ' . $locationType . ' selected.')],
            ]);
        }

        return $record->{$repository->name()};
    }

    protected function buildPhone(string $dialCode, string $phoneNumber): ?string
    {
        $dialCode = trim($dialCode);
        $phoneNumber = trim($phoneNumber);

        if ($dialCode === '' && $phoneNumber === '') {
            return null;
        }

        return trim($dialCode . ' ' . $phoneNumber);
    }

    protected function normalizeManagerEmails($emails): array
    {
        if (!is_array($emails)) {
            return [];
        }

        $filtered = [];
        foreach ($emails as $email) {
            $value = strtolower(trim((string) $email));
            if ($value !== '') {
                $filtered[] = $value;
            }
        }

        return array_values(array_unique($filtered));
    }

    protected function toNullableInt($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    protected function nullIfEmpty(string $value): ?string
    {
        $value = trim($value);

        return $value === '' ? null : $value;
    }
}
