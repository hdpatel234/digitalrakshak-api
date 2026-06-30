<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CountryStateCityService;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
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
            Country::updateOrCreate(
                ['iso_code_2' => $countryData['iso2']],
                [
                    'name' => $countryData['name'],
                    'iso_code_3' => $countryData['iso3'] ?? null,
                    'phone_code' => $countryData['phonecode'] ?? null,
                    'currency_code' => $countryData['currency'] ?? null,
                    'capital' => $countryData['capital'] ?? null,
                    'continent' => $countryData['region'] ?? null,
                    'latitude' => $countryData['latitude'] ?? null,
                    'longitude' => $countryData['longitude'] ?? null,
                    'timezones' => $countryData['timezones'] ?? null,
                ]
            );
        });
        
        $this->newLine();
        $this->info("Countries synced.");
    }

    protected function syncStates()
    {
        $this->info("Fetching states...");
        $countries = Country::all();

        foreach ($countries as $country) {
            try {
                $states = $this->apiService->getStates($country->iso_code_2);

                if (!empty($states)) {
                    $this->info("Syncing states for {$country->name}...");
                    
                    foreach ($states as $stateData) {
                        State::updateOrCreate(
                            [
                                'country_id' => $country->id,
                                'name' => $stateData['name'],
                            ],
                            [
                                'code' => $stateData['iso2'] ?? null,
                                'latitude' => $stateData['latitude'] ?? null,
                                'longitude' => $stateData['longitude'] ?? null,
                            ]
                        );
                    }
                }
            } catch (Exception $e) {
                // If we run out of API keys while looping, bubble up the exception
                if (str_contains($e->getMessage(), 'daily limit')) {
                    throw $e;
                }
                $this->error("Failed to fetch states for {$country->name}: " . $e->getMessage());
            }
        }
        $this->info("States synced.");
    }

    protected function syncCities()
    {
        $this->info("Fetching cities...");
        // Order by cities_synced_at ascending so we prioritize states that haven't been synced recently
        $states = State::whereNotNull('code')->orderBy('cities_synced_at', 'asc')->get();

        foreach ($states as $state) {
            $country = Country::find($state->country_id);
            if (!$country || !$country->iso_code_2) continue;

            try {
                $cities = $this->apiService->getCities($country->iso_code_2, $state->code);

                if (!empty($cities)) {
                    $this->info("Syncing cities for {$state->name}, {$country->name}...");
                    
                    foreach ($cities as $cityData) {
                        City::updateOrCreate(
                            [
                                'state_id' => $state->id,
                                'name' => $cityData['name'],
                            ],
                            [
                                'country_id' => $country->id,
                                'latitude' => $cityData['latitude'] ?? null,
                                'longitude' => $cityData['longitude'] ?? null,
                            ]
                        );
                    }
                }

                // Mark the state as synced
                $state->update(['cities_synced_at' => now()]);
            } catch (Exception $e) {
                if (str_contains($e->getMessage(), 'daily limit')) {
                    $this->warn("API Limit reached. Will continue from {$state->name} next time.");
                    throw $e;
                }
                $this->error("Failed to fetch cities for {$state->name}: " . $e->getMessage());
            }
        }
        $this->info("Cities synced.");
    }
}
