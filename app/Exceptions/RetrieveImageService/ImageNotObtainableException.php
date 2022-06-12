<?php

namespace App\Exceptions\RetrieveImageService;

class ImageNotObtainableException extends ImageRetrievalFailedException
{
    public function __construct(string $uri)
    {
        parent::__construct('Could not reach image at ' . $uri, 1002, null);
    }
}
