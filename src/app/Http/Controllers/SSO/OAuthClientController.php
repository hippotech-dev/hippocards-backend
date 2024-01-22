<?php

namespace App\Http\Controllers\SSO;

use App\Http\Controllers\Controller;
use App\Http\Resources\SSO\OAuthClientResource;
use App\Http\Services\SSOService;
use App\Models\SSO\OAuthClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OAuthClientController extends Controller
{
    public function __construct(private SSOService $service) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clients = $this->service->getClients();
        return response()->success(OAuthClientResource::collection($clients));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = Validator::make(
            $request->only(
                "name"
            ),
            [
                "name" => "required|string|max:128"
            ]
        )
            ->validate();

        $client = $this->service->createClient($validatedData);

        return new OAuthClientResource($client);
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
