<?php

namespace App\Services\EmailServerTesting;

use App\Models\EmailServer;

interface EmailServerTesterInterface
{
    /**
     * Test the connection to the email server.
     *
     * @param EmailServer $server
     * @return array ['status' => 'success'|'error', 'logs' => array]
     */
    public function test(EmailServer $server): array;
}
