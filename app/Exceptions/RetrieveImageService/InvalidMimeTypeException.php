<?php

namespace App\Exceptions\RetrieveImageService;

use Exception;

class InvalidMimeTypeException extends Exception
{
    public function __construct(string $filename)
    {
        parent::__construct('Retrieved file has an invalid mime type: ' . $filename, 1003, null);
    }
}
