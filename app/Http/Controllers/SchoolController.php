<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Teacher;
use App\School;
use App\Exam;
use App\User;

class SchoolController extends Controller
{
    // MOSTRAR LAS ESCUELAS 
    public function index(){
        $schools = School::orderBy('school', 'asc')->get();
        return response()->json($schools);
    }

    // MOSTRAR UNA ESCUELA
    public function show(Request $request){
        $school = School::whereId($request->school_id)
                    ->with('teachers.user')->first();
        return response()->json($school);
    }

    // MOSTRAR COINCIDENCIAS DE ESCUELA POR NOMBRE
    public function by_name(Request $request){
        $schools = School::where('school', 'like', '%'.$request->school.'%')
                    ->orderBy('school', 'asc')->get();
        return response()->json($schools);
    }

    // MOSTRAR PROFESORES DE UNA ESCUELA
    public function show_teachers(Request $request){
        $teachers = Teacher::select('user_id')->where('school_id', $request->school_id)->get();
        $users = User::whereIn('id', $teachers)->with('teacher.school')->orderBy('name', 'asc')->paginate(20);
        return response()->json($users);
    }

    // MOSTRAR COINCIDENCIA POR NOMBRE DE PROFESOR
    public function by_teacher(Request $request){
        $teachers = \DB::table('users')->select('teachers.id as teacher_id', 'users.name as name')
                    ->join('teachers', 'users.id', '=', 'teachers.user_id')
                    ->join('schools', 'teachers.school_id', '=', 'schools.id')
                    ->where('users.name','like','%'.$request->teacher.'%')
                    ->where('schools.id', $request->school_id)->get();
        return response()->json($teachers);
    }
}
