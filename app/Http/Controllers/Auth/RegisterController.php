<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Traits\UserDeviceInformation;
use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Laravel\Socialite\Facades\Socialite;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;
    use UserDeviceInformation;

    /**
     * Where to redirect users after registration.
     *
     * @return string
     */

    protected function redirectTo()
    {
        return route('account.index');
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
        $this->middleware('checkUserForDoubleRegistration')
            ->only(['register', 'createFromSocialite']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'accept' => 'accepted',
            'username' => ['required', 'latin_num_dash', 'string', 'max:255', 'unique:users'],
            'role_id' => ['required', 'allowed_role_id']
        ]);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $v = $this->validator($request->all());

        if ($v->fails()) {
            return redirect()->route('login', ['action' => 'registration'])
                ->withErrors($v->errors())->withInput();
        }

        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);
        $this->setUserDeviceInformation($request->all(), $user);

        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
    }

    public function createFromSocialite(Request $request)
    {
        $user = User::create($request->merge([
            'password' => Str::random(),
            'email_verified_at' => \Carbon\Carbon::now()
        ])->all());

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        event(new Registered($user));
        $user->roles()->sync([4]);
        Auth::login($user);

        return redirect($this->redirectTo());
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'username' => $data['username']
        ]);

        $user->attachRole($data['role_id']);
        return $user;
    }
}
