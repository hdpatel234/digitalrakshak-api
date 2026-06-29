<?php

namespace App\Repositories;

use App\Models\DocumentOcrQueue;

class DocumentOcrQueueRepository extends BaseRepository
{
    public function __construct(DocumentOcrQueue $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function documentId()
    {
        return DocumentOcrQueue::DOCUMENT_ID;
    }

    public function priority()
    {
        return DocumentOcrQueue::PRIORITY;
    }

    public function status()
    {
        return DocumentOcrQueue::STATUS;
    }

    public function attempts()
    {
        return DocumentOcrQueue::ATTEMPTS;
    }

    public function maxAttempts()
    {
        return DocumentOcrQueue::MAX_ATTEMPTS;
    }

    public function ocrText()
    {
        return DocumentOcrQueue::OCR_TEXT;
    }

    public function errorMessage()
    {
        return DocumentOcrQueue::ERROR_MESSAGE;
    }

    public function processedAt()
    {
        return DocumentOcrQueue::PROCESSED_AT;
    }

    public function completedAt()
    {
        return DocumentOcrQueue::COMPLETED_AT;
    }
    // functions
}