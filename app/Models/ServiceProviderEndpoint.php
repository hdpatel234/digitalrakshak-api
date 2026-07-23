<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceProviderEndpoint extends BaseModel
{
    use SoftDeletes;
    protected $table = 'service_provider_endpoints';
    const CONFIG_ID = 'config_id';
    const API_NAME = 'api_name';
    const API_CODE = 'api_code';
    const ENDPOINT_PATH = 'endpoint_path';
    const HTTP_METHOD = 'http_method';
    const CONTENT_TYPE = 'content_type';
    const CUSTOM_HEADERS = 'custom_headers';
    const REQUEST_SCHEMA = 'request_schema';
    const RESPONSE_SCHEMA = 'response_schema';
    const STATUS = 'status';

    protected $fillable = [
        self::CONFIG_ID,
        self::API_NAME,
        self::API_CODE,
        self::ENDPOINT_PATH,
        self::HTTP_METHOD,
        self::CONTENT_TYPE,
        self::CUSTOM_HEADERS,
        self::REQUEST_SCHEMA,
        self::RESPONSE_SCHEMA,
        self::STATUS,
    ];

    protected $casts = [
        self::CUSTOM_HEADERS => 'array',
        self::REQUEST_SCHEMA => 'array',
        self::RESPONSE_SCHEMA => 'array',
    ];

    public function config()
    {
        return $this->belongsTo(ServiceProviderConfig::class, self::CONFIG_ID, 'id');
    }
}
