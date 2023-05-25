<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use App\School;
use App\Teacher;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = 'teacher\home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data) {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:5', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data) {
        \DB::beginTransaction();
        try {
            $password = $data['password'];
            $user = User::create([
                'role_id' => 2,
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($password),
                'view_password' => $password,
                'send' => 1,
            ]);

            Teacher::create([
                'user_id' => $user->id, 
                'school_id' => $data['school_id']
            ]);
            \DB::commit();
        }  catch (Exception $e) {
            \DB::rollBack();
        }
        return $user;
    }

    public function showRegistrationForm(){
        $schools = School::orderBy('school', 'asc')->get();
        return view('auth.register', compact('schools'));
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();
        $user = $this->create($request->all());

        return redirect()->route('login')->with('status', 'Tu registro se guardó correctamente, por favor inicia sesión. Bienvenido.');
    }
}
