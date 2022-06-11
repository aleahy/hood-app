<?php

namespace App\Exceptions\RetrieveImageService;

use Exception;

class TempFileFailureException extends Exception
{
    public function __construct(string $uri)
    {
        parent::__construct('Could not create a temporary file for image at ' . $uri, 1004, null);
    }
}
