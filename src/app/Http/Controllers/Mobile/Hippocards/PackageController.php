<?php

namespace App\Http\Controllers\Mobile\Hippocards;

use App\Http\Controllers\Controller;
use App\Http\Resources\Mobile\Hippocards\WordSortResource;
use App\Http\Services\PackageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class PackageController extends Controller
{
    public function __construct(private PackageService $service)
    {
        $this->middleware("jwt.auth");
    }
}
