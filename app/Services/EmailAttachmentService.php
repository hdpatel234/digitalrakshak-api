<?php

namespace App\Services;

use App\Repositories\EmailAttachmentRepository;

class EmailAttachmentService extends BaseService
{
    
    public function __construct(EmailAttachmentRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function emailQueueId()
    {
        return $this->repository->emailQueueId();
    }

    public function documentId()
    {
        return $this->repository->documentId();
    }

    public function filename()
    {
        return $this->repository->filename();
    }

    public function filePath()
    {
        return $this->repository->filePath();
    }

    public function fileSize()
    {
        return $this->repository->fileSize();
    }

    public function mimeType()
    {
        return $this->repository->mimeType();
    }

    public function cid()
    {
        return $this->repository->cid();
    }

    public function isInline()
    {
        return $this->repository->isInline();
    }
    // functions
}