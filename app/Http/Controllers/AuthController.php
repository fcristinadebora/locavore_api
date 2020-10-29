<?php

namespace App\Http\Controllers;

use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Firebase\JWT\ExpiredException;
use Laravel\Lumen\Routing\Controller as BaseController;

class AuthController extends BaseController
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;

    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Create a new token.
     * 
     * @param  \App\User   $user
     * @return string
     */
    protected function jwt(User $user)
    {
        $payload = [
            'iss' => "lumen-jwt", // Issuer of the token
            'sub' => $user->id, // Subject of the token
            'iat' => time(), // Time when JWT was issued. 
            'exp' => time() + 60 * 60 * 24 * 365 * 5 // Expiration time = 5 years
        ];

        // As you can see we are passing `JWT_SECRET` as the second parameter that will 
        // be used to decode the token in the future.
        return JWT::encode($payload, env('JWT_SECRET'));
    }

    /**
     * Authenticate a user and return the token if the provided credentials are correct.
     * 
     * @param  \App\User   $user 
     * @return mixed
     */
    public function login(User $user)
    {
        $this->validate($this->request, [
            'email'     => 'required|email|string',
            'password'  => 'required|string'
        ]);

        $user = User::where('email', $this->request->email)->first();

        if(!$user){
            return response()->json([
                'message' => 'E-mail não cadastrado'
            ], 400);
        }

        if(!Hash::check($this->request->password, $user->password)){
            return response()->json([
                'message' => 'Usuário ou senha incorretos'
            ], 400);
        }

        return response()->json([
            'token' => $this->jwt($user),
            'user' => $user
        ], 200);
    }

    public function register(Request $request)
    {
        $data = $this->validate($request, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string'
        ]);

        try {
            $data['password'] = Hash::make($data['password']);

            $user = User::create($data);

            return response()->json([
                'created' => $user,
                'token' => $this->jwt($user)
            ] , 200);
        } catch (\Throwable $th) {
            return response()->json($th->getTrace(), 500);
        }
    }

    public function authenticated(Request $request){
        return response()->json([
            'user' => [
                'name' => $request->auth->name,
                'email' => $request->auth->email,
                'isGrower' => $request->auth->is_grower
            ]
        ], 200);
    }
}
