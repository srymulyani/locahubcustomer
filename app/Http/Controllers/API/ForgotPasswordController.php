<?php

namespace App\Http\Controllers\API;

// use Exception;
// use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
// use Illuminate\Support\Facades\Hash;
// use App\Models\PasswordReset;
// use App\Mail\SendCodeResetPassword;
// use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Password as RulesPassword;
// use Illuminate\Support\Facades\Mail;



class ForgotPasswordController extends Controller
{
    
    public function ForgotPassword(Request $request)
    {
    
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status == Password::RESET_LINK_SENT){
        return[
            'status'=> __($status)
        ];
        }
        
        throw ValidationException::withMessages([
        'email'=>[trans($status)],
        ]);
        // return response($response, 200);

        
        // try {
        //     $user = User::where('email',$request->email)->get();

        //     if (count($user) > 0) {
        //        $token = Str::random(40);
        //        $domain = URL::to('/');
        //        $url = $domain.'/reset-password?token='.$token;

        //        $data['url'] = $url;
        //        $data['email'] = $request->email;
        //        $data['title'] = "Password Reset";
        //        $data['body'] = "Please click this link to reset your password";
                
        //        Mail::send('forgotPassword',['data'=>$data], function($message) use ($data){
        //         $message->to($data['email'])->subject($data['title']);
        //        });

        //        $datetime = Carbon::now()->format('Y-m-d H:i:s');
        //        PasswordReset::updateOrCreate(
        //         ['email'=>$request->email],
        //         [
        //             'email'=>$request->email,
        //             'token'=> $token,
        //             'created_at' =>  $datetime
        //         ]
        //         );
        //         return response()->json(['success'=>true,'msg'=>'Please Check Your Email To Reset Your Password']);

        //     }else{
        //          return response()->json(['success'=>false,'msg'=>'User Not Found']);
        //     }

        // } catch (Exception $error) {
        //     return response()->json(['success'=>false,'msg'=>$error->getMessage()]);
        //     //throw $th;
        // }


        // $data = $request->validate([
        //     'email' => 'required|email|exists:users',
        // ]);

        // // Delete all old code that user send before.
        // PasswordReset::where('email', $request->email)->delete();

        // // Generate random code
        // $data['token'] = mt_rand(100000, 999999);

        // // Create a new code
        // $tokenData = PasswordReset::create($data);

        // // Send email to user
        // Mail::to($request->email)->send(new SendCodeResetPassword($tokenData->token));

        // return response(['message' => trans('passwords.sent')], 200);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', RulesPassword::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                $user->tokens()->delete();

                event(new PasswordReset($user));
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return response([
                'message'=> 'Password reset successfully'
            ]);
        }

        return response([
            'message'=> __($status)
        ], 500);

    }
}
