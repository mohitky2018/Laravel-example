<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Core\DTOs\UserData;
use App\Core\Services\UserService;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

/**
 * API Controller for User CRUD operations.
 * 
 * Handles RESTful API endpoints for user management.
 * Returns JSON responses with proper HTTP status codes.
 */
class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService
    ) {
    }

    /**
     * Display a listing of users.
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $users = $this->userService->getPaginatedUsers();

        return UserResource::collection($users);
    }

    /**
     * Store a newly created user.
     *
     * @param StoreUserRequest $request
     * @return JsonResponse
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $userData = UserData::fromRequest($request);

        $user = $this->userService->createUser($userData);

        return (new UserResource($user))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified user.
     *
     * @param int $id
     * @return UserResource|JsonResponse
     */
    public function show(int $id): UserResource|JsonResponse
    {
        $user = $this->userService->findUser($id);

        if ($user === null) {
            return response()->json([
                'message' => 'User not found.',
            ], Response::HTTP_NOT_FOUND);
        }

        return new UserResource($user);
    }

    /**
     * Update the specified user.
     *
     * @param UpdateUserRequest $request
     * @param int $id
     * @return UserResource
     */
    public function update(UpdateUserRequest $request, int $id): UserResource
    {
        $userData = UserData::fromRequest($request);

        $user = $this->userService->updateUser($id, $userData);

        return new UserResource($user);
    }

    /**
     * Remove the specified user.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $this->userService->deleteUser($id);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
