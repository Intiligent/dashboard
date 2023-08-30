<?php

namespace Dashboard\Http\Controllers;

use Auth;
// use Meta;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\User;

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

    protected $guard = 'dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:'.$this->guard)->except('logout');
    }

    /**
     * Where to redirect users after login.
     *
     * @return string
     */
    protected function redirectTo()
    {
        return route('dashboard.home');
    }

    /**
     * Login page
     *
     * @return Illuminate\Support\Facades\View
     */
    public function showLoginForm()
    {
        // Meta::set('title', __('auth.login'));
        // Meta::set('description', config('META_DESCRIPTION', 'Dashboard website monitoring'));
        return view('dashboard::pages.login');
    }

    /**
     * Authorithation request
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postLogin(Request $request)
    {
        $this->validate($request, [
            $this->username() => ['required', 'email', 'max:255'],
            'password' => ['required', 'min:4'],
        ]);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        // получить обьект пользователя
        $user = User::where($this->username(), $request->email)->first();

        // check permission to access for dashboard
        // todo refactoring to autorithe
        // if ($user && !$user->hasPermission('postLoginDashboard')) {
        //     return redirect()->back()
        //         ->withInput($request->only($this->username(), 'remember'))
        //         ->withErrors([$this->username() => ['Access denied']]);
        // }
        $credentials = [
            $this->username() => $request->email,
            'password' => $request->password,
        ];
        if (Auth::guard($this->guard)->attempt($credentials, $request->has('remember'))) {
            // dd($this->redirectTo());
            // Event::fire('dashboard.login', [$request, $user]);
			return redirect()->intended($this->redirectTo());
		}

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        // dd(trans('auth.failed'));

        // redirect with errors
        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
			->withErrors([$this->username() => [trans('auth.failed')]]);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        Auth::guard($this->guard)->logout();
        return redirect()->route('dashboard.login');
    }
}
