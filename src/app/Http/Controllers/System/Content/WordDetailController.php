<?php

namespace App\Http\Controllers\System\Content;

use App\Enums\EPermissionScope;
use App\Http\Controllers\Controller;
use App\Http\Services\WordSortService;
use App\Models\Package\Word\Word;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WordDetailController extends Controller
{
    public function __construct(private WordSortService $service)
    {
        $this->middleware("jwt.auth");
        $this->middleware(get_role_middleware(EPermissionScope::READ_WORD))->only("index", "show");
        $this->middleware(get_role_middleware([ EPermissionScope::CREATE_WORD, EPermissionScope::UPDATE_WORD ]))->only("store");
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
                "translation",
                "pronunciation",
                "keyword",
                "hiragana",
                "part_of_speech"
            ),
            [
                "translation" => "nullable|string",
                "pronunciation" => "nullable|string",
                "keyword" => "nullable|string",
                "hiragana" => "nullable|string",
                "part_of_speech" => "nullable|string"
            ]
        )
            ->validate();

        $this->service->createUpdateWordDetail($word, $validatedData);

        return response()->success();
    }
}
