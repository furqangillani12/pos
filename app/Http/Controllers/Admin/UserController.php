<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function assignRoleForm() {
        $users = User::all();
        $roles = Role::all();

//        foreach ($users as $user) {
//            echo "User: " . $user->name . " - Roles: " . implode(', ', $user->getRoleNames()->toArray()) . "<br>";
//        }
//
//        dd('Check complete');

        return view('users.assign_role', compact('users', 'roles'));
    }


    public function assignRole(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        $newRole = $request->role;

        // Remove all current roles
        $user->syncRoles([$newRole]);

        return redirect()->back()->with('success', 'Role assigned successfully.');
    }


}
