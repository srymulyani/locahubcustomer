<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;


class ResetPasswordController extends Controller
{
   public function SendResetLinkResponse(Request $request)
   {
      //password.reset
      $input = $request->only('email','token', 'password', 'password_confirmation');
      $validator = Validator::make($input, [
      'token' => 'required',
      'email' => 'required|email',
      'password' => 'required|confirmed|min:8',
      ]);
      if ($validator->fails()) {
         return response(['errors'=>$validator->errors()->all()], 422);
      }
         $response = Password::reset($input, function ($user, $password) {
         $user->forceFill([
         'password' => Hash::make($password)
      ])->save();

      //$user->setRememberToken(Str::random(60));
      event(new PasswordReset($user));
      });
      if($response == Password::PASSWORD_RESET){
         $message = "Password reset successfully";
      }else{
         $message = "Email could not be sent to this email address";
      }
      $response = ['data'=>'','message' => $message];
      return response()->json($response);
   }
    
}
