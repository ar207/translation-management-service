<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Translation Management API"
 * ),
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Enter token with `Bearer {token}`"
 * )
 */
class AuthenticateController extends Controller
{
    private $success = false, $message = '', $statusCode = 400;

    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "password_confirmation"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="john@example.com"),
     *             @OA\Property(property="password", type="string", example="secret123"),
     *             @OA\Property(property="password_confirmation", type="string", example="secret123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User Registered."),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="john@example.com"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-06-13T04:49:59.000000Z"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-06-13T04:49:59.000000Z"),
     *                 @OA\Property(property="id", type="integer", example=11)
     *             ),
     *             @OA\Property(property="token", type="string", example="1|sbPATe7dcdjuHVu3fVAzUmoIOIynzPyNrP7qfYGC62732f90")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation Error"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="code", type="string", example="email"),
     *                     @OA\Property(property="message", type="string", example="The email has already been taken.")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function register(RegisterRequest $request)
    {
        $data = $request->all();
        $user = $token = '';

        try {
            $user = User::query()->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
            ]);

            $token = $user->createToken('api-token')->plainTextToken;
            $this->message = 'User Registered.';
            $this->statusCode = 200;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
        }

        return response()->json([
            'user' => $user,
            'token' => $token,
            'message' => $this->message
        ], $this->statusCode);
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Authenticate user and return token",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", example="john@example.com"),
     *             @OA\Property(property="password", type="string", example="secret123"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User Logged In",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User Registered."),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="john@example.com"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-06-13T04:49:59.000000Z"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-06-13T04:49:59.000000Z"),
     *                 @OA\Property(property="id", type="integer", example=11)
     *             ),
     *             @OA\Property(property="token", type="string", example="1|sbPATe7dcdjuHVu3fVAzUmoIOIynzPyNrP7qfYGC62732f90")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation Error"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="code", type="string", example="email"),
     *                     @OA\Property(property="message", type="string", example="The email does not match with the records.")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function login(LoginRequest $request)
    {
        $data = $request->all();
        $user = $token = '';

        try {
            $user = User::query()->where('email', $data['email'])->first();

            if (!$user || !Hash::check($data['password'], $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['Invalid credentials.'],
                ]);
            }

            $token = $user->createToken('api-token')->plainTextToken;
            $this->message = "User Logged In";
            $this->statusCode = 200;
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
        }

        return response()->json([
            'user' => $user,
            'token' => $token,
            'message' => $this->message
        ], $this->statusCode);
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Logout user and revoke token",
     *     tags={"Auth"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User logged out successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User Logged out")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'User Logged out']);
    }
}
