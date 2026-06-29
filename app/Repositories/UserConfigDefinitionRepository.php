<?php

namespace App\Repositories;

use App\Models\UserConfigDefinition;
use Illuminate\Support\Collection;

class UserConfigDefinitionRepository extends BaseRepository
{
    public function __construct(UserConfigDefinition $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function categoryId()
    {
        return UserConfigDefinition::CATEGORY_ID;
    }

    public function configKey()
    {
        return UserConfigDefinition::CONFIG_KEY;
    }

    public function configName()
    {
        return UserConfigDefinition::CONFIG_NAME;
    }

    public function description()
    {
        return UserConfigDefinition::DESCRIPTION;
    }

    public function valueType()
    {
        return UserConfigDefinition::VALUE_TYPE;
    }

    public function defaultValue()
    {
        return UserConfigDefinition::DEFAULT_VALUE;
    }

    public function possibleValues()
    {
        return UserConfigDefinition::POSSIBLE_VALUES;
    }

    public function validationRules()
    {
        return UserConfigDefinition::VALIDATION_RULES;
    }

    public function isRequired()
    {
        return UserConfigDefinition::IS_REQUIRED;
    }

    public function isEditable()
    {
        return UserConfigDefinition::IS_EDITABLE;
    }

    public function isPrivate()
    {
        return UserConfigDefinition::IS_PRIVATE;
    }

    public function displayOrder()
    {
        return UserConfigDefinition::DISPLAY_ORDER;
    }

    public function uiComponent()
    {
        return UserConfigDefinition::UI_COMPONENT;
    }

    public function uiProps()
    {
        return UserConfigDefinition::UI_PROPS;
    }

    public function dependsOn()
    {
        return UserConfigDefinition::DEPENDS_ON;
    }

    public function isActive()
    {
        return UserConfigDefinition::IS_ACTIVE;
    }

    public function createdBy()
    {
        return UserConfigDefinition::CREATED_BY;
    }

    public function getResolvedConfigsForUser(int|string $userId): array
    {
        $definitions = $this->query()
            ->leftJoin('user_config_values as ucv', function ($join) use ($userId): void {
                $join->on('ucv.config_id', '=', 'user_config_definitions.id')
                    ->where('ucv.user_id', '=', $userId);
            })
            ->orderBy($this->displayOrder())
            ->orderBy($this->id())
            ->get([
                'user_config_definitions.id',
                'user_config_definitions.config_key',
                'user_config_definitions.config_name',
                'user_config_definitions.default_value',
                'ucv.value as user_value',
            ]);

        return $definitions->map(static function ($row): array {
            $userValue = $row->user_value;
            $resolvedValue = is_string($userValue) && trim($userValue) !== ''
                ? $userValue
                : $row->default_value;

            return [
                'id' => $row->id,
                'key' => $row->config_key,
                'name' => $row->config_name,
                'value' => $resolvedValue,
                'default_value' => $row->default_value,
                'user_value' => $userValue,
            ];
        })->values()->all();
    }

    public function getConfigIdMapByKeys(array $keys): Collection
    {
        if ($keys === []) {
            return collect();
        }

        return $this->query()
            ->whereIn($this->configKey(), $keys)
            ->pluck($this->id(), $this->configKey());
    }
}
