<?php

namespace App\Http\Controllers\System\Utility;

use App\Http\Controllers\Controller;
use App\Http\Resources\Utility\LanguageResource;
use App\Http\Services\LanguageService;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function __construct(private LanguageService $service)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $languages = $this->service->getLanguages();

        return LanguageResource::collection($languages);
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
