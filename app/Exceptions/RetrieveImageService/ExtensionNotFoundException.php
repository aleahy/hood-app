<?php

namespace App\Exceptions\RetrieveImageService;

class ExtensionNotFoundException extends ImageRetrievalFailedException
{
    public function __construct(string $filename)
    {
        parent::__construct('Cannot guess extension on retrieved file ' . $filename, 1001, null);
    }
}
