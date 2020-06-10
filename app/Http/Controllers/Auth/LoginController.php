<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Traits\UserDeviceInformation;
use App\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use InvalidArgumentException;
use jeremykenedy\LaravelRoles\Models\Role;
use Laravel\Socialite\Facades\Socialite;

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

    use AuthenticatesUsers;
    use UserDeviceInformation;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm(Request $request)
    {
        $data = [
            'title' => 'Вход',
            'roles' => Role::where('level', '<', config('general.roles.allowed_role_registration_level'))->get(),
            'action' => $request->input('action')
        ];

        return view('auth.login', $data);
    }

    public function username()
    {
        return 'username';
    }

    public function login(Request $request)
    {
        $v = $this->validateLogin($request);

        if($v->fails()) {
            return redirect()->route('login')
                ->withErrors($v->errors())->withInput();
        }

        $username = $request->input('username');
        $password = $request->input('password');

        if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
            Auth::attempt(['email' => $username, 'password' => $password]);
        }

        if (!Auth::check()) {
            Auth::attempt(['username' => $username, 'password' => $password]);
        }

        if (Auth::check()) {
            $this->setUserDeviceInformation($request->all(), Auth::user());
            $this->redirectTo();
        }

        return redirect()->back()->withErrors([
            'credentials' => 'Логин или пароль не верны'
        ]);
    }

    protected function redirectTo()
    {
        if (\Auth::user()->level() >= 3) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('account.index');
    }

    public function redirectToProvider(string $driver)
    {
        try {
            return Socialite::with($driver)->redirect();
        } catch (InvalidArgumentException $e) {
        }

        return abort(404);
    }

    public function handleProviderCallback(string $driver)
    {
       // request()->session()->put('state', request()->input('state'));
       // request()->session()->save();
        try {
            $socialiteUser = Socialite::driver($driver)->user();
        } catch (InvalidArgumentException $e) {
            return abort(404);
        }
        $user = User::whereEmail($socialiteUser->getEmail() ?? $socialiteUser->getId() . '@' . $driver . '.com')->first();

        if (!$user) {
            $data = [
                'email' => $socialiteUser->getEmail() ?? $socialiteUser->getId() . '@' . $driver . '.com',
                'name' => $socialiteUser->getName() ?? $socialiteUser->getId(),
                'username' => $socialiteUser->getEmail() ?? $socialiteUser->getId() . '@' . $driver . '.com',
            ];
            return redirect()->route('register.from.socialite', $data);
        }
        Auth::login($user);
       
        return $this->redirectTo();
    }


    protected function validateLogin(Request $request)
    {
        $v = \Validator::make($request->all(), [
            $this->username() => 'required|string',
            'password' => 'required|string',
            'accept' => 'accepted'
        ]);

        return $v;
    }
}
