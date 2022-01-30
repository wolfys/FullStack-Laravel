<?php

namespace App\Http\Controllers;

class ProfileController extends Controller
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        return response()->json(auth()->user());
    }
}
