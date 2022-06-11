<?php

namespace App\Exceptions\RetrieveImageService;

use Exception;

class ImageNotObtainableException extends Exception
{
    public function __construct(string $uri)
    {
        parent::__construct('Could not reach image at ' . $uri, 1002, null);
    }
}
