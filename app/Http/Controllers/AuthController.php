<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
// use App\Repositories\Auth\AuthRepo;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
  // protected $authRepo;
  // public function __construct(AuthRepo $authRepo)
  // {
  //   $this->authRepo = $authRepo;
  // }

  public function signup(UserRequest $request)
  {
    $validated = $request->validated();
    if ($validated) {
      $userName = User::where('username', $request->username)->first();
      if ($userName) {
        return response()->json([
          'message' => 'User name conflict! please insert another'
        ], 409);
      }
      $user = User::create([
        'username' => $request->username,
        'fullname' => $request->fullname,
        'password' => Hash::make($request->password)
      ]);
      $token = JWTAuth::fromUser($user);
      return response()->json([
        'token' => $token
      ], 200);
    }
    return response()->json([
      'message' => 'Validation error!'
    ], 400);
  }

  public function login(UserRequest $request)
  {
    $validated = $request->validated();
    if ($validated) {
      if (!Auth::attempt($validated)) {
        // throw ValidationException::withMessages([
        //   'message' => 'Credentials errors'
        // ]);
        return response()->json([
          'message' => 'Bad request!'
        ], 400);
      }
      $user = $request->user();
      $token = JWTAuth::fromUser($user);

      return response()->json([
        'token' => $token
      ], 200);
    }
  }
  public function listuser()
  {
    $users = User::paginate(5);
    return response()->json([
      'data' => $users
    ]);
  }
}
