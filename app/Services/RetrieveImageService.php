<?php

namespace App\Services;

use App\Events\ImageRetrievalFailedEvent;
use App\Events\ImageRetrievedEvent;
use App\Exceptions\RetrieveImageService\ExtensionNotFoundException;
use App\Exceptions\RetrieveImageService\ImageNotObtainableException;
use App\Exceptions\RetrieveImageService\ImageRetrievalFailedException;
use App\Exceptions\RetrieveImageService\InvalidMimeTypeException;
use App\Exceptions\RetrieveImageService\TempFileFailureException;
use App\Models\Image;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RetrieveImageService
{

    /**
     * Retrieves and stores a message
     * Updates image model with url to the saved image.
     *
     * Dispatches an ImageRetrievedEvent on success
     * Dispatches an ImageRetrievalFailedEvent on failure
     *
     * @param Image $image
     * @return void
     */
    public function retrieveAndStoreImage(Image $image)
    {
        try {
            $tempFile = $this->getFileFromURI($image);

            $this->validateMimeTypes($tempFile);

            $uniqueFilename = $this->createUniqueFilename($tempFile);

            Storage::disk('images')->put($uniqueFilename, file_get_contents($tempFile));

            $image->update([
                'filename' => $uniqueFilename,
            ]);

            ImageRetrievedEvent::dispatch($image);
        }
        catch(ImageRetrievalFailedException $exception) {
            Log::error($exception->getMessage());

            ImageRetrievalFailedEvent::dispatch($image);
        }
    }


    /**
     * Create the file from the image URI
     *
     * @param Image $image
     * @return string
     * @throws ImageNotObtainableException
     * @throws TempFileFailureException
     */
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
     * Validate the retrieved file against expected mime types
     *
     * @param string $tempFile
     * @return void
     * @throws InvalidMimeTypeException
     */
    protected function validateMimeTypes(string $tempFile): void
    {
        $mimeTypes = self::getMimeTypeCollection()
            ->join(',');
        $validator = Validator::make(
            ['file' => new File($tempFile)],
            ['file' => 'mimes:' . $mimeTypes]
        );
        if ($validator->fails()) {
            throw new InvalidMimeTypeException($tempFile);
        }
    }

    /**
     * Create a new storage filename with extension
     *
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

    /**
     * Get a collection of allowable mime type extensions from config file
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getMimeTypeCollection()
    {
        return collect(config('imageretrieval.accepted_mime_extensions', 'png'));
    }
}
