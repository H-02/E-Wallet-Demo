<?php

namespace App\Http\Controllers\v1;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\AuthResource;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Handle login for API.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */

    protected function response($status = 0, $data = [], $message = "")
    {
        return [
            'status' => $status,
            'data' => $data,
            'message' => $message
        ];
    }

    public function login(LoginRequest $loginRequest)
    {
        try{
            $user = User::where('email', $loginRequest->email)
                ->active()
                ->first();

            if(is_null($user)){
                return $this->response(401, [], "No active users found with email.");
            }

            if(!Hash::check($loginRequest->password, $user->password)){
                return $this->response(401, [], "User credentials does not matched.");
            }

            // remove all previous tokens
            $user->tokens()->delete();

            // create new token
            $token = $user->createToken('auth')->plainTextToken;

            $user->user_token = $token;
            return $this->response(200, ["user" => new AuthResource($user)], "Logged in successfully");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->response(500, [], "Error occurred in login");
        }
    }

    /**
     * Handle logout functionality.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $request->user()->tokens->each(function ($token) {
            $token->delete();
        });
        return $this->response(200, [], "Logged out successfully");
        
    }
}