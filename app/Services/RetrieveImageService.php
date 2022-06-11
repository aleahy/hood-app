<?php

namespace App\Services;

use App\Events\ImageRetrievedEvent;
use App\Exceptions\RetrieveImageService\ExtensionNotFoundException;
use App\Exceptions\RetrieveImageService\ImageNotObtainableException;
use App\Exceptions\RetrieveImageService\InvalidMimeTypeException;
use App\Exceptions\RetrieveImageService\TempFileFailureException;
use App\Models\Image;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RetrieveImageService
{
    public function retrieveAndStoreImage(Image $image)
    {
            $tempFile = $this->getFileFromURI($image);

            $this->validateMimeTypes($tempFile);

            $uniqueFilename = $this->createUniqueFilename($tempFile);

            Storage::disk('images')->put($uniqueFilename, file_get_contents($tempFile));

            $image->update([
                'filename' => $uniqueFilename,
            ]);

            ImageRetrievedEvent::dispatch($image);
    }


    protected function getFileFromURI(Image $image)
    {
        $stream = @fopen($image->uri, 'r');
        if (! $stream) {
            throw new ImageNotObtainableException($image->uri);
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'img_');
        if (! $tempFile) {
            throw new TempFileFailureException($image->uri);
        }

        file_put_contents($tempFile, $stream);

        return $tempFile;
    }

    /**
     * @param string $tempFile
     * @return void
     * @throws InvalidMimeTypeException
     */
    protected function validateMimeTypes(string $tempFile): void
    {
        $validator = Validator::make(
            ['file' => new File($tempFile)],
            ['file' => 'mimes:jpg,jpeg,png,bmp,gif,svg,webp']
        );
        if ($validator->fails()) {
            throw new InvalidMimeTypeException($tempFile);
        }
    }

    /**
     * @param string $tempFile
     * @return string
     * @throws ExtensionNotFoundException
     */
    protected function createUniqueFilename(string $tempFile): string
    {
        $file = new File($tempFile);
        $extension = $file->guessExtension();
        if (! $extension) {
            throw new ExtensionNotFoundException($tempFile);
        }
        return Str::uuid() . '.' . $extension;
    }
}
