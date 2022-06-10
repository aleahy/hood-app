<?php

namespace App\Services;

use App\Events\ImageRetrievedEvent;
use App\Models\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RetrieveImageService
{
    public function retrieveAndStoreImage(Image $image)
    {
        $contents = $this->getFileFromURI($image);

        $uniqueFilename = $this->createUniqueFilename($image);

        Storage::disk('images')->put($uniqueFilename, $contents);

        $originalFilename = $this->getOriginalFilename($image);

        $image->update([
            'filename' => $originalFilename,
            'path' => Storage::disk('images')->path($uniqueFilename)
        ]);

        ImageRetrievedEvent::dispatch($image);
    }

    protected function getFileFromURI(Image $image)
    {
        return file_get_contents($image->uri);
    }

    protected function createUniqueFilename(Image $image)
    {
        return Str::uuid() . '.' . pathinfo($image->uri, PATHINFO_EXTENSION);
    }

    protected function getOriginalFilename(Image $image)
    {
        return pathinfo($image->uri, PATHINFO_BASENAME);
    }
}
