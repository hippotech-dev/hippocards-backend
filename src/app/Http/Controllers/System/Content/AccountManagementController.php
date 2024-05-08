<?php

namespace App\Http\Controllers\System\Content;

use App\Http\Controllers\Controller;
use App\Http\Resources\System\Content\UserResource;
use App\Http\Services\AccountService;
use App\Http\Services\UserService;
use Illuminate\Http\Request;

class AccountManagementController extends Controller
{
    public function __construct(private AccountService $service)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->only("filter");

        $users = $this->service->getUsersWithPage($filters);

        return UserResource::collection($users);
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
