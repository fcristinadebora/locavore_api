<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;

class RegistrationController extends Controller
{
    private $rules = [
        'name' => 'required|string',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string'
    ];
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function create(Request $request)
    {
        $data = $this->validate($request, $this->rules);

        try {
            if(User::emailExists($data['email'])){
                return response()->json(['message' => 'Email já está em uso'], 409);    
            }

            $data['password'] = app('hash')->make('password');

            $user = User::create($data);
            
            return response()->json(['created' => $user], 200);
        } catch (\Throwable $th) {
            return response()->json('Falha ao criar usuário.' . $th->getMessage(), 500);
        }
    }
}
