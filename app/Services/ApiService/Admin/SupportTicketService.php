<?php

namespace App\Services\ApiService\Admin;

use App\Repositories\SupportDepartmentRepository;
use App\Repositories\SupportPriorityRepository;
use App\Repositories\SupportTicketRepository;
use App\Services\BaseService;
use App\Services\UserService;
use App\Models\SupportTicketConversation;

/**
 * @property SupportTicketRepository $repository
 */
class SupportTicketService extends BaseService
{
    protected SupportDepartmentRepository $departmentRepository;
    protected SupportPriorityRepository $priorityRepository;
    protected UserService $userService;

    public function __construct(
        SupportTicketRepository $repository,
        SupportDepartmentRepository $departmentRepository,
        SupportPriorityRepository $priorityRepository,
        UserService $userService
    ) {
        $this->departmentRepository = $departmentRepository;
        $this->priorityRepository = $priorityRepository;
        $this->userService = $userService;
        parent::__construct($repository);
    }

    public function getTickets(array $params): array
    {
        $ticketTable = $this->repository->query()->getModel()->getTable();
        $statusColumn = $this->repository->status();
        $ticketNumberColumn = $this->repository->ticketNumber();
        $subjectColumn = $this->repository->subject();
        $createdAtColumn = $this->repository->query()->getModel()->getCreatedAtColumn();

        $qualifiedStatusColumn = $ticketTable . '.' . $statusColumn;

        $query = $this->query()
            ->with(['order:id,order_number', 'department:id,name', 'priority:id,name', 'client:id,company_name']);

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
                        })->orWhereHas('client', function ($q) use ($search) {
                            $q->where('company_name', 'like', '%' . $search . '%');
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
                $item['client_name'] = $item['client']['company_name'] ?? null;
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

        $lists = [
            'status_list' => $statusList,
            'departments' => $departments,
            'priorities' => $priorities,
        ];

        if (is_array($result)) {
            $result = array_merge($result, $lists);
        } else {
            $result = array_merge(['list' => $result], $lists);
        }

        return $result;
    }

    public function getTicket(int $id): array
    {
        $ticket = $this->query()
            ->with(['order:id,order_number', 'department:id,name', 'priority:id,name', 'client:id,company_name'])
            ->where($this->repository->id(), $id)
            ->first();

        if (!$ticket) {
            throw new \Exception("Ticket not found", 404);
        }

        $result = $ticket->toArray();
        $result['order_number'] = $ticket->order->order_number ?? null;
        $result['department_name'] = $ticket->department->name ?? null;
        $result['priority_name'] = $ticket->priority->name ?? null;
        $result['client_name'] = $ticket->client->company_name ?? null;
        $result['created_at_human'] = \App\Models\BaseModel::formatTimeAgo($ticket->created_at);

        return $result;
    }

    public function getTicketConversations(int $id): array
    {
        $ticket = $this->query()
            ->where($this->repository->id(), $id)
            ->first();

        if (!$ticket) {
            throw new \Exception("Ticket not found", 404);
        }

        $threads = [];

        $conversations = SupportTicketConversation::where('ticket_id', $ticket->id)
            ->orderBy('created_at', 'asc')
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
                'sender_type' => $conversation->sender_type ?? 'agent',
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

    public function addTicketReply(int $ticketId, string $message, ?object $user, array $attachments = []): array
    {
        $ticket = $this->query()
            ->where($this->repository->id(), $ticketId)
            ->first();

        if (!$ticket) {
            throw new \Exception("Ticket not found", 404);
        }

        $email = $user->email ?? 'admin@example.com';
        $name = $user ? ($user->{$this->userService->firstName()} . ' ' . $user->{$this->userService->lastName()}) : 'Admin Support';

        $storedAttachments = $this->handleAttachments($attachments);

        $conversation = SupportTicketConversation::create([
            'ticket_id' => $ticket->id,
            'message' => $message,
            'sender_type' => 'agent',
            'sender_name' => trim($name) ?: 'Admin Support',
            'sender_email' => $email,
            'is_internal' => false,
            'attachments' => json_encode($storedAttachments),
        ]);

        return $conversation->toArray();
    }
}
