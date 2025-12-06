<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $users = $this->service->getAllUsers();
        $roles = $this->service->getAllRoles();
        $departments = $this->service->getAllDepartments();

        if ($request->wantsJson()) {
            return response()->json($users);
        }

        return view('users.index', compact('users', 'roles', 'departments'));
    }

    public function store(UserRequest $request)
    {
        $user = $this->service->createUser($request->validated());

        if ($request->wantsJson()) {
            return response()->json($user, 201);
        }

        return redirect()->back()->with('success', 'User created successfully!');
    }

    public function edit($id)
    {
        $user = $this->service->findUser($id);
        $users = $this->service->getAllUsers();
        $roles = $this->service->getAllRoles();
        $departments = $this->service->getAllDepartments();

        return view('users.index', compact('users', 'roles', 'departments', 'user'));
    }

    public function update(UserRequest $request, $id)
    {
        $user = $this->service->updateUser($id, $request->validated());

        if ($request->wantsJson()) {
            return response()->json($user);
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully!');
    }

    public function resetPassword(Request $request, $id)
    {
        $user = $this->service->resetUserPassword($id);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Password reset to default (123456) successfully']);
        }

        return redirect()->route('users.index')->with('success', "Password for {$user->name} has been reset to default (123456)!");
    }

    public function destroy(Request $request, $id)
    {
        $this->service->deleteUser($id);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'User deleted successfully']);
        }

        return redirect()->route('users.index')->with('success', 'User deleted successfully!');
    }
}
