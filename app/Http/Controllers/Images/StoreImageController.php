<?php

namespace App\Http\Controllers\Images;

use App\Http\Controllers\Controller;
use App\Http\Requests\Images\StoreImageRequest;
use App\Http\Resources\ImageResource;
use App\Jobs\RetrieveImageJob;
use App\Models\Image;
use Illuminate\Http\Request;

class StoreImageController extends Controller
{
    public function __invoke(StoreImageRequest $request)
    {
        $image = Image::create([
            'user_id' => auth()->user()->id,
            'uri' => $request->safe()['image_uri'],
        ]);

        RetrieveImageJob::dispatch($image);

        return ImageResource::make($image);
    }
}
