<?php

namespace App\Http\Controllers;

use App\Http\Requests\OtpCodeRequest;
use App\Http\Requests\OtpLoginRequest;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\SuccessResource;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserCodes;
use Exception;
use Illuminate\Support\Facades\Hash;
use Kavenegar;
use App\Models\Referral;
use Illuminate\Routing\Controller;

class LoginController extends Controller
{



    public function otpCodeRequest(OtpCodeRequest $request)
    {

        $token = rand(100000, 999999);
        $now = strtotime(date('Y-m-d H:i:s'));
        $expire_date = date('Y-m-d H:i:s', $now + (env('OTP_EXPIRE_MINUTES') * 60));  //add the environment variable in .env file
        $password = Hash::make($token);
        $user = User::where('phone', $request->phone)->first();
        if (!$user) {
            $user = User::create(["phone" => $request->phone, "password" => $password]);
        } 
                
        $usercode = UserCodes::where("user-id", $user->id)->first();
        if (!$usercode) {
            $usercode = UserCodes::create(["code" => $password, "user-id" => $user->id, 'expired' => 0, 'expire_date' => $expire_date]);
        } else {
            $usercode->update(['code' => $password, 'expired' => 0, 'expire_date' => $expire_date]);
        }
        try {

            // In this part you should send the OTP code via SMS service provider API.
            // For this purpose I used kavenegar. 
            $sender = env("OTP_SENDER");
            $receptor = strval($user->phone);
            $template = "login";
            $result = Kavenegar::VerifyLookup($receptor, $token, $token2 = null, $token3 = null, $template, $type = null);
            return $result;
        } catch (\Kavenegar\Exceptions\ApiException $e) {
            // در صورتی که خروجی وب سرویس 200 نباشد این خطا رخ می دهد
            // If the response code isn't 200 this error happens
            return new ErrorResource((object)[  // 
                'error' => __('errors.KeveNegarApiException'),
                'message' => $e->errorMessage(),
            ]);
        } catch (\Kavenegar\Exceptions\HttpException $e) {
            // در زمانی که مشکلی در برقرای ارتباط با وب سرویس وجود داشته باشد این خطا رخ می دهد
            // In case of server connection problems this error happens
            return new ErrorResource((object)[
                'error' => __('errors.KeveNegarHttpException'),
                'message' => $e->errorMessage(),
            ]);
        }
    }


   


    public function OtpLogin(OtpLoginRequest $request)
    {

        try {
            $user = User::where('phone', $request->phone)->first();
            $dbCode = UserCodes::where([
                ['user-id', '=', $user->id],
                ['expired', '=', 0],
                ['expire_date', '>=', date('Y-m-d H:i:s')]
            ])->first();
        } catch (Exception $e) {
            return new ErrorResource((object)[
                'error' => __('errors.Phone Number Is Incorrect'),
                'message' => __('errors.Phone Number Is Incorrect'),
            ]);
        }

        if (!$dbCode || !Hash::check($request->code, $dbCode->code)) {
            return new ErrorResource((object)[
                'error' => __('errors.Credentials Are Incorrect'),
                'message' => __('errors.Credentials Are Incorrect'),
            ]);
        }
        UserCodes::where('user-id', $user->id)->update(['expired' => 1]);
        return new SuccessResource((object)['data' => (object)['accessToken' => $user->createToken('AccessToken')->accessToken, 'refreshToken' => '', 'tokenType' => 'Bearer']]);
    }

    public function Logout(Request $request)
    {
        try {
            $token = $request->user()->token();
            $token->revoke();
            return new SuccessResource((object)['data' => 'you are logged out successfully']);
        } catch (Exception $e) {
            return new ErrorResource((object)[
                'error' => __('errors.Server Error Occured'),
                'message' => __('errors.Server Error Occured'),
            ]);
        }
    }
}
