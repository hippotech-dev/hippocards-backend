<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Http\Services\SentenceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SentenceController extends Controller
{
    public function __construct(private SentenceService $service)
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
        $validatedData = Validator::make(
            $request->only(
                "sentences",
                "object_id",
                "object_type"
            ),
            [
                "object_id" => "required|integer",
                "object_type" => "required|string",
                "sentences" => "required|array",
                "sentences.*.id" => "sometimes|integer",
                "sentences.*.is_deleted" => "sometimes|boolean",
                "sentences.*.type" => "required|integer",
                "sentences.*.language_id" => "required|integer",
                "sentences.*.value" => "required|string",
                "sentences.*.order" => "sometimes|integer",
                "sentences.*.translation" => "nullable|string",
                "sentences.*.latin" => "nullable|string",
            ]
        )
            ->validate();

        $object = get_class_map_object($validatedData["object_id"], $validatedData["object_type"]);

        if (is_null($object)) {
            throw new NotFoundHttpException("Object is not found!");
        }

        $this->service->modifyObjectSentences($object, $validatedData["sentences"]);

        return response()->success();
    }
}
