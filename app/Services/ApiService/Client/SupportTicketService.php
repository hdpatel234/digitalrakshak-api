<?php

namespace App\Services\ApiService\Client;

use App\Repositories\SupportDepartmentRepository;
use App\Repositories\SupportPriorityRepository;
use App\Repositories\SupportTicketRepository;
use App\Repositories\OrderRepository;
use App\Repositories\SupportTicketConversationRepository;
use App\Enums\SupportTicketStatus;
use App\Services\BaseService;
use App\Services\ClientService;
use App\Services\UserService;
use Illuminate\Support\Str;

/**
 * @property SupportTicketRepository $repository
 */
class SupportTicketService extends BaseService
{
    public function __construct(
        SupportTicketRepository $repository,
        protected SupportDepartmentRepository $departmentRepository,
        protected SupportPriorityRepository $priorityRepository,
        protected OrderRepository $orderRepository,
        protected SupportTicketConversationRepository $conversationRepository,
        protected ClientService $clientService,
        protected UserService $userService
    ) {
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
            ->select($ticketTable . '.*')
            ->selectSub(function ($q) {
                $q->select($this->orderRepository->orderNumber())
                    ->from($this->orderRepository->query()->getModel()->getTable())
                    ->whereColumn($this->orderRepository->query()->getModel()->getTable() . '.' . $this->orderRepository->id(), $this->repository->query()->getModel()->getTable() . '.' . $this->repository->orderId());
            }, 'order_number')
            ->selectSub(function ($q) {
                $q->select('name')
                    ->from($this->departmentRepository->query()->getModel()->getTable())
                    ->whereColumn($this->departmentRepository->query()->getModel()->getTable() . '.' . $this->departmentRepository->id(), $this->repository->query()->getModel()->getTable() . '.' . $this->repository->departmentId());
            }, 'department_name')
            ->selectSub(function ($q) {
                $q->select('name')
                    ->from($this->priorityRepository->query()->getModel()->getTable())
                    ->whereColumn($this->priorityRepository->query()->getModel()->getTable() . '.' . $this->priorityRepository->id(), $this->repository->query()->getModel()->getTable() . '.' . $this->repository->priorityId());
            }, 'priority_name')
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
                    function (\Illuminate\Database\Eloquent\Builder $builder, string $search) use ($ticketTable) {
                        $builder->orWhereIn($ticketTable . '.' . $this->repository->orderId(), function ($q) use ($search) {
                            $q->select($this->orderRepository->id())
                                ->from($this->orderRepository->query()->getModel()->getTable())
                                ->where($this->orderRepository->orderNumber(), 'like', '%' . $search . '%');
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
                return is_array($row) ? $row : $row->toArray();
            })->all();
        }

        $statusList = collect(SupportTicketStatus::cases())->map(fn($status) => [
            'key' => $status->value,
            'name' => $status->name()
        ])->toArray();

        $departments = $this->departmentRepository->getActiveDepartments()->map(function ($d) {
            return ['id' => $d->id, 'name' => $d->name];
        })->values()->all();

        $priorities = $this->priorityRepository->getActivePriorities()->map(function ($p) {
            return ['id' => $p->id, 'name' => $p->name];
        })->values()->all();

        $orders = $this->orderRepository->query()
            ->where($this->orderRepository->clientId(), $clientId)
            ->select($this->orderRepository->id(), $this->orderRepository->orderNumber())
            ->orderBy($this->orderRepository->id(), 'desc')
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
            ->select($this->repository->query()->getModel()->getTable() . '.*')
            ->selectSub(function ($q) {
                $q->select($this->orderRepository->orderNumber())
                    ->from($this->orderRepository->query()->getModel()->getTable())
                    ->whereColumn($this->orderRepository->query()->getModel()->getTable() . '.' . $this->orderRepository->id(), $this->repository->query()->getModel()->getTable() . '.' . $this->repository->orderId());
            }, 'order_number')
            ->selectSub(function ($q) {
                $q->select('name')
                    ->from($this->departmentRepository->query()->getModel()->getTable())
                    ->whereColumn($this->departmentRepository->query()->getModel()->getTable() . '.' . $this->departmentRepository->id(), $this->repository->query()->getModel()->getTable() . '.' . $this->repository->departmentId());
            }, 'department_name')
            ->selectSub(function ($q) {
                $q->select('name')
                    ->from($this->priorityRepository->query()->getModel()->getTable())
                    ->whereColumn($this->priorityRepository->query()->getModel()->getTable() . '.' . $this->priorityRepository->id(), $this->repository->query()->getModel()->getTable() . '.' . $this->repository->priorityId());
            }, 'priority_name')
            ->where($this->repository->clientId(), $clientId)
            ->where($this->repository->id(), $id)
            ->first();

        if (!$ticket) {
            throw new \Exception("Ticket not found", 404);
        }

        $result = $ticket->toArray();
        $result['created_at_human'] = \App\Models\BaseModel::formatTimeAgo($ticket->created_at);

        return $result;
    }

    public function getTicketConversations(int $id, int $clientId): array
    {
        $ticket = $this->query()
            ->select($this->repository->query()->getModel()->getTable() . '.*')
            ->selectSub(function ($q) {
                $q->select($this->orderRepository->orderNumber())
                    ->from($this->orderRepository->query()->getModel()->getTable())
                    ->whereColumn($this->orderRepository->query()->getModel()->getTable() . '.' . $this->orderRepository->id(), $this->repository->query()->getModel()->getTable() . '.' . $this->repository->orderId());
            }, 'order_number')
            ->selectSub(function ($q) {
                $q->select('name')
                    ->from($this->departmentRepository->query()->getModel()->getTable())
                    ->whereColumn($this->departmentRepository->query()->getModel()->getTable() . '.' . $this->departmentRepository->id(), $this->repository->query()->getModel()->getTable() . '.' . $this->repository->departmentId());
            }, 'department_name')
            ->selectSub(function ($q) {
                $q->select('name')
                    ->from($this->priorityRepository->query()->getModel()->getTable())
                    ->whereColumn($this->priorityRepository->query()->getModel()->getTable() . '.' . $this->priorityRepository->id(), $this->repository->query()->getModel()->getTable() . '.' . $this->repository->priorityId());
            }, 'priority_name')
            ->where($this->repository->clientId(), $clientId)
            ->where($this->repository->id(), $id)
            ->first();

        if (!$ticket) {
            throw new \Exception("Ticket not found", 404);
        }

        $threads = [];

        $conversations = $this->conversationRepository->query()
            ->where($this->conversationRepository->ticketId(), $ticket->id)
            ->orderBy($this->conversationRepository->createdAt(), 'asc')
            ->get();

        foreach ($conversations as $conversation) {
            $createdAt = $conversation->created_at;
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

            $attachments = $conversation->attachments;
            if (is_string($attachments)) {
                $attachments = json_decode($attachments, true);
            }
            if (!is_array($attachments)) {
                $attachments = [];
            }

            $threads[] = [
                'id' => $conversation->id,
                'message' => html_entity_decode($conversation->message ?? ''),
                'sender_type' => $conversation->sender_type ?? 'customer',
                'sender_name' => $conversation->sender_name ?? 'Unknown',
                'sender_email' => $conversation->sender_email ?? null,
                'created_at' => $formattedDate,
                'time_ago' => $timeAgo,
                'attachments' => $attachments,
            ];
        }

        return $threads;
    }

    protected function handleAttachments(array $attachments): array
    {
        $storedPaths = [];
        foreach ($attachments as $attachment) {
            if ($attachment instanceof \Illuminate\Http\UploadedFile) {
                $path = $attachment->store('tickets/attachments', 'public');
                $storedPaths[] = ['url' => '/storage/' . $path, 'name' => $attachment->getClientOriginalName()];
            }
        }
        return $storedPaths;
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

        $ticketNumber = strtoupper(Str::random(3)) . rand(100, 999);

        $attachments = $payload['attachments'] ?? $payload['attachment'] ?? [];
        $storedAttachments = $this->handleAttachments($attachments);

        $ticket = $this->repository->create([
            $this->repository->clientId() => $clientId,
            $this->repository->orderId() => $payload['order'] ?? null,
            $this->repository->departmentId() => $payload['department'] ?? null,
            $this->repository->priorityId() => $payload['priority'] ?? null,
            $this->repository->ticketNumber() => $ticketNumber,
            $this->repository->subject() => $payload['title'] ?? '',
            $this->repository->description() => $payload['message'] ?? '',
            $this->repository->status() => SupportTicketStatus::OPEN->value,
            $this->repository->createdBy() => $user?->id,
        ]);

        $this->conversationRepository->create([
            $this->conversationRepository->ticketId() => $ticket->id,
            $this->conversationRepository->message() => $payload['message'] ?? '',
            $this->conversationRepository->senderType() => 'customer',
            $this->conversationRepository->senderName() => $name,
            $this->conversationRepository->senderEmail() => $email,
            $this->conversationRepository->isInternal() => false,
            $this->conversationRepository->attachments() => json_encode($storedAttachments),
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

        /** @var \App\Models\Client $client */
        $client = $this->clientService->query()->where($this->clientService->id(), $clientId)->first();

        if (!$client) {
            throw new \Exception("Client not found", 404);
        }

        $email = $client->{$this->clientService->email()} ?? ($user->email ?? '');
        $name = $user ? ($user->{$this->userService->firstName()} .  $user->{$this->userService->lastName()}) : 'Client';

        $storedAttachments = $this->handleAttachments($attachments);

        $conversation = $this->conversationRepository->create([
            $this->conversationRepository->ticketId() => $ticket->id,
            $this->conversationRepository->message() => $message,
            $this->conversationRepository->senderType() => 'customer',
            $this->conversationRepository->senderName() => $name,
            $this->conversationRepository->senderEmail() => $email,
            $this->conversationRepository->isInternal() => false,
            $this->conversationRepository->attachments() => json_encode($storedAttachments),
        ]);

        return $conversation->toArray();
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
