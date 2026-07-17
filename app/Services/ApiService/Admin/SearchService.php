<?php

namespace App\Services\ApiService\Admin;

use App\Repositories\ClientRepository;
use App\Repositories\CandidateRepository;
use App\Repositories\SupportTicketRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\CandidateOrderRepository;
use App\Repositories\ServiceRepository;
use App\Repositories\PackageRepository;

class SearchService
{
    public function __construct(
        protected ClientRepository $clientRepo,
        protected CandidateRepository $candidateRepo,
        protected SupportTicketRepository $supportTicketRepo,
        protected InvoiceRepository $invoiceRepo,
        protected CandidateOrderRepository $candidateOrderRepo,
        protected ServiceRepository $serviceRepo,
        protected PackageRepository $packageRepo
    ) {}
    public function search(string $query): array
    {
        if (empty($query)) {
            return [];
        }

        $results = [];

        // Search Clients
        $clients = $this->clientRepo->query()->where('company_name', 'like', "%{$query}%")
                         ->orWhere('email', 'like', "%{$query}%")
                         ->orWhere('address', 'like', "%{$query}%")
                         ->take(5)
                         ->get();
        if ($clients->count() > 0) {
            $data = $clients->map(function ($client) {
                return [
                    'title' => $client->company_name,
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

        // Search Candidates
        $candidates = $this->candidateRepo->query()->where('first_name', 'like', "%{$query}%")
                         ->orWhere('last_name', 'like', "%{$query}%")
                         ->orWhere('email', 'like', "%{$query}%")
                         ->orWhere('address', 'like', "%{$query}%")
                         ->take(5)
                         ->get();
        if ($candidates->count() > 0) {
            $data = $candidates->map(function ($candidate) {
                return [
                    'title' => trim($candidate->first_name . ' ' . $candidate->last_name),
                    'url' => '/candidates/edit/' . $candidate->id,
                    'icon' => 'user',
                    'category' => 'Candidates',
                    'categoryTitle' => 'Candidates',
                ];
            });
            $results[] = [
                'title' => 'Candidates',
                'data' => $data
            ];
        }

        // Search Support Tickets
        $tickets = $this->supportTicketRepo->query()->where('ticket_number', 'like', "%{$query}%")
                         ->take(5)
                         ->get();
        if ($tickets->count() > 0) {
            $data = $tickets->map(function ($ticket) {
                return [
                    'title' => $ticket->ticket_number,
                    'url' => '/support/tickets/' . $ticket->id,
                    'icon' => 'ticket',
                    'category' => 'Tickets',
                    'categoryTitle' => 'Tickets',
                ];
            });
            $results[] = [
                'title' => 'Tickets',
                'data' => $data
            ];
        }

        // Search Invoices
        $invoices = $this->invoiceRepo->query()->where('invoice_number', 'like', "%{$query}%")
                         ->take(5)
                         ->get();
        if ($invoices->count() > 0) {
            $data = $invoices->map(function ($invoice) {
                return [
                    'title' => $invoice->invoice_number,
                    'url' => '/invoices/view/' . $invoice->id,
                    'icon' => 'file-text',
                    'category' => 'Invoices',
                    'categoryTitle' => 'Invoices',
                ];
            });
            $results[] = [
                'title' => 'Invoices',
                'data' => $data
            ];
        }

        // Search Orders
        $orders = $this->candidateOrderRepo->query()->where('order_number', 'like', "%{$query}%")
                         ->take(5)
                         ->get();
        if ($orders->count() > 0) {
            $data = $orders->map(function ($order) {
                return [
                    'title' => $order->order_number,
                    'url' => '/orders/view/' . $order->id,
                    'icon' => 'shopping-cart',
                    'category' => 'Orders',
                    'categoryTitle' => 'Orders',
                ];
            });
            $results[] = [
                'title' => 'Orders',
                'data' => $data
            ];
        }

        // Search Services
        $services = $this->serviceRepo->query()->where('service_name', 'like', "%{$query}%")
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
        $packages = $this->packageRepo->query()->where('package_name', 'like', "%{$query}%")
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

        return $results;
    }
}
