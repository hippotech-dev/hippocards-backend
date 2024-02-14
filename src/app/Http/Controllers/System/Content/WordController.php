<?php

namespace App\Http\Controllers\System\Content;

use App\Http\Controllers\Controller;
use App\Http\Resources\System\Academy\PackageResource;
use App\Http\Resources\System\Academy\WordSortResource;
use App\Http\Services\WordService;
use Illuminate\Http\Request;

class WordController extends Controller
{
    public function __construct(private WordService $service) {}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Utility index
     */
    public function search(Request $request)
    {
        $filters = $request->only("package", "language", "word");

        if (count($filters) === 0) {
            return response()->success([]);
        }

        $packages = $this->service->searchSorts($filters, ["word"]);

        return WordSortResource::collection($packages);
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
