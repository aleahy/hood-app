<?php

namespace App\Exceptions\RetrieveImageService;

class InvalidMimeTypeException extends ImageRetrievalFailedException
{
    public function __construct(string $filename)
    {
        parent::__construct('Retrieved file has an invalid mime type: ' . $filename, 1003, null);
    }
}
