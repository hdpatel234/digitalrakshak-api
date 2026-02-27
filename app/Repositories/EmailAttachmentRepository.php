<?php

namespace App\Repositories;

use App\Models\EmailAttachment;

class EmailAttachmentRepository extends BaseRepository
{
    public function __construct(EmailAttachment $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function emailQueueId()
    {
        return $this->model::EMAIL_QUEUE_ID;
    }

    public function documentId()
    {
        return $this->model::DOCUMENT_ID;
    }

    public function filename()
    {
        return $this->model::FILENAME;
    }

    public function filePath()
    {
        return $this->model::FILE_PATH;
    }

    public function fileSize()
    {
        return $this->model::FILE_SIZE;
    }

    public function mimeType()
    {
        return $this->model::MIME_TYPE;
    }

    public function cid()
    {
        return $this->model::CID;
    }

    public function isInline()
    {
        return $this->model::IS_INLINE;
    }
    // functions
}