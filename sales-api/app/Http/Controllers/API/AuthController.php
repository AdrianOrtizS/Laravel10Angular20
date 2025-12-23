<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;
use Illuminate\Validation\Rule;
use Mail;
use App\Mail\VerifyMail;
use App\Mail\RecoverPasswordMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Log;

class AuthController extends Controller
{
    public function __construct()
    {
          $this->middleware('auth:api', ['except' => 
                                            [   
                                                'register','getBranches','login',
                                                'recover_password_email',
                                                'update_password_for_code',
                                                'verified_auth','verified_code'
                                            ]
                                        ]);
    }

    public function getBranches()
    {
        $branches = Branch::orderBy('name')->get();

        return response()->json(
            [   
                'code'  => 200,
                'Branches' => ($branches)
            ]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make(request()->all(), [
                            'name'      => 'required|min:10|max:100',
                            'email'     => 'required|email|unique:users',
                            'id_point_of_sale' => 'required',
                            'password'  => 'required|min:8|confirmed',
                        ]);
 
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        try{
            DB::beginTransaction();

            $user = User::create([
                            'name'  => request()->name,
                            'email' => request()->email,
                            'role'  => 'user',
                            'id_point_of_sale' => request()->id_point_of_sale,
                            'password' => Hash::make(request()->password),
                            'uniqid' => uniqid(),
                            'state'  => 1
                            ]);
            // $token = JWTAuth::fromUser($user);
            
            Mail::to($user->email)->send(new VerifyMail($user));
            DB::commit();
            return response()->json(['user'=>$user], 201);           
            
        }catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error creating account: ' . $e->getMessage());
            
            return response()->json([
                'code' => 500,
                'message' => 'Error interno del servidor'
            ], 500);
        }

    }

    public function verified_auth(Request $request)
    {
        // se ejecuta desde el front (ngOnInit())
        // al abrir enlace desde correo 
        // busca al usuario con uniqid y actualiza
        // campo email_verified_at

        $user = User::where('uniqid', $request->uniqid)->first();
        if($user){
            $user->update(['email_verified_at'=> now()]);
            return response()->json(['message' => 200]);
        }
        return response()->json(['message' => 403]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make(request()->all(), [
                            'email' => 'required|email',
                            'password' => 'required',
                        ]);
 
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        try{
            if (!$token = auth('api')->attempt([
                    'email'=> request()->email,
                    'password'=> request()->password])
            ){
                return response()->json([ 'code'    => 401 ,
                                          'message' => 'Usuario o clave incorrcto']);
            }

            // solo ingresa si esta lleno el campo email_verified_at
            if(!auth('api')->user()->email_verified_at){
                $token = null;
                return response()->json([ 'code'    => 402,  
                                          'message' => 'Email no esta verificado']);
            }
            if(auth('api')->user()->state == 0){
                $token = null;
                return response()->json([ 'code'    => 403,
                                          'message' => 'Usuario desactivado']);
            }
            return $this->respondWithToken($token);
        }catch(JWTException $e){
               return response()->json(['error' => 'Could not create token', $e], 500);
        }

    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL(),
            'user' => [
                "name"      => auth('api')->user()->name,
                "email"     => auth('api')->user()->email,
                "point_of_sale" => auth('api')->user()->pointsOfSale->codigo_punto_emision,
                "branch"    => auth('api')->user()->pointsOfSale->branch,
                "imagen"    => env('APP_URL').'storage/'.auth('api')->user()->imagen
            ] 
        ]);
    }

    // public function updateUserLog()
    // {
    //     $user   = User::find(auth('api')->user()->id);
    //     $user->name  = request()->name;
    //     $user->email = request()->email;
    //     $user->update();

    //     return response()->json(['message' => 'User profile updated successfully'], 200);
    // }

    public function updateUserLog(Request $request)
    {   
        $user = auth('api')->user();

        // Validar solo campos que se pueden actualizar
        $validator = Validator::make($request->all(), [
            'name'   => 'sometimes|string|min:3|max:100',
            // 'email'  => 'sometimes|email|unique:users,email,' . $user->id,
            'imagen' => 'sometimes|file|image|max:2048', // máximo 2MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Actualizar nombre y otros datos simples
            if ($request->filled('name')) {
                $user->name = $request->name;
            }

            if ($request->hasFile('imagen')) {
                if ($user->imagen && Storage::exists($user->imagen)) {
                    Storage::delete($user->imagen);
                }

                $path = $request->file('imagen')->store('users', 'public');
                $user->imagen = $path;
            }

            $user->save();

            $mappedUser = [
                'name'  => $user->name,
                'email' => $user->email,
                'point_of_sale' => $user->pointsOfSale->codigo_punto_emision,
                'branch'    => $user->pointsOfSale->branch,
                'imagen'    => $user->imagen ? env('APP_URL').'storage/'.$user->imagen : null,
            ];

            return response()->json([
                'code' => 200,
                'message' => 'Perfil actualizado correctamente',
                'user' => $mappedUser
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Error al actualizar el perfil',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update_password_userLog(Request $request)
    {
        $validator = Validator::make(request()->all(), [
                            'new_password' => 'required',
                        ]);
        
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        
        $new_password  = request()->new_password;
        $user = auth('api')->user();
        
        if(!$user){
            return response()->json(['message' => 403]);
        }else{
            $user->update([
                        'password' => $new_password
                     ]);
            return response()->json(['message' => 200]); 
        }
    }

    public function me()
    {
        return response()->json(auth('api')->user());
    }

    public function logout()
    {
            //El usuario tiene un token válido
        if (auth('api')->check()) {
            auth('api')->logout();
            return response()->json(['code'=>'201','message'=>'Logged out']);
        }else{
            auth('api')->logout();
            return response()->json(['code'=>'401','message'=>'token Invalido']);
        }
    }

    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    
    /////////////////////////////////////////////////////////////////////
    //////////////////////   RECUPERAR PASSWORD      ////////////////////
    // Front => ingresa email para recuperar password y llama a esta funcion
    
    public function recover_password_email(Request $request)
    {
        $validator = Validator::make(request()->all(), 
                        [
                            'email' => 'required|email',
                        ]);
 
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        error_log(request()->email);
        $user = User::where('email', $request->email)->first();

        if($user){
            $user->update(['code_verified'=> uniqid()]);
            ////////////////////////////////////////////////////////
            // envia correo con formulario para ingresar nueva clave
            Mail::to($request->email)->send(new RecoverPasswordMail($user));
            return response()->json(['message' => 200]);
        }else{
            return response()->json(['message' => 403]); 
        }
    }

    
    public function update_password_for_code(Request $request)
    {
        error_log($request->code_verified);
        $user = User::where('code_verified', $request->code_verified)->first();
        if($user){
            $user->update(
                ['password' => Hash::make($request->new_password), 
                 'code_verified'=> null]);
            return response()->json(['message' => 200]);
        }else{
            return response()->json(['message' => 403]); 
        }
    }
    

}
