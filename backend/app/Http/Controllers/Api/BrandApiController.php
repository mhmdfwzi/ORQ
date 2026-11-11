<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\BrandStory;
use Illuminate\Http\JsonResponse;

/**
 * Public brand content for the storefront ("شفرة ORQ" story).
 */
class BrandApiController extends Controller
{
    public function show(): JsonResponse
    {
        return response()->json(['data' => BrandStory::get()]);
    }
}

