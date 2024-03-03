<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\System\Academy\CourseResource;
use App\Http\Resources\System\Auth\UserResource;
use App\Http\Services\CourseService;
use App\Http\Services\UserService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private UserService $userService, private CourseService $courseService)
    {
    }

    /**
     * Get web academy identity
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAcademyIdentity()
    {
        $requestUser = auth()->user();

        $userCourses = $this->courseService->getUserActiveCourses($requestUser);

        return response()->success([
            "user" => new UserResource($requestUser),
            "courses" => CourseResource::collection($userCourses)
        ]);
    }
}
