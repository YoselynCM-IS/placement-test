<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Mail\SendData;
use App\Teacher;
use App\School;
use App\User;

class AdministratorController extends Controller
{
    // PAGINA PRINCIPAL
    public function home(){
        return view('users.administrator.home');
    }

    // EXAMENES
    public function exams(){
        $s = School::withCount('exams')->orderBy('school', 'asc')->get();
        $schools = $s->where('exams_count', '>', 0);
        return view('users.administrator.exams', compact('schools'));
    }

    // TEACHERS
    public function teachers(){
        // $s = School::withCount('teachers')->orderBy('school', 'asc')->get();
        // $schools = $s->where('teachers_count', '>', 0);
        // , compact('schools')
        return view('users.administrator.teachers');
    }

    // GUARDAR TEACHER
    public function save_teacher(Request $request){
        // VALIDACIÓN DE DATOS
        $this->validate($request, [
            'name' => ['required', 'string', 'min:5'],
            'email' => ['required', 'email', 'max:60', 'unique:users']
        ]);

        \DB::beginTransaction();
        try {
            $password = Str::random(5);
            $user = User::create([
                'role_id'       => 2,
                'name'          => Str::of($request->name)->upper(),
                'email'         => $request->email,
                'password'      => bcrypt($password),
                'view_password' => $password
            ]);

            $teacher = Teacher::create([
                'user_id' => $user->id, 
                'school_id' => $request->school_id
            ]);

            \DB::commit();
        }  catch (Exception $e) {
            \DB::rollBack();
        }

        return response()->json($teacher);
    }

    // ACTUALIZAR PROFESOR
    public function update_teacher(Request $request){
        // VALIDACIÓN DE DATOS
        $this->validate($request, [
            'name' => ['required', 'string', 'min:5'],
            'email' => ['required', 'email', 'max:60']
        ]);

        $user = User::find($request->id);

        \DB::beginTransaction();
        try {
            $user->update([
                'name'          => Str::of($request->name)->upper(),
                'email'         => $request->email
            ]);

            Teacher::where('user_id', $user->id)->update([
                'user_id' => $user->id, 
                'school_id' => $request->school_id
            ]);

            \DB::commit();
        }  catch (Exception $e) {
            \DB::rollBack();
        }
        return response()->json();
    }

    // REVISAR SI EL PROFESOR TIENE GRUPOS O EXAMENES ASIGNADOS
    public function teacher_assignments(Request $request){
        $user = User::find($request->user_id);
        $teacher = Teacher::where('user_id', $user->id)
                    ->withCount('exams', 'groups')->first();
        return response()->json($teacher);
    }

    // ELIMINAR PROFESOR
    public function delete_teacher(Request $request){
        \DB::beginTransaction();
        try {
            Teacher::where('user_id', $request->user_id)->delete();
            User::whereId($request->user_id)->delete();
            \DB::commit();
        }  catch (Exception $e) {
            \DB::rollBack();
        }
        return response()->json();
    }

    public function send_data(Request $request){
        \DB::beginTransaction();
        try {
            $user = User::find($request->user_id);
            $user->update([ 'send' => 1 ]);

            Mail::to($user->email)->send(new SendData($user));
            \DB::commit();
        }  catch (Exception $e) {
            \DB::rollBack();
        }
        return response()->json();
    }

}
