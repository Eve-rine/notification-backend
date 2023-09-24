<?php

namespace App\Http\Controllers\UserManagement;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    //get users
    public function getUsers(Request $request)
    {
        $users = User::query()
            ->get();
        return response()->json([
            'data' => $users,
            'success' => true,
        ], 200);
    }
}
