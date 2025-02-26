<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;;
use Laravel\Passport\HasApiTokens;

use Illuminate\Foundation\Auth\User as Authenticatable;
class AuthController extends Controller
{
   
    public function register(Request $request)
    {
       
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'gender' => 'required|in:male,female',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
      
        $user = User::create([
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'gender' => $request->gender,
        ]);
        $response = [];
       
        $token = $user->createToken('YourAppName')->accessToken;
        $response['token'] = $token;
        $response['user_id'] = $user->id;
        $response['email'] = $user->email;
        $response['password'] = $user->password;
        $response['gender'] = $user->gender;
        return response()->json([
            'status' => 1,
            'message' => 'Пользователь зарегистрирован',
            'data' => $response,
            
        ]);
    }
    
    public function login(Request $request)
    {
        if(Auth::attempt(["email" => $request->email, "password" => $request->password])){
            /** @var \App\Models\User $user */
            $user=Auth::user();
            $response = [];
            $token = $user->createToken('YourAppName')->accessToken;
            $response['token'] = $token;
            $response['user_id'] = $user->id;
            $response['email'] = $user->email;  
            return response()->json([
                'status'=> 1,
                'message'=> 'User authorization',
                'data'=> $response,]);

        }
        return response()->json([
            'status'=> 0,
            'message'=> 'User is not authorization',
            'data'=> null,
        //return response()->json(['user' => Auth::user()]);
        ]);
    }
    public function delete(Request $request)
{
    // Валидация входных данных
    $validator = Validator::make($request->all(), [
        'email' => 'required|email|exists:users,email',
        'password' => 'required|min:6',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 0,
            'message' => 'Ошибка валидации',
            'errors' => $validator->errors(),
        ], 422);
    }

    //$user = User::where('email', $request->email)->where('password', $request->password)->first();

    $user = User::where('email', $request->email)->first();

    if ($user && Hash::check($request->password, $user->password)) {
        // Если пользователь найден и пароль верный, удаляем пользователя
        $user->delete();

        return response()->json([
            'status' => 1,
            'message' => 'Пользователь успешно удален',
        ]);
    }

    return response()->json([
        'status' => 0,
        'message' => 'Пользователь не найден',
    ], 404);
}

public function update(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'sometimes|email|unique:users,email,' . Auth::id(),
        'password' => 'sometimes|min:6',
        'gender' => 'sometimes|in:male,female',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 0,
            'message' => 'Ошибка валидации',
            'errors' => $validator->errors(),
        ], 422);
    }

    
    /** @var User $user */
    $user = Auth::user();

    if (!$user) {
        return response()->json([
            'status' => 0,
            'message' => 'Пользователь не аутентифицирован',
        ], 401);
    }

    if ($request->has('email')) {
        $user->email = $request->email;
    }

    if ($request->has('password')) {
        $user->password = bcrypt($request->password);
    }

    if ($request->has('gender')) {
        $user->gender = $request->gender;
    }

    $user->save();

    return response()->json([
        'status' => 1,
        'message' => 'Данные пользователя успешно обновлены',
        'data' => [
            'user_id' => $user->id,
            'email' => $user->email,
            'gender' => $user->gender,
            'password' => $user->password,
        ],
    ]);
}
public function show_all(){
    $response = [];
    $users = User::all()->toArray();
    foreach ($users as $user) {
        $response[] = $user;
    }
    return response()->json($response);
}
public function show_user(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
    ]);
    if ($validator->fails()) {
        return response()->json([
            'status' => 0,
            'message' => 'Ошибка валидации',
            'errors' => $validator->errors(),
        ], 422);
    }
    $response = [];
    $user = User::where('email', $request->email)->first();
    $response[] = $user;
    return response()->json([
        'status'=> 1,
        'message'=> "$user->email is register",
        'date' => $response,
    ]); 
}
public function add_pc_config(Request $request){
    $validator = Validator::make($request->all(), [
        'motherboard'=> 'min:6||max:128',
        'processor'=> 'min:6||max:128',
        'graphic_card'=>'min:6||max:128',
        'ram'=> 'min:6||max:128'

    ]);
    if ($validator->fails()) {
        return response()->json([
            'status' => 0,
            'message' => 'Ошибка валидации',
            'errors' => $validator->errors(),
        ], 422);

    }
    /** @var User $user */
    $user = Auth::user();
     if (!$user) {
         return response()->json([
             'status' => 0,
             'message' => 'Пользователь не аутентифицирован',
         ], 401);
     }
     $pcConfigData = [
        'user_id' => $user->id,
    ];

    if ($request->has('motherboard')) {
        $pcConfigData['motherboard'] = $request->motherboard;
    }

    if ($request->has('processor')) {
        $pcConfigData['processor'] = $request->processor;
    }

    if ($request->has('graphic_card')) {
        $pcConfigData['graphic_card'] = $request->graphic_card;
    }

    if ($request->has('ram')) {
        $pcConfigData['ram'] = $request->ram;
    }

    // Вставляем данные в таблицу pc_config
    DB::table('pc_config')->insert($pcConfigData);
    return response()->json([
        'status' => 1,
        'message' => 'Конфигурация ПК успешно добавлена',
        'data' => $pcConfigData,
    ]);
     
}
public function update_pc_conf(Request $request,$id){
    $validator = Validator::make($request->all(), [
       
        'motherboard' => 'min:6|max:128',
        'processor' => 'min:6|max:128',
        'graphic_card' => 'min:6|max:128',
        'ram' => 'min:6|max:128'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 0,
            'message' => 'Ошибка валидации',
            'errors' => $validator->errors(),
        ], 422);
    }

    /** @var User $user */
    $user = Auth::user();
    if (!$user) {
        return response()->json([
            'status' => 0,
            'message' => 'Пользователь не аутентифицирован',
        ], 401);
    }
   
    $pcConfigData = [];
    if ($request->has('motherboard')) {
        $pcConfigData['motherboard'] = $request->motherboard;
    }
    if ($request->has('processor')) {
        $pcConfigData['processor'] = $request->processor;
    }
    if ($request->has('graphic_card')) {
        $pcConfigData['graphic_card'] = $request->graphic_card;
    }
    if ($request->has('ram')) {
        $pcConfigData['ram'] = $request->ram;
    }

    $updated = DB::table('pc_config')->where('id', $id)->update($pcConfigData);

    if ($updated) {
        return response()->json([
            'status' => 1,
            'message' => 'Указанная конфигурация обновлена!',
            'data' => $pcConfigData,
        ]);
    } else {
        return response()->json([
            'status' => 0,
            'message' => 'Конфигурация не найдена или нет изменений!'
        ], 404);
    }
}


    // Получение профиля пользователя
    public function profile(Request $request)
    {
        return response()->json(['user' => Auth::user()]);
    }
}
