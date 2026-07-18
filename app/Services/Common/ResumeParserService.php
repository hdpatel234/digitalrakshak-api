<?php

namespace App\Services\Common;

class ResumeParserService
{
    public function __construct()
    {
        //
    }

    /**
     * Parse a resume file.
     *
     * @param string $tempPath
     * @param string $extension
     * @param string $originalName
     * @param string $promptCode
     * @return array
     */
    public function parseResumeFile(string $tempPath, string $extension, string $originalName, string $promptCode): array
    {
        return [
            'success' => true,
            'data' => []
        ];
    }
}
