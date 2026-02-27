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
        return $this->model::DOCUMENT_ID;
    }

    public function priority()
    {
        return $this->model::PRIORITY;
    }

    public function status()
    {
        return $this->model::STATUS;
    }

    public function attempts()
    {
        return $this->model::ATTEMPTS;
    }

    public function maxAttempts()
    {
        return $this->model::MAX_ATTEMPTS;
    }

    public function ocrText()
    {
        return $this->model::OCR_TEXT;
    }

    public function errorMessage()
    {
        return $this->model::ERROR_MESSAGE;
    }

    public function processedAt()
    {
        return $this->model::PROCESSED_AT;
    }

    public function completedAt()
    {
        return $this->model::COMPLETED_AT;
    }
    // functions
}