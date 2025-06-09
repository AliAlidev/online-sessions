<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    protected function authenticated(Request $request, $user)
    {
        if (isEventUser() || isClientUser())
            return redirect()->route('events.index');

        if (Auth::user()->hasRole('super-admin'))
            return redirect()->route('insights.index');
        else if (Auth::user()->hasAnyPermission(['create_event', 'update_event', 'delete_event']))
            return redirect()->route('events.index');
        else if (Auth::user()->hasAnyPermission(['create_client', 'update_client', 'delete_client']))
            return redirect()->route('clients.index');
        else if (Auth::user()->hasAnyPermission(['create_role', 'update_role', 'delete_role']))
            return redirect()->route('roles.index');
        else
            abort(403);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $fieldType = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        if (Auth::attempt([$fieldType => $request->email, 'password' => $request->password], $request->filled('remember'))) {
            return $this->authenticated($request, Auth::user());
        }

        return back()->withErrors(['email' => 'Invalid credentials.'])->withInput();
    }
}
