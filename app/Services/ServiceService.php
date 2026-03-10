<?php

namespace App\Services;

use App\Repositories\ServiceRepository;
use Illuminate\Support\Str;

class ServiceService extends BaseService
{
    private const CATEGORY_PREFIX_MAP = [
        'background verification' => 'BGV',
        'identity verification' => 'IDV',
        'identity verifation' => 'IDV',
    ];
    
    public function __construct(ServiceRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function serviceCategory()
    {
        return $this->repository->serviceCategory();
    }

    public function serviceName()
    {
        return $this->repository->serviceName();
    }

    public function serviceCode()
    {
        return $this->repository->serviceCode();
    }

    public function description()
    {
        return $this->repository->description();
    }

    public function basePrice()
    {
        return $this->repository->basePrice();
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
    // functions
    public function create(array $data)
    {
        $categoryColumn = $this->serviceCategory();
        $codeColumn = $this->serviceCode();

        $category = trim((string) ($data[$categoryColumn] ?? ''));
        $code = trim((string) ($data[$codeColumn] ?? ''));

        if ($category !== '' && $code === '') {
            $data[$codeColumn] = $this->generateNextServiceCode($category);
        }

        return parent::create($data);
    }

    protected function generateNextServiceCode(string $category): string
    {
        $prefix = $this->resolveCategoryPrefix($category);
        $pattern = '/^' . preg_quote($prefix, '/') . '\-(\d+)$/i';

        $maxSequence = $this->query()
            ->where($this->serviceCode(), 'like', $prefix . '-%')
            ->get([$this->serviceCode()])
            ->reduce(function (int $carry, $service) use ($pattern) {
                $code = (string) data_get($service, $this->serviceCode(), '');

                if (!preg_match($pattern, $code, $matches)) {
                    return $carry;
                }

                $sequence = (int) ($matches[1] ?? 0);

                return max($carry, $sequence);
            }, 0);

        return sprintf('%s-%03d', $prefix, $maxSequence + 1);
    }

    protected function resolveCategoryPrefix(string $category): string
    {
        $normalized = strtolower(trim($category));

        if (isset(self::CATEGORY_PREFIX_MAP[$normalized])) {
            return self::CATEGORY_PREFIX_MAP[$normalized];
        }

        $acronym = Str::of($category)
            ->replaceMatches('/[^A-Za-z0-9 ]/', ' ')
            ->squish()
            ->explode(' ')
            ->filter()
            ->map(fn ($word) => Str::substr(Str::upper($word), 0, 1))
            ->implode('');

        $prefix = Str::upper(Str::limit((string) $acronym, 3, ''));

        return $prefix !== '' ? $prefix : 'SRV';
    }
}
