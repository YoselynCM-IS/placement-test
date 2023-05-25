<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Group;
use App\Exam;
use App\User;

class TeacherController extends Controller
{
    // PAGINA PRINCIPAL 
    public function home(){
        return view('users.teacher.home');
    }

    // EXAMENES DEL PROFESOR
    public function exams(){
        $exams = Exam::where('teacher_id', auth()->user()->teacher->id)
                    ->where('send', true)
                    ->with('categories')
                    ->orderBy('created_at', 'asc')->get();
        return view('users.teacher.exams', compact('exams'));
    }

    // MOSTRAR GRUPOS
    public function groups() {
        $groups = $this->get_groups();
        return view('users.teacher.groups', compact('groups'));
    }

    // MOSTRAR RESULTADOS POR ALUMNO
    public function results(){
        $groups = $this->get_groups();
        return view('users.teacher.results', compact('groups'));
    }

    // OBTENER GRUPOS ASIGNADOS AL PROFESOR
    public function get_groups(){
        return Group::where('teacher_id', auth()->user()->teacher->id)
                    ->withCount('students')
                    ->orderBy('group', 'asc')->get();
    }
}
