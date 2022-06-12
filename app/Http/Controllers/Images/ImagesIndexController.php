<?php

namespace App\Http\Controllers\Images;

use App\Http\Controllers\Controller;
use App\Http\Resources\ImageResource;
use App\Models\Image;
use Illuminate\Http\Request;

class ImagesIndexController extends Controller
{
    public function __invoke()
    {
        $images = Image::OwnedBy(auth()->user())
                    ->paginate(12);
        return ImageResource::collection($images);
    }
}
