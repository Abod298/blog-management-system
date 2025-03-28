<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{

    public function index()
    {
        abort_if(Gate::denies('access-users'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return response()->json(UserResource::collection(User::with('roles')->get()));
    }


    public function show(User $user)
    {
        abort_if(Gate::denies('access-users'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return response()->json(new UserResource($user));
    }

    public function update(Request $request , User $user){
        abort_if(Gate::denies('edit-users'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $request->validate([
            'role' => 'in:1,2,3',
        ]);
        $user->roles()->sync([$request->role]);
        return response()->json(['message' => 'Role updated successfully']);
    }
    public function destroy(User $user)
    {
        return $user->delete();
    }
    public function getUserPermissions(){
        $user = auth()->user();
        return $user->roles()->with('permissions')
                ->get()->pluck('permissions')->flatten()->pluck('title');
    }
}
