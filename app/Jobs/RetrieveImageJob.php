<?php

namespace App\Jobs;

use App\Events\ImageRetrievedEvent;
use App\Models\Image;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RetrieveImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(private Image $image)
    {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $contents = $this->getFileFromURI();

        $originalFilename = $this->getOriginalFilename();
        $uniqueFilename = $this->createUniqueFilename();

        Storage::disk('images')->put($uniqueFilename, $contents);

        $this->image->update([
            'filename' => $originalFilename,
            'path' => Storage::disk('images')->path($uniqueFilename)
        ]);

        ImageRetrievedEvent::dispatch($this->image);
    }

    protected function getFileFromURI()
    {
        return file_get_contents($this->image->uri);
    }

    protected function createUniqueFilename()
    {
        return Str::uuid() . '.' . pathinfo($this->image->uri, PATHINFO_EXTENSION);
    }

    protected function getOriginalFilename()
    {
        return pathinfo($this->image->uri, PATHINFO_BASENAME);
    }
}
