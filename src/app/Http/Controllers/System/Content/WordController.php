<?php

namespace App\Http\Controllers\System\Content;

use App\Http\Controllers\Controller;
use App\Http\Resources\System\Academy\WordSortResource;
use App\Http\Services\PackageService;
use Illuminate\Http\Request;

class WordController extends Controller
{
    public function __construct(private PackageService $service)
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->only("search", "package", "language", "word");

        $words = $this->service->getSortsWithSimplePage($filters, ["word"]);

        return WordSortResource::collection($words);
    }

    /**
     * Utility index
     */
    public function search(Request $request)
    {
        $filters = $request->only("search", "package", "language", "word");

        if (count($filters) === 0) {
            return response()->success([]);
        }

        $words = $this->service->getSortsWithSimplePage($filters, ["word"]);

        return WordSortResource::collection($words);
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
