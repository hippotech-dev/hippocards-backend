<?php

namespace App\Http\Controllers\Mobile\Hippocards;

use App\Http\Controllers\Controller;
use App\Http\Resources\Mobile\Hippocards\WordResource;
use App\Http\Services\PackageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class WordController extends Controller
{
    public function __construct(private PackageService $service)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        $sort = Cache::remember(
            cache_key("get-word-overview", [ $id, 123 ]),
            3600,
            function () use ($id) {
                $sort = $this->service->getSortByIdLoaded($id);

                if (is_null($sort)) {
                    throw new NotFoundHttpException("Sort not found!");
                }

                return (new WordResource($sort->word))->toArray(request());
            }
        );

        return $sort;
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
