<?php

namespace App\Repositories;

use App\Models\UserConfigValue;
use Illuminate\Support\Facades\DB;

class UserConfigValueRepository extends BaseRepository
{
    public function __construct(UserConfigValue $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function userId()
    {
        return UserConfigValue::USER_ID;
    }

    public function configId()
    {
        return UserConfigValue::CONFIG_ID;
    }

    public function value()
    {
        return UserConfigValue::VALUE;
    }

    public function upsertForUserByConfigIds(int|string $userId, array $configIdValueMap): void
    {
        if ($configIdValueMap === []) {
            return;
        }

        DB::transaction(function () use ($configIdValueMap, $userId): void {
            foreach ($configIdValueMap as $configId => $value) {
                $this->query()->updateOrCreate(
                    [
                        $this->userId() => $userId,
                        $this->configId() => $configId,
                    ],
                    [
                        $this->value() => (string) $value,
                    ]
                );
            }
        });
    }
}
