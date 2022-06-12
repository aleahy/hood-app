<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class SelfController extends Controller
{
    public function __invoke(Request $request)
    {
        return UserResource::make($request->user());
    }
}
