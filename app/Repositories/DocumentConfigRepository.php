<?php

namespace App\Repositories;

use App\Models\DocumentConfig;

class DocumentConfigRepository extends BaseRepository
{
    public function __construct(DocumentConfig $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function documentPlatformId()
    {
        return DocumentConfig::DOCUMENT_PLATFORM_ID;
    }

    public function configName()
    {
        return DocumentConfig::CONFIG_NAME;
    }

    public function isDefault()
    {
        return DocumentConfig::IS_DEFAULT;
    }

    public function apiUrl()
    {
        return DocumentConfig::API_URL;
    }

    public function username()
    {
        return DocumentConfig::USERNAME;
    }

    public function password()
    {
        return DocumentConfig::PASSWORD;
    }

    public function apiKey()
    {
        return DocumentConfig::API_KEY;
    }

    public function apiSecret()
    {
        return DocumentConfig::API_SECRET;
    }

    public function accessToken()
    {
        return DocumentConfig::ACCESS_TOKEN;
    }

    public function refreshToken()
    {
        return DocumentConfig::REFRESH_TOKEN;
    }

    public function tokenExpiresAt()
    {
        return DocumentConfig::TOKEN_EXPIRES_AT;
    }

    public function rootFolder()
    {
        return DocumentConfig::ROOT_FOLDER;
    }

    public function clientFolder()
    {
        return DocumentConfig::CLIENT_FOLDER;
    }

    public function folderStructure()
    {
        return DocumentConfig::FOLDER_STRUCTURE;
    }

    public function fileNamingConvention()
    {
        return DocumentConfig::FILE_NAMING_CONVENTION;
    }

    public function maxFileSize()
    {
        return DocumentConfig::MAX_FILE_SIZE;
    }

    public function allowedFileTypes()
    {
        return DocumentConfig::ALLOWED_FILE_TYPES;
    }

    public function isPublicReadable()
    {
        return DocumentConfig::IS_PUBLIC_READABLE;
    }

    public function shareExpiryDays()
    {
        return DocumentConfig::SHARE_EXPIRY_DAYS;
    }

    public function webhookSecret()
    {
        return DocumentConfig::WEBHOOK_SECRET;
    }

    public function additionalConfig()
    {
        return DocumentConfig::ADDITIONAL_CONFIG;
    }

    public function status()
    {
        return DocumentConfig::STATUS;
    }

    public function createdBy()
    {
        return DocumentConfig::CREATED_BY;
    }

    public function updatedBy()
    {
        return DocumentConfig::UPDATED_BY;
    }
    // functions
}