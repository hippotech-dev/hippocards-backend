<?php

namespace App\Http\Controllers\Utilities;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

const VALIDATIONS = [
    "upload" => [
        "file" => "required|file|max:131072"
    ],
];

class UploadController extends Controller
{
    /**
     * Upload file
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        $validatedData = Validator::make(
            $request->only(
                "file",
            ),
            VALIDATIONS["upload"]
        )
            ->validate();

        return response()->success();
    }
}
