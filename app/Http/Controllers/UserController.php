<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use League\Fractal\TransformerAbstract;
use App\Transformers\UserTransformer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

use League\Fractal\Serializer\JsonApiSerializer;

class UserController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth:sanctum', ['except' => ['login', 'register']]);
    // }

    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,150',
            'email' => 'required|email|between:4,150|unique:users',
            'password' => 'required|string|between:6,150',
            'is_admin' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 422);
        }

        //объединяет два массива
        $user = User::create(array_merge( 
            $validator->validated(),
           // ['password'=>bcrypt($request->password)]
        ));

        if ($request->input('is_admin')) {
            $user->is_admin = true;
            $user->save();
            $token = $user->createToken('Personal Access Token', ['admin'])->plainTextToken;
            $user->token = $token;
            $user = fractal($user, new UserTransformer())->serializeWith(new JsonApiSerializer());
            return response()->json(
                $user, 201);
        }
        $token = $user->createToken('Personal Access Token', ['user'])->plainTextToken;
        $user->token = $token;
        $user = fractal($user, new UserTransformer())->serializeWith(new JsonApiSerializer());
        return response()->json(
            $user, 201);
    }

    public function login(Request $request)
    {
        //создать объект, который проверяет реквест 
        $validator = Validator::make($request->all(), [ 
            'email' => 'required|email',
            'password' => 'required|string|min:6'
        ]);

        //проверяет реквест
        if ($validator->fails()) { 
            return response()->json([
                'message' => $validator->errors()->first()
            ], 422);
        }

        //проверяем действительны ли данные, есть ли пользователь с такими данными
        // $credentials = request(['email', 'password']);
        // if (!auth()->attempt($credentials)) {
        //     return response()->json([], 422);
        // }
        // $user = $request->user();


        $count = User::where(['email' => $request['email'], 'password' => $request['password']])->count();
        
        if ($count != 1)
            return response()->json([], 422);
        
        $user =  User::where(['email' => $request['email'], 'password' => $request['password']])->first();

        if ( $user->is_admin) {
            $token = $user->createToken('Personal Access Token', ['admin'])->plainTextToken;
            $user->token = $token;

            $user = fractal($user, new UserTransformer())->serializeWith(new JsonApiSerializer());
            return response()->json($user, 200);
        }
        $token = $user->createToken('Personal Access Token', ['user'])->plainTextToken;
        $user->token = $token;
        $user = fractal($user, new UserTransformer())->serializeWith(new JsonApiSerializer());
        return response()->json($user, 200);
    }

    public function profile() {
        return response()->json(auth()->user());
    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete;
            return response()->json([], 204);
    }
}
