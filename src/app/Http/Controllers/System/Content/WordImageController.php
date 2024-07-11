<?php

namespace App\Http\Controllers\System\Content;

use App\Enums\EPermissionScope;
use App\Enums\EWordImageType;
use App\Http\Controllers\Controller;
use App\Http\Services\WordSortService;
use App\Models\Package\Word\Word;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class WordImageController extends Controller
{
    public function __construct(private WordSortService $service)
    {
        $this->middleware("jwt.auth");
        $this->middleware(get_role_middleware(EPermissionScope::READ_WORD))->only("index");
        $this->middleware(get_role_middleware(EPermissionScope::UPDATE_WORD))->only("store");
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
    public function store(Request $request, Word $word)
    {
        $validatedData = Validator::make(
            $request->only(
                "type",
                "v3_asset_id"
            ),
            [
                "type" => [
                    "required",
                    Rule::in([ EWordImageType::PRIMARY->value, EWordImageType::IMAGINATION->value, EWordImageType::DEFINITION->value ])
                ],
                "v3_asset_id" => "required|exists:v3_assets,id"
            ]
        )
            ->validate();

        $this->service->createWordImage($word, $validatedData);

        return response()->success();
    }
}
