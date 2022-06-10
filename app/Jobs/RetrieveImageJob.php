<?php

namespace App\Jobs;

use App\Models\Image;
use App\Services\RetrieveImageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RetrieveImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public Image $image)
    {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(RetrieveImageService $imageService): void
    {
        $imageService->retrieveAndStoreImage($this->image);
    }
}
