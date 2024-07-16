<?php

namespace App\Http\Controllers\System\Content;

use App\Enums\EPackageType;
use App\Enums\EPermissionScope;
use App\Enums\EStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\System\Content\PackageResource;
use App\Http\Resources\System\Content\WordSortResource;
use App\Http\Services\PackageService;
use App\Http\Services\WordSortService;
use App\Models\Package\Baseklass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PackageController extends Controller
{
    public function __construct(private PackageService $service, private WordSortService $wordSortService)
    {
        $this->middleware("jwt.auth");
        $this->middleware(get_role_middleware(EPermissionScope::READ_PACKAGE))->only("index", "show");
        $this->middleware(get_role_middleware(EPermissionScope::CREATE_PACKAGE))->only("store");
        $this->middleware(get_role_middleware(EPermissionScope::UPDATE_PACKAGE))->only("update");
        $this->middleware(get_role_middleware(EPermissionScope::DELETE_PACKAGE))->only("delete");
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->only("filter", "name_like", "language", "status", "type");

        $packages = $this->service->getPackagesWithPage($filters, [ "category" ]);

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

        $packages = $this->service->getPackagesWithPage($filters);

        return PackageResource::collection($packages);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = Validator::make(
            $request->only(
                "name",
                "description",
                "v3_thumbnail_asset_id",
                "main_category_id",
                "for_kids",
                "type",
                "foreign_name",
                "type",
                "language_id"
            ),
            [
                "name" => "required|string|max:128",
                "language_id" => "required|integer",
                "description" => "required|string|max:256",
                "for_kids" => "required|boolean",
                "main_category_id" => "required|integer",
                "type" => [
                    "required",
                    Rule::in(EPackageType::ARTICLE->value, EPackageType::DEFAULT->value, EPackageType::BOOK->value)
                ],
                "foreign_name" => "sometimes|string|max:128",
                "v3_thumbnail_asset_id" => "sometimes|integer",
            ]
        )
            ->validate();

        $requestUser = auth()->user();

        $package = $this->service->createPackage($requestUser, $validatedData);

        return new PackageResource($package);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $package = $this->service->getPackageById($id);

        if (is_null($package)) {
            throw new NotFoundHttpException("Package is not found!");
        }

        return new PackageResource($package);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Baseklass $package)
    {
        $validatedData = Validator::make(
            $request->only(
                "name",
                "description",
                "v3_thumbnail_asset_id",
                "main_category_id",
                "for_kids",
                "type",
                "foreign_name",
                "type",
                "language_id",
                "status",
            ),
            [
                "name" => "sometimes|string|max:128",
                "language_id" => "sometimes|integer",
                "description" => "sometimes|string|max:256",
                "for_kids" => "sometimes|boolean",
                "main_category_id" => "sometimes|integer",
                "type" => [
                    "sometimes",
                    Rule::in(EPackageType::ARTICLE->value, EPackageType::DEFAULT->value, EPackageType::BOOK->value)
                ],
                "foreign_name" => "sometimes|string|max:128",
                "v3_thumbnail_asset_id" => "sometimes|integer",
                "status" => [
                    "sometimes",
                    Rule::in(EStatus::PENDING->value, EStatus::PACKAGE_PUBLISHED->value)
                ],
            ]
        )
            ->validate();

        $requestUser = auth()->user();

        $this->service->updatePackage($requestUser, $package, $validatedData);

        return new PackageResource($package);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Baseklass $package)
    {
        $requestUser = auth()->user();
        $this->service->deletePackage($requestUser, $package);

        return response()->success();
    }

    /**
     * Get package sorts
     */
    public function getPackageSorts(Request $request, Baseklass $package)
    {
        $filters = $request->only("search", "language", "word");

        $sorts = $this->wordSortService->getPackageSorts($package, $filters, [ "word", "word.mainDetail", "word.images", "word.synonyms", "word.definitionSentences", "word.imaginationSentences" ]);

        return WordSortResource::collection($sorts);
    }
}
