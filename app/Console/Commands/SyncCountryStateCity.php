<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CountryStateCityService;
use App\Services\CountryService;
use App\Services\StateService;
use App\Services\CityService;
use Exception;

class SyncCountryStateCity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'csc:sync {type=all : The type of data to sync (countries, states, cities, all)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Country, State, and City data from the CountryStateCity API';

    protected $apiService;

    public function __construct(
        protected CountryService $countryService,
        protected StateService $stateService,
        protected CityService $cityService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(CountryStateCityService $apiService)
    {
        $this->apiService = $apiService;
        $type = $this->argument('type');

        try {
            if ($type === 'countries' || $type === 'all') {
                $this->syncCountries();
            }

            if ($type === 'states' || $type === 'all') {
                $this->syncStates();
            }

            if ($type === 'cities' || $type === 'all') {
                $this->syncCities();
            }
        } catch (Exception $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }

        $this->info("Sync completed successfully.");
        return self::SUCCESS;
    }

    protected function syncCountries()
    {
        $this->info("Fetching countries...");
        $countries = $this->apiService->getCountries();

        $this->withProgressBar($countries, function ($countryData) {
            $this->countryService->query()->updateOrCreate(
                [$this->countryService->isoCode2() => $countryData['iso2']],
                [
                    $this->countryService->name() => $countryData['name'],
                    $this->countryService->isoCode3() => $countryData['iso3'] ?? null,
                    $this->countryService->phoneCode() => $countryData['phonecode'] ?? null,
                    $this->countryService->currencyCode() => $countryData['currency'] ?? null,
                    $this->countryService->capital() => $countryData['capital'] ?? null,
                    $this->countryService->continent() => $countryData['region'] ?? null,
                    $this->countryService->latitude() => $countryData['latitude'] ?? null,
                    $this->countryService->longitude() => $countryData['longitude'] ?? null,
                    $this->countryService->timezones() => $countryData['timezones'] ?? null,
                ]
            );
        });
        
        $this->newLine();
        $this->info("Countries synced.");
    }

    protected function syncStates()
    {
        $this->info("Fetching states...");
        $countries = $this->countryService->all();

        foreach ($countries as $country) {
            try {
                $states = $this->apiService->getStates($country->{$this->countryService->isoCode2()});

                if (!empty($states)) {
                    $this->info("Syncing states for {$country->{$this->countryService->name()}}...");
                    
                    foreach ($states as $stateData) {
                        $this->stateService->query()->updateOrCreate(
                            [
                                $this->stateService->countryId() => $country->{$this->countryService->id()},
                                $this->stateService->name() => $stateData['name'],
                            ],
                            [
                                $this->stateService->code() => $stateData['iso2'] ?? null,
                                $this->stateService->latitude() => $stateData['latitude'] ?? null,
                                $this->stateService->longitude() => $stateData['longitude'] ?? null,
                            ]
                        );
                    }
                }
            } catch (Exception $e) {
                // If we run out of API keys while looping, bubble up the exception
                if (str_contains($e->getMessage(), 'daily limit')) {
                    throw $e;
                }
                $this->error("Failed to fetch states for {$country->{$this->countryService->name()}}: " . $e->getMessage());
            }
        }
        $this->info("States synced.");
    }

    protected function syncCities()
    {
        $this->info("Fetching cities...");
        // Order by cities_synced_at ascending so we prioritize states that haven't been synced recently
        $states = $this->stateService->query()->whereNotNull($this->stateService->code())->orderBy($this->stateService->citiesSyncedAt(), 'asc')->get();

        foreach ($states as $state) {
            $country = $this->countryService->find($state->{$this->stateService->countryId()});
            if (!$country || !$country->{$this->countryService->isoCode2()}) continue;

            try {
                $cities = $this->apiService->getCities($country->{$this->countryService->isoCode2()}, $state->{$this->stateService->code()});

                if (!empty($cities)) {
                    $this->info("Syncing cities for {$state->{$this->stateService->name()}}, {$country->{$this->countryService->name()}}...");
                    
                    foreach ($cities as $cityData) {
                        $this->cityService->query()->updateOrCreate(
                            [
                                $this->cityService->stateId() => $state->{$this->stateService->id()},
                                $this->cityService->name() => $cityData['name'],
                            ],
                            [
                                $this->cityService->countryId() => $country->{$this->countryService->id()},
                                $this->cityService->latitude() => $cityData['latitude'] ?? null,
                                $this->cityService->longitude() => $cityData['longitude'] ?? null,
                            ]
                        );
                    }
                }

                // Mark the state as synced
                $state->update([$this->stateService->citiesSyncedAt() => now()]);
            } catch (Exception $e) {
                if (str_contains($e->getMessage(), 'daily limit')) {
                    $this->warn("API Limit reached. Will continue from {$state->{$this->stateService->name()}} next time.");
                    throw $e;
                }
                $this->error("Failed to fetch cities for {$state->{$this->stateService->name()}}: " . $e->getMessage());
            }
        }
        $this->info("Cities synced.");
    }
}
