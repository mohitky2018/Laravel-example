<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Core\DTOs\UserData;
use App\Core\Services\UserService;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Web Controller for User CRUD operations.
 * 
 * Handles web-based endpoints for user management.
 * Returns Blade views and redirects with flash messages.
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
     * @return View
     */
    public function index(): View
    {
        $users = $this->userService->getPaginatedUsers();

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     *
     * @return View
     */
    public function create(): View
    {
        return view('users.create');
    }

    /**
     * Store a newly created user.
     *
     * @param StoreUserRequest $request
     * @return RedirectResponse
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $userData = UserData::fromRequest($request);
        $this->userService->createUser($userData);

        return redirect()
            ->route('users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user.
     *
     * @param int $id
     * @return View|RedirectResponse
     */
    public function show(int $id): View|RedirectResponse
    {
        $user = $this->userService->findUser($id);

        if ($user === null) {
            return redirect()
                ->route('users.index')
                ->with('error', 'User not found.');
        }

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param int $id
     * @return View|RedirectResponse
     */
    public function edit(int $id): View|RedirectResponse
    {
        $user = $this->userService->findUser($id);

        if ($user === null) {
            return redirect()
                ->route('users.index')
                ->with('error', 'User not found.');
        }

        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified user.
     *
     * @param UpdateUserRequest $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(UpdateUserRequest $request, int $id): RedirectResponse
    {
        $userData = UserData::fromRequest($request);
        // dd($userData);
        $this->userService->updateUser($id, $userData);
        return redirect()
            ->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->userService->deleteUser($id);

        return redirect()
            ->route('users.index')
            ->with('success', 'User deleted successfully.');
    }
}
