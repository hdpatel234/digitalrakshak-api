<?php

namespace App\Services\Email\Contracts;

use App\Models\EmailServer;

interface EmailDriverInterface
{
    /**
     * Send an email.
     *
     * @param array $data
     * @return array
     */
    public function send(array $data): array;

    /**
     * Receive emails (if supported).
     *
     * @return array
     */
    public function receive(): array;

    /**
     * Test the connection to the email server.
     *
     * @return array
     */
    public function testConnection(): array;
}
