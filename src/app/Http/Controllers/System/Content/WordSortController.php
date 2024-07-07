<?php

namespace App\Http\Controllers\System\Content;

use App\Enums\EPermissionScope;
use App\Http\Controllers\Controller;
use App\Http\Resources\System\Content\WordSortResource;
use App\Http\Services\PackageService;
use App\Http\Services\WordSortService;
use Illuminate\Http\Request;

class WordSortController extends Controller
{
    public function __construct(private WordSortService $service)
    {
        $this->middleware("jwt.auth");
        $this->middleware(get_role_middleware(EPermissionScope::READ_WORD))->only("index", "show");
        $this->middleware(get_role_middleware(EPermissionScope::CREATE_WORD))->only("store");
        $this->middleware(get_role_middleware(EPermissionScope::UPDATE_WORD))->only("update");
        $this->middleware(get_role_middleware(EPermissionScope::DELETE_WORD))->only("delete");
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->only("search", "package", "language", "word");

        $words = $this->service->getSortsWithPage($filters, ["word"]);

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
