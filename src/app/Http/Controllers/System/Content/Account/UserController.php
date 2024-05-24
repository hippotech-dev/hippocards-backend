<?php

namespace App\Http\Controllers\System\Content\Account;

use App\Enums\EUserRole;
use App\Http\Controllers\Controller;
use App\Http\Resources\System\Content\UserResource;
use App\Http\Services\AccountService;
use App\Models\User\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserController extends Controller
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
        $user = $this->service->getUserById($id);

        if (is_null($user)) {
            throw new NotFoundHttpException("User not found!");
        }

        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validatedData = Validator::make(
            $request->only(
                "name",
                "email",
                "phone",
                "sex",
                "birth_year",
                "v3_asset_id"
            ),
            [
                "name" => "sometimes|string|max:128",
                "email" => "sometimes|string|max:128",
                "phone" => "sometimes|string|max:32",
                "sex" => "sometimes|integer",
                "birth_year" => "sometimes|date",
                "v3_asset_id" => "sometimes|exists:v3_assets,id"
            ]
        )
            ->validate();

        $this->service->updateUser($user, $validatedData);

        return response()->success();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Set default password for the user
     */
    public function setDefaultPasswordForUser(User $user)
    {
        $this->service->setDefaultPasswordForUser($user);

        return response()->success();
    }

    /**
     * Account delete request
     */
    public function deleteAccountRequest()
    {
        $user = auth()->user();

        // $this->service->deleteUser($user);

        return response()->success();
    }
}
