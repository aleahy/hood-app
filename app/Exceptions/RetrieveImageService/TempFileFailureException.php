<?php

namespace App\Exceptions\RetrieveImageService;

class TempFileFailureException extends ImageRetrievalFailedException
{
    public function __construct(string $uri)
    {
        parent::__construct('Could not create a temporary file for image at ' . $uri, 1004, null);
    }
}
