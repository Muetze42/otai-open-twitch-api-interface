<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DebugController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user || !in_array($user->getKey(), config('app.admin-users', [279904718]))) {
            abort(404);
        }
    }
}
