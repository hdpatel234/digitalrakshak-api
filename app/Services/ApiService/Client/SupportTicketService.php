<?php

namespace App\Services\ApiService\Client;

use App\Repositories\SupportDepartmentRepository;
use App\Repositories\SupportPriorityRepository;
use App\Repositories\SupportTicketRepository;
use App\Services\BaseService;
use App\Services\ClientService;
use App\Services\Support\SupportManager;
use App\Services\UserService;
use Illuminate\Support\Str;

/**
 * @property SupportTicketRepository $repository
 */
class SupportTicketService extends BaseService
{
    protected SupportDepartmentRepository $departmentRepository;
    protected SupportPriorityRepository $priorityRepository;
    protected ClientService $clientService;
    protected SupportManager $supportManager;
    protected UserService $userService;

    public function __construct(
        SupportTicketRepository $repository,
        SupportDepartmentRepository $departmentRepository,
        SupportPriorityRepository $priorityRepository,
        ClientService $clientService,
        SupportManager $supportManager,
        UserService $userService
    ) {
        $this->departmentRepository = $departmentRepository;
        $this->priorityRepository = $priorityRepository;
        $this->clientService = $clientService;
        $this->supportManager = $supportManager;
        $this->userService = $userService;
        parent::__construct($repository);
    }

    public function getTickets(array $params, int $clientId): array
    {
        $ticketTable = $this->repository->query()->getModel()->getTable();
        $statusColumn = $this->repository->status();
        $clientIdColumn = $this->repository->clientId();
        $ticketNumberColumn = $this->repository->ticketNumber();
        $subjectColumn = $this->repository->subject();
        $createdAtColumn = $this->repository->query()->getModel()->getCreatedAtColumn();

        $qualifiedStatusColumn = $ticketTable . '.' . $statusColumn;
        $qualifiedClientIdColumn = $ticketTable . '.' . $clientIdColumn;

        $query = $this->query()
            ->with(['order:id,order_number', 'department:id,name', 'priority:id,name'])
            ->where($qualifiedClientIdColumn, $clientId);

        if (isset($params['limit']) && !isset($params['per_page'])) {
            $params['per_page'] = $params['limit'];
        }
        if (isset($params['start_date'])) {
            $params['date_from'] = $params['start_date'];
        }
        if (isset($params['end_date'])) {
            $params['date_to'] = $params['end_date'];
        }

        $parseIds = function ($value) {
            $raw = is_array($value) ? $value : explode(',', (string) $value);
            return collect($raw)->map(fn($id) => (int) $id)->filter(fn($id) => $id > 0)->values()->all();
        };

        $result = $this->datatable(
            query: $query,
            params: $params,
            config: [
                'searchable' => [
                    $ticketTable . '.' . $ticketNumberColumn,
                    $ticketTable . '.' . $subjectColumn,
                    function (\Illuminate\Database\Eloquent\Builder $builder, string $search) {
                        $builder->orWhereHas('order', function ($q) use ($search) {
                            $q->where('order_number', 'like', '%' . $search . '%');
                        });
                    }
                ],
                'status_column' => $qualifiedStatusColumn,
                'date_column' => $ticketTable . '.' . $createdAtColumn,
                'allowed_filters' => [
                    'status' => function ($builder, $value) use ($qualifiedStatusColumn) {
                        $raw = is_array($value) ? $value : explode(',', (string) $value);
                        $statuses = collect($raw)->filter()->values()->all();
                        if ($statuses !== []) {
                            $builder->whereIn($qualifiedStatusColumn, $statuses);
                        }
                    },
                    'department_id' => function ($builder, $value) use ($ticketTable, $parseIds) {
                        $ids = $parseIds($value);
                        if ($ids !== []) $builder->whereIn($ticketTable . '.' . $this->repository->departmentId(), $ids);
                    },
                    'priority_id' => function ($builder, $value) use ($ticketTable, $parseIds) {
                        $ids = $parseIds($value);
                        if ($ids !== []) $builder->whereIn($ticketTable . '.' . $this->repository->priorityId(), $ids);
                    },
                    'order_id' => function ($builder, $value) use ($ticketTable, $parseIds) {
                        $ids = $parseIds($value);
                        if ($ids !== []) $builder->whereIn($ticketTable . '.' . $this->repository->orderId(), $ids);
                    },
                ],
                'allowed_sorts' => [
                    $ticketTable . '.' . $this->repository->query()->getModel()->getKeyName(),
                    $ticketTable . '.' . $ticketNumberColumn,
                    $ticketTable . '.' . $createdAtColumn,
                ],
                'default_sort_by' => $ticketTable . '.' . $createdAtColumn,
                'default_sort_direction' => 'desc',
                'default_per_page' => 10,
                'max_per_page' => 100,
            ]
        );

        if (is_array($result) && isset($result['list'])) {
            $result['list'] = collect($result['list'])->map(function ($row) {
                $item = is_array($row) ? $row : $row->toArray();
                $item['order_number'] = $item['order']['order_number'] ?? null;
                $item['department_name'] = $item['department']['name'] ?? null;
                $item['priority_name'] = $item['priority']['name'] ?? null;
                return $item;
            })->all();
        }

        $statusList = [
            ['key' => 'open', 'name' => 'Open'],
            ['key' => 'pending', 'name' => 'Pending'],
            ['key' => 'resolved', 'name' => 'Resolved'],
            ['key' => 'closed', 'name' => 'Closed'],
        ];

        $departments = $this->departmentRepository->getActiveDepartments()->map(function ($d) {
            return ['id' => $d->id, 'name' => $d->name];
        })->values()->all();

        $priorities = $this->priorityRepository->getActivePriorities()->map(function ($p) {
            return ['id' => $p->id, 'name' => $p->name];
        })->values()->all();

        $orders = \App\Models\CandidateOrder::where('client_id', $clientId)
            ->select('id', 'order_number')
            ->orderBy('id', 'desc')
            ->get()
            ->toArray();

        $lists = [
            'status_list' => $statusList,
            'departments' => $departments,
            'priorities' => $priorities,
            'orders' => $orders,
        ];

        if (is_array($result)) {
            $result = array_merge($result, $lists);
        } else {
            $result = array_merge(['list' => $result], $lists);
        }

        return $result;
    }

    public function getTicket(int $id, int $clientId): array
    {
        $ticket = $this->query()
            ->with(['order:id,order_number', 'department:id,name', 'priority:id,name'])
            ->where($this->repository->clientId(), $clientId)
            ->where($this->repository->id(), $id)
            ->first();

        if (!$ticket) {
            throw new \Exception("Ticket not found", 404);
        }

        $result = $ticket->toArray();
        $result['order_number'] = $ticket->order->order_number ?? null;
        $result['department_name'] = $ticket->department->name ?? null;
        $result['priority_name'] = $ticket->priority->name ?? null;
        $result['created_at_human'] = \App\Models\BaseModel::formatTimeAgo($ticket->created_at);

        return $result;
    }

    public function getTicketConversations(int $id, int $clientId): array
    {
        $ticket = $this->query()
            ->with(['order:id,order_number', 'department:id,name', 'priority:id,name'])
            ->where($this->repository->clientId(), $clientId)
            ->where($this->repository->id(), $id)
            ->first();

        if (!$ticket) {
            throw new \Exception("Ticket not found", 404);
        }

        $externalTicketId = $ticket->{$this->repository->externalTicketId()};
        $supportConfigId = $ticket->{$this->repository->supportConfigId()};

        $threads = [];

        if ($externalTicketId && $supportConfigId) {
            try {
                $supportConfig = \App\Models\SupportConfig::find($supportConfigId);
                /** @var \App\Models\Client $client */
                $client = $this->clientService->query()->where($this->clientService->id(), $clientId)->first();

                if ($supportConfig && $client) {
                    $externalData = $this->supportManager->getTicket($client, $externalTicketId, $supportConfig);

                    if (isset($externalData['ticket']['threads']) && is_array($externalData['ticket']['threads'])) {
                        foreach ($externalData['ticket']['threads'] as $thread) {
                            $createdAt = $thread['createdAt'] ?? null;
                            $formattedDate = null;
                            $timeAgo = null;

                            if ($createdAt) {
                                try {
                                    $carbonDate = \App\Models\BaseModel::convertToUserTimezone($createdAt);
                                    $formattedDate = \App\Models\BaseModel::formatToUserDateTime($carbonDate);
                                    $timeAgo = \App\Models\BaseModel::formatTimeAgo($carbonDate);
                                } catch (\Exception $e) {
                                    $formattedDate = $createdAt;
                                }
                            }

                            $threads[] = [
                                'id' => $thread['id'] ?? null,
                                'message' => html_entity_decode($thread['message'] ?? ''),
                                'sender_type' => $thread['createdBy'] ?? 'customer',
                                'sender_name' => $thread['user']['name'] ?? 'Unknown',
                                'sender_email' => $thread['user']['email'] ?? null,
                                'created_at' => $formattedDate,
                                'time_ago' => $timeAgo,
                                'attachments' => $thread['attachments'] ?? [],
                            ];
                        }
                    }
                }
            } catch (\Exception $e) {
                addErrorLog("Failed to fetch external ticket threads for ticket ID: {$id}. Error: " . $e->getMessage());
            }
        }

        return $threads;
    }

    public function createTicket(array $payload, int $clientId, ?object $user): array
    {
        /** @var \App\Models\Client $client */
        $client = $this->clientService->query()->where($this->clientService->id(), $clientId)->first();

        if (!$client) {
            throw new \Exception("Client not found", 404);
        }

        $email = $client->{$this->clientService->email()} ?? ($user->email ?? '');
        $name = $user ? ($user->{$this->userService->firstName()} .  $user->{$this->userService->lastName()}) : 'Client';

        $uvdeskPayload = [
            'message' => $payload['message'] ?? '',
            'actAsType' => 'customer',
            'actAsEmail' => $email,
            'name' => $name,
            'subject' => $payload['title'] ?? '',
            'from' => $email,
            'attachments' => $payload['attachments'] ?? $payload['attachment'] ?? [],
        ];

        try {
            $externalResponse = $this->supportManager->createTicket($client, $uvdeskPayload);
        } catch (\Exception $e) {
            throw new \Exception("Failed to create ticket with support provider: " . $e->getMessage(), 500);
        }

        $externalTicketId = $externalResponse['ticket']['id'] ?? $externalResponse['id'] ?? null;
        $ticketData = is_array($externalResponse) ? json_encode($externalResponse) : null;

        $ticketNumber = strtoupper(Str::random(3)) . rand(100, 999);

        $platformId = (int) $client->default_support_config_id;
        $supportConfigId = null;

        if ($platformId > 0) {
            $supportConfig = \App\Models\SupportConfig::where('support_platform_id', $platformId)
                ->where(function ($query) {
                    $query->where('is_default', true)
                        ->orWhere('status', 'active');
                })
                ->first()
                ?? \App\Models\SupportConfig::where('support_platform_id', $platformId)
                ->first();

            $supportConfigId = $supportConfig ? $supportConfig->id : null;
        }

        $ticket = $this->repository->create([
            $this->repository->clientId() => $clientId,
            $this->repository->supportConfigId() => $supportConfigId,
            $this->repository->orderId() => $payload['order'] ?? null,
            $this->repository->externalTicketId() => (string) $externalTicketId,
            $this->repository->departmentId() => $payload['department'] ?? null,
            $this->repository->priorityId() => $payload['priority'] ?? null,
            $this->repository->ticketNumber() => $ticketNumber,
            $this->repository->subject() => $payload['title'] ?? '',
            $this->repository->description() => $payload['message'] ?? '',
            $this->repository->status() => 'open',
            $this->repository->ticketData() => $ticketData,
            $this->repository->createdBy() => $user?->id,
        ]);

        return $ticket->toArray();
    }

    public function addTicketReply(int $ticketId, string $message, int $clientId, ?object $user, array $attachments = []): array
    {
        $ticket = $this->query()
            ->where($this->repository->clientId(), $clientId)
            ->where($this->repository->id(), $ticketId)
            ->first();

        if (!$ticket) {
            throw new \Exception("Ticket not found", 404);
        }

        $externalTicketId = $ticket->{$this->repository->externalTicketId()};
        $supportConfigId = $ticket->{$this->repository->supportConfigId()};

        if (!$externalTicketId || !$supportConfigId) {
            throw new \Exception("External ticket reference not found", 422);
        }

        /** @var \App\Models\Client $client */
        $client = $this->clientService->query()->where($this->clientService->id(), $clientId)->first();

        if (!$client) {
            throw new \Exception("Client not found", 404);
        }

        $supportConfig = \App\Models\SupportConfig::find($supportConfigId);
        if (!$supportConfig) {
            throw new \Exception("Support configuration not found", 404);
        }

        $email = $client->{$this->clientService->email()} ?? ($user->email ?? '');

        $payload = [
            'message' => $message,
            'actAsType' => 'customer',
            'actAsEmail' => $email,
            'threadType' => 'reply',
            'attachments' => $attachments
        ];

        return $this->supportManager->addReply($client, $externalTicketId, $payload, $supportConfig);
    }

    public function getDepartments()
    {
        return $this->departmentRepository->getActiveDepartments();
    }

    public function getPriorities()
    {
        return $this->priorityRepository->getActivePriorities();
    }
}
