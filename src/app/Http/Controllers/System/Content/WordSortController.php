<?php

namespace App\Http\Controllers\System\Content;

use App\Enums\EPermissionScope;
use App\Http\Controllers\Controller;
use App\Http\Resources\System\Content\WordSortResource;
use App\Http\Services\PackageService;
use App\Http\Services\WordSortService;
use App\Models\Package\Sort;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class WordSortController extends Controller
{
    public function __construct(private WordSortService $service, private PackageService $packageService)
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

        $validatedData = Validator::make(
            $request->only([
                "word",
                "package_id",
                "sort_word"
            ]),
            [
                "word" => "required|string",
                "package_id" => "required|exists:baseklass,id",
                "sort_word" => "required|integer",
            ]
        )
            ->validate();

        $requstUser = auth()->user();
        $package = $this->packageService->getPackageById($validatedData["package_id"]);

        if (is_null($package)) {
            throw new NotFoundHttpException("Package not found!");
        }

        $this->service->createSort($requstUser, $package, $validatedData);

        return response()->success();
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $sort = $this->service->getSortByIdLoaded($id);

        if (is_null($sort)) {
            throw new NotFoundHttpException("Sort does not exist!");
        }

        return new WordSortResource($sort);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sort $sort)
    {
        $validatedData = Validator::make(
            $request->only([
                "sort_word"
            ]),
            [
                "sort_word" => "required|integer",
            ]
        )
            ->validate();

        $requstUser = auth()->user();

        $this->service->updateSort($requstUser, $sort, $validatedData);

        return response()->success();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sort $sort)
    {
        $requestUser = auth()->user();
        $this->service->deleteSort($requestUser, $sort);

        return response()->success();
    }
}
