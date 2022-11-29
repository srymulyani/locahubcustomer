<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Laravel\Fortify\Rules\Password;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function register(Request $request)
    {
        
         $validator = Validator::make($request->all(),
                [
                    'name' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255|unique:users',
                    'username' => 'required|string|max:255|unique:users',
                    'phone_number' => 'nullable|string|max:255',
                    'password' => ['required','string',new Password],
                ]); 
        if ($validator->fails()){
            return ResponseFormatter::error([
                'message'=>'Validation fails',
                'errors' => $validator->errors()
            ],'Authentication Failed',422);
        }
        User::create([
            'name'=> $request->name,
            'email'=> $request->email,
            'username'=> $request->username,
            'phone_number'=> $request->phone_number,
            'password'=> Hash::make($request->password),
        ]);

        $user = User::where('email', $request->email)->first();

        $tokenResult = $user->createToken('authToken')->plainTextToken; //membuat token

        return ResponseFormatter::success([
            'access_token'=>$tokenResult,
            'token_type' => 'Bearer',
            'user'=> $user,
        ],  'User Registered', 200);
        }
    public function login(Request $request)
    {
        try{
         
            $validator= Validator::make($request->all(),[
                'email' => 'email|required',
                'username' => 'username|required',
                'phone_number' => 'phone_number|required',  
                'password' => 'required'
           ]);

            // if ($validator->fails()){
            //     return response()->json($validator->errors(),400);
            // }
            $credentials = request(['email','username','phone_number','password']);
            // if (!Auth::attempt($credentials)) {
            //     return ResponseFormatter::error([
            //         'message' => 'Unauthorized',   
            //     ], 'Authentication Error', 500);
            // } 
            $user = User::where('email', $request->email)
                ->orWhere('username', $request->username)
                ->orWhere('phone_number', $request->phone_number)
                ->first();
            if (!Hash::check($request->password,$user->password,[])){
                throw new Exception("Invalid Credentials",1);
            }
            $tokenResult = $user->createToken('authToken')->plainTextToken;
              return ResponseFormatter::success(
                [
                    'access_token' => $tokenResult,
                    'token_type' => 'Bearer',
                    'user' => $user, 
                ], 'Authenticated', 200);
            } catch (Exception $error){
                return ResponseFormatter::error([
                        'message' => 'Something went wrong',
                        'error' => $error->getMessage(),
                    ], 'Authentication Failed', 500);
                }     
    }
    public function fetch(){ //ambil data user
        return ResponseFormatter::success([
            'user' =>Auth::user(),
            'Data user berhasil diambil'
        ]);
    }
    public function updateProfile(Request $request){
        $data = $request->all();

        $user = Auth::user();

        if($request->hasFile('profile_image')){
            if($user->profile_image){
                $old_path=public_path().'/uploads/profile_images/'.$user->profile_image;
                if(File::exists($old_path)){
                    File::delete($old_path);
                }
            }

            $image_name='profile-image-'.time().'.'.$request->profile_image->extension();
            $request->profile_image->move(public_path('/uploads/profile_images'),$image_name);
        }else{
            $image_name=$user->profile_image;
        }

        $user->update($data);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->bio= $request->bio;
        $user->ttl= $request->ttl;
        $user->username = $request->username;
        $user->phone_number = $request->phone_number;
        $user->profile_image = $image_name;
        $user->save();

        return ResponseFormatter::success(
            $user,
            'Profile Updated'
        );
    }
    public function logout(Request $request){
        $token = $request->user()->currentAccessToken()->delete();

        return ResponseFormatter::success($token, 'Revoked');
    }
   
    public function changePassword(Request $request){
         try {
            $request->validate([
                // 'email' =>'required',
                'current_password' => ['required','string'],
                'password' => ['required','string'],
                'confirm_password' => ['required','string'],
                'id' => 'required',
            ]);
            
            $user = User::find($request->id);
            $userPassword = $user->password;

            if (!Hash::check($request->current_password, $userPassword)) {
                return ResponseFormatter::error(null, 'Password Tidak Cocok', 404);
            }

            if ($request->password != $request->confirm_password) {
                return ResponseFormatter::error(null, 'Password Tidak Cocok', 404);
            }

            $password =  Hash::make($request->password);

            User::where('id', $user->id)->update(['password' => $password]);

            return ResponseFormatter::success($user, 'Berhasil ganti password');

        } catch (Exception $error) {
            // print($th);
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error->getMessage(),
            ], 'Authentication Failed', 500
        );
        }
    }

}