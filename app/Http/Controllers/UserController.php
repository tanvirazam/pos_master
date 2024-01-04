<?php

namespace App\Http\Controllers;

use App\Mail\OTPMail;
use Exception;
use App\Models\User;
use Firebase\JWT\JWT;
use App\Helper\JWTToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use SebastianBergmann\Type\VoidType;

class UserController extends Controller
{

    // page controller start



    function LoginPage()
    {
        return view('pages.auth.login-page');
    }

    function RegistrationPage()
    {
        return view('pages.auth.registration-page');
    }
    function SendOtpPage()
    {
        return view('pages.auth.send-otp-page');
    }
    function VerifyOTPPage()
    {
        return view('pages.auth.verify-otp-page');
    }

    function ResetPasswordPage()
    {
        return view('pages.auth.reset-pass-page');
    }
    function ProfilePage()
    {
        return view('pages.dashboard.profile-page');
    }

    function ProfileForm()
    {
        return view('pages.dashboard.profile-page');
    }






    //page controller end
    public function userRegistration(Request $request)
    {

        try {
            User::create([
                'firstName' => $request->input('firstName'),
                'lastName' => $request->input('lastName'),
                'email' => $request->input('email'),
                'mobile' => $request->input('mobile'),
                'password' => $request->input('password'),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Registration Successfully Done !',
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'status' => 'Faield',
                'message' => $exception->getMessage(),
            ]);
        }
    }

    public function userLogin(Request $request)
    {
        $count = User::where('email', '=', $request->input('email'))
            ->where('password', '=', $request->input('password'))
            ->select('id')->first();

        try {
            if ($count !== null) {
                $token = JWTToken::CreateToken($request->input('email'), $count->id);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Login Success',

                ])->cookie('token', $token, 60 * 24 * 60);
            } else {
                return response()->json([
                    'status' => 'Faield',
                    'message' => 'Unauthorize',
                ]);
            }

        } catch (Exception $exception) {
            return response()->json([
                'status' => 'Faield',
                'message' => $exception->getMessage(),
            ]);
        }
    }

    public function OTPMailSendCode(Request $request)
    {
        $mail = $request->input('email');
        $otp = rand(1000, 9999);
        $userMail = User::where('email', '=', $mail)
            ->count();



        if ($userMail === 1) {
            //mail pathanu 
            Mail::to($mail)->send(new OTPMail($otp));
            // OTO Code Table Update
            User::where('email', '=', $mail)->update(['otp' => $otp]);

            return response()->json([
                'status' => 'success',
                'message' => 'Sending Mail successfully Done !',
            ]);

        } else {
            return response()->json([
                'status' => 'faield',
                'message' => 'unauthorized',
            ]);
        }
    }

    public function VerifayOtp(Request $request)
    {
        $userEmail = $request->input('email');
        $userOtp = $request->input('otp');

        $user = User::where('email', '=', $userEmail)
            ->where('otp', '=', $userOtp)
            ->count();

        if ($user == 1) {
            //database a otp update kore dawa hoyse..

            User::where('email', '=', $userEmail)->update(['otp' => '0']);



            $token = JWTToken::CreateTokenForOtpSend($request->input('email'));

            return response()->json([
                'status' => 'successfull',
                'message' => 'done otp send'

            ])->cookie('token', $token, 60 * 24 * 60);

        } else {
            return response()->json([
                'status' => 'Faield',
                'message' => 'Faield request',
            ]);
        }
    }

    public function passwordReset(Request $request)
    {
        try {
            $email = $request->header('email');
            $password = $request->input('password');

            User::where('email', '=', $email)
                ->update([
                    'password' => $password,
                ]);

            return response()->json(
                [
                    'status' => 'success',
                    'message' => 'Password Reset successfully Done !',
                ]
            );
        } catch (Exception $exception) {
            return response()->json([
                'status' => 'faield',
                'message' => $exception->getMessage(),
            ]);
        }



    }

    public function userLogout(Request $request)
    {
        return redirect('/userLogin')->cookie('token', '', -1);
    }


    public function userProfile(Request $request)
    {
        $email = $request->header('email');

        $user = User::where('email', '=', $email)->first();

        return response()->json(
            [
                'status' => 'success',
                'message' => 'Request success',
                'data' => $user,
            ]
        );


    }

    public function userProfileUpdate(Request $request)
    {
        try {
            $email = $request->header('email');
            $firstName = $request->input('firstName');
            $lastName = $request->input('lastName');
            $mobile = $request->input('mobile');
            $password = $request->input('password');

            User::where('email', '=', $email)->update([
                'firstName' => $firstName,
                'lastName' => $lastName,
                'mobile' => $mobile,
                'password' => $password
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Update Successfully Done!',
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'status' => 'Faield',
                'message' => $exception->getMessage(),
            ]);
        }
    }
}