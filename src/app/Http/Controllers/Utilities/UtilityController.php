<?php

namespace App\Http\Controllers\Utilities;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UtilityController extends Controller
{
    /**
     * Get code version
     */
    public function getCodeVersion()
    {
        return response()->successAppend([
            "version" => 1
        ]);
    }
}
