<?php

namespace App\Http\Controllers\System\Content;

use App\Exceptions\AppException;
use App\Http\Controllers\Controller;
use App\Http\Resources\System\Academy\PackageResource;
use App\Http\Services\PackageService;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function __construct(private PackageService $service)
    {
        $this->middleware("jwt.auth");
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->only("name_like", "language_id", "status");

        if (count($filters) === 0) {
            return response()->success([]);
        }

        $packages = $this->service->getPackages($filters);

        return PackageResource::collection($packages);
    }

    /**
     * Utility index
     */
    public function search(Request $request)
    {
        $filters = $request->only("name_like", "language_id");

        if (count($filters) === 0) {
            return response()->success([]);
        }

        $packages = $this->service->getPackages($filters);

        return PackageResource::collection($packages);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
