<?php

namespace App\Http\Controllers\Api\V1\User\Auth;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Constants\GlobalConst;
use App\Http\Helpers\Response;
use App\Traits\User\LoggedInUsers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Http\Controllers\Api\V1\User\Auth\AuthorizationController;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    protected $request_data;

    use AuthenticatesUsers, LoggedInUsers;

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $this->request_data = $request;

        $validator = Validator::make($request->all(),[
            'credentials'   => 'required|string',
            'password'      => 'required|string',
        ]);

        if($validator->fails()) {
            return Response::error($validator->errors()->all(),[]);
        }

        $validated = $validator->validate();
        if(!User::where($this->username(),$validated['credentials'])->exists()) {
            return Response::error([__("User doesn't exists!")],[],404);
        }

        $user = User::where($this->username(),$validated['credentials'])->first();
        if(!$user) return Response::error([__("User doesn't exists!")]);

        if(Hash::check($validated['password'],$user->password)) {
            if($user->status != GlobalConst::ACTIVE) return Response::error([__("Your account is temporary banded. Please contact with system admin")]);

            // User authenticated
            $token = $user->createToken("auth_token")->accessToken;
            return $this->authenticated($request,$user,$token);
        }

        return Response::error([__("Credentials didn't match")]);
    }


    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        $request->merge(['status' => true]);
        $request->merge([$this->username() => $request->credentials]);
        return $request->only($this->username(), 'password','status');
    }


    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        $request = $this->request_data->all();
        $credentials = $request['credentials'];
        if(filter_var($credentials,FILTER_VALIDATE_EMAIL)) {
            return "email";
        }
        return "username";
    }


    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard("api");
    }


    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user, $token)
    {
        $user->update([
            'two_factor_verified'   => false,
        ]);

        try{
            $mail_response = [];
            if($user->email_verified == false) {
                $mail_response = AuthorizationController::sendCodeToMail($user);
            }
        }catch(Exception $e) {
            return Response::error([$e->getMessage()],[],500);
        }

        try{
            $this->refreshUserWallets($user);
        }catch(Exception $e) {
            return Response::error([__('Login Failed! Failed to refresh wallet! Please try again')],[],500);
        }
        $this->createLoginLog($user);

        return Response::success([__('User successfully logged in')],[
            'token'         => $token,
            'user_info'     => $user->only([
                'id',
                'firstname',
                'lastname',
                'fullname',
                'username',
                'email',
                'mobile_code',
                'mobile',
                'full_mobile',
                'email_verified',
                'kyc_verified',
                'two_factor_verified',
                'two_factor_status',
                'two_factor_secret',
            ]),
            'authorization' => [
                'status'    => count($mail_response) > 0 ? true : false,
                'token'     => $mail_response['token'] ?? "",
            ],
        ],200);
    }
}
