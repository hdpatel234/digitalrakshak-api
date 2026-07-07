<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Service;
use App\Models\Package;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');
        
        if (!$query) {
            return response()->json([]);
        }

        $results = [];

        // Search Clients
        $clients = Client::where('company_name', 'like', "%{$query}%")
                         ->orWhere('contact_person', 'like', "%{$query}%")
                         ->orWhere('email', 'like', "%{$query}%")
                         ->take(5)
                         ->get();
        if ($clients->count() > 0) {
            $data = $clients->map(function ($client) {
                return [
                    'title' => $client->company_name ?? $client->contact_person,
                    'url' => '/clients/edit/' . $client->id,
                    'icon' => 'users',
                    'category' => 'Clients',
                    'categoryTitle' => 'Clients',
                ];
            });
            $results[] = [
                'title' => 'Clients',
                'data' => $data
            ];
        }

        // Search Services
        $services = Service::where('service_name', 'like', "%{$query}%")
                           ->take(5)
                           ->get();
        if ($services->count() > 0) {
            $data = $services->map(function ($service) {
                return [
                    'title' => $service->service_name,
                    'url' => '/services/edit/' . $service->id,
                    'icon' => 'server',
                    'category' => 'Services',
                    'categoryTitle' => 'Services',
                ];
            });
            $results[] = [
                'title' => 'Services',
                'data' => $data
            ];
        }

        // Search Packages
        $packages = Package::where('package_name', 'like', "%{$query}%")
                           ->take(5)
                           ->get();
        if ($packages->count() > 0) {
            $data = $packages->map(function ($package) {
                return [
                    'title' => $package->package_name,
                    'url' => '/packages/edit/' . $package->id,
                    'icon' => 'package',
                    'category' => 'Packages',
                    'categoryTitle' => 'Packages',
                ];
            });
            $results[] = [
                'title' => 'Packages',
                'data' => $data
            ];
        }

        return response()->json($results);
    }
}
