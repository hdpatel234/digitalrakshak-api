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
        return EmailAttachment::EMAIL_QUEUE_ID;
    }

    public function documentId()
    {
        return EmailAttachment::DOCUMENT_ID;
    }

    public function filename()
    {
        return EmailAttachment::FILENAME;
    }

    public function filePath()
    {
        return EmailAttachment::FILE_PATH;
    }

    public function fileSize()
    {
        return EmailAttachment::FILE_SIZE;
    }

    public function mimeType()
    {
        return EmailAttachment::MIME_TYPE;
    }

    public function cid()
    {
        return EmailAttachment::CID;
    }

    public function isInline()
    {
        return EmailAttachment::IS_INLINE;
    }
    // functions
}