<?php

namespace App\Services\ApiService\Client;

use App\Models\Candidate;
use App\Models\CandidateInvitation;
use App\Models\Package;
use App\Models\Service;
use App\Models\CandidateOrder;
use App\Models\Invoice;
use App\Models\SupportTicket;

class SearchService
{
    public function performSearch(string $queryStr, int $clientId): array
    {
        if (empty($queryStr)) {
            return [];
        }

        $results = [];

        // 1. Search Candidates
        $candidates = Candidate::where('client_id', $clientId)
            ->where(function ($q) use ($queryStr) {
                $q->where('first_name', 'like', "%{$queryStr}%")
                  ->orWhere('last_name', 'like', "%{$queryStr}%")
                  ->orWhere('email', 'like', "%{$queryStr}%")
                  ->orWhere('phone', 'like', "%{$queryStr}%");
            })
            ->limit(5)
            ->get();

        if ($candidates->isNotEmpty()) {
            $candidateData = $candidates->map(function ($c) {
                $fullName = trim("{$c->first_name} {$c->last_name}");
                return [
                    'key' => "candidate-{$c->id}",
                    'path' => "/candidates/details/{$c->id}",
                    'title' => $fullName . ($c->email ? " ({$c->email})" : ""),
                    'icon' => 'customers',
                    'category' => 'Candidates',
                    'categoryTitle' => 'Candidates',
                ];
            })->toArray();

            $results[] = [
                'title' => 'Candidates',
                'data' => $candidateData,
            ];
        }

        // 2. Search Invitations
        $invitations = CandidateInvitation::where('client_id', $clientId)
            ->where(function ($query) use ($queryStr) {
                $query->whereHas('candidate', function ($q) use ($queryStr) {
                    $q->where('email', 'like', "%{$queryStr}%")
                      ->orWhere('first_name', 'like', "%{$queryStr}%")
                      ->orWhere('last_name', 'like', "%{$queryStr}%");
                })
                ->orWhere('invitation_token', 'like', "%{$queryStr}%");
            })
            ->with('candidate')
            ->limit(5)
            ->get();

        if ($invitations->isNotEmpty()) {
            $invitationData = $invitations->map(function ($inv) {
                $candidateName = $inv->candidate ? trim("{$inv->candidate->first_name} {$inv->candidate->last_name}") : "Unknown";
                return [
                    'key' => "invitation-{$inv->id}",
                    'path' => "/invitations/all", 
                    'title' => "Invitation for {$candidateName} (Token: {$inv->invitation_token})",
                    'icon' => 'invitations',
                    'category' => 'Invitations',
                    'categoryTitle' => 'Invitations',
                ];
            })->toArray();

            $results[] = [
                'title' => 'Invitations',
                'data' => $invitationData,
            ];
        }

        // 3. Search Packages
        $packages = Package::where('client_id', $clientId)
            ->where('package_name', 'like', "%{$queryStr}%")
            ->limit(5)
            ->get();

        if ($packages->isNotEmpty()) {
            $packageData = $packages->map(function ($p) {
                return [
                    'key' => "package-{$p->id}",
                    'path' => "/packages/list", 
                    'title' => $p->package_name,
                    'icon' => 'products',
                    'category' => 'Packages',
                    'categoryTitle' => 'Packages',
                ];
            })->toArray();

            $results[] = [
                'title' => 'Packages',
                'data' => $packageData,
            ];
        }

        // 4. Search Services
        $services = Service::where('service_name', 'like', "%{$queryStr}%")
            ->limit(5)
            ->get();

        if ($services->isNotEmpty()) {
            $serviceData = $services->map(function ($s) {
                return [
                    'key' => "service-{$s->id}",
                    'path' => "/services/list", 
                    'title' => $s->service_name,
                    'icon' => 'services',
                    'category' => 'Services',
                    'categoryTitle' => 'Services',
                ];
            })->toArray();

            $results[] = [
                'title' => 'Services',
                'data' => $serviceData,
            ];
        }

        // 5. Search Orders
        $orders = CandidateOrder::where('client_id', $clientId)
            ->where(function ($query) use ($queryStr) {
                $query->where('order_number', 'like', "%{$queryStr}%")
                      ->orWhere('payment_reference', 'like', "%{$queryStr}%")
                      ->orWhereHas('paymentTransactions', function ($q) use ($queryStr) {
                          $q->where('gateway_payment_id', 'like', "%{$queryStr}%")
                            ->orWhere('gateway_transaction_id', 'like', "%{$queryStr}%")
                            ->orWhere('bank_reference', 'like', "%{$queryStr}%");
                      });
            })
            ->limit(5)
            ->get();

        if ($orders->isNotEmpty()) {
            $orderData = $orders->map(function ($o) {
                return [
                    'key' => "order-{$o->id}",
                    'path' => "/orders/details/{$o->id}", 
                    'title' => "Order {$o->order_number}",
                    'icon' => 'orders',
                    'category' => 'Orders',
                    'categoryTitle' => 'Orders',
                ];
            })->toArray();

            $results[] = [
                'title' => 'Orders',
                'data' => $orderData,
            ];
        }

        // 6. Search Invoices
        $invoices = Invoice::where('client_id', $clientId)
            ->where('invoice_number', 'like', "%{$queryStr}%")
            ->limit(5)
            ->get();

        if ($invoices->isNotEmpty()) {
            $invoiceData = $invoices->map(function ($i) {
                return [
                    'key' => "invoice-{$i->id}",
                    'path' => "/billing/invoices?search={$i->invoice_number}",
                    'title' => "Invoice {$i->invoice_number}",
                    'icon' => 'invoices',
                    'category' => 'Invoices',
                    'categoryTitle' => 'Invoices',
                ];
            })->toArray();

            $results[] = [
                'title' => 'Invoices',
                'data' => $invoiceData,
            ];
        }

        // 7. Search Support Tickets
        $tickets = SupportTicket::where('client_id', $clientId)
            ->where(function ($query) use ($queryStr) {
                $query->where('ticket_number', 'like', "%{$queryStr}%")
                      ->orWhere('subject', 'like', "%{$queryStr}%");
            })
            ->limit(5)
            ->get();

        if ($tickets->isNotEmpty()) {
            $ticketData = $tickets->map(function ($t) {
                return [
                    'key' => "ticket-{$t->id}",
                    'path' => "/support/ticket-details/{$t->id}",
                    'title' => "Ticket {$t->ticket_number}: {$t->subject}",
                    'icon' => 'help', 
                    'category' => 'Support Tickets',
                    'categoryTitle' => 'Support Tickets',
                ];
            })->toArray();

            $results[] = [
                'title' => 'Support Tickets',
                'data' => $ticketData,
            ];
        }

        return $results;
    }
}
