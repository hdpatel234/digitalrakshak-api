<?php

namespace App\Repositories;

use App\Models\ClientDocumentConfig;

class ClientDocumentConfigRepository extends BaseRepository
{
    public function __construct(ClientDocumentConfig $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function clientId()
    {
        return $this->model::CLIENT_ID;
    }

    public function documentPlatformId()
    {
        return $this->model::DOCUMENT_PLATFORM_ID;
    }

    public function configName()
    {
        return $this->model::CONFIG_NAME;
    }

    public function isDefault()
    {
        return $this->model::IS_DEFAULT;
    }

    public function apiUrl()
    {
        return $this->model::API_URL;
    }

    public function username()
    {
        return $this->model::USERNAME;
    }

    public function password()
    {
        return $this->model::PASSWORD;
    }

    public function apiKey()
    {
        return $this->model::API_KEY;
    }

    public function apiSecret()
    {
        return $this->model::API_SECRET;
    }

    public function accessToken()
    {
        return $this->model::ACCESS_TOKEN;
    }

    public function refreshToken()
    {
        return $this->model::REFRESH_TOKEN;
    }

    public function tokenExpiresAt()
    {
        return $this->model::TOKEN_EXPIRES_AT;
    }

    public function rootFolder()
    {
        return $this->model::ROOT_FOLDER;
    }

    public function clientFolder()
    {
        return $this->model::CLIENT_FOLDER;
    }

    public function folderStructure()
    {
        return $this->model::FOLDER_STRUCTURE;
    }

    public function fileNamingConvention()
    {
        return $this->model::FILE_NAMING_CONVENTION;
    }

    public function maxFileSize()
    {
        return $this->model::MAX_FILE_SIZE;
    }

    public function allowedFileTypes()
    {
        return $this->model::ALLOWED_FILE_TYPES;
    }

    public function isPublicReadable()
    {
        return $this->model::IS_PUBLIC_READABLE;
    }

    public function shareExpiryDays()
    {
        return $this->model::SHARE_EXPIRY_DAYS;
    }

    public function webhookSecret()
    {
        return $this->model::WEBHOOK_SECRET;
    }

    public function additionalConfig()
    {
        return $this->model::ADDITIONAL_CONFIG;
    }

    public function status()
    {
        return $this->model::STATUS;
    }

    public function createdBy()
    {
        return $this->model::CREATED_BY;
    }

    public function updatedBy()
    {
        return $this->model::UPDATED_BY;
    }
    // functions
}