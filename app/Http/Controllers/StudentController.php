<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Advance;
use App\Student;
use App\Exam;

use Carbon\Carbon;

class StudentController extends Controller
{
    // PAGINA PRINCIPAL
    public function home(){
        $exams = Exam::where('group_id', auth()->user()->student->group->id)->get();
        return view('users.student.home', compact('exams'));
    }

    // OBTENER EL EXAMEN DEL ALUMNO
    public function exam($exam_id){
        $exam = Exam::whereId($exam_id)->with('group', 'teacher.user')->first();

        if($exam->students()->where('student_id', auth()->user()->student->id)->count() > 0){
            $student = Student::find(auth()->user()->student->id);
            $exam = $student->exams()
                            ->where('exam_student.exam_id', $exam_id)
                            ->first();
            return view('users.student.results', compact('exam', 'student'));
        } else {
            $today = Carbon::now();
            $number_days = $today->diffInDays($exam->start_date);
            if ($number_days > 1 && $exam->start_date < $today->format('Y-m-d'))
                $information = $this->set_information('El examen fue hace '.$number_days.' día(s).', false);
            if ($number_days == 1)
                $information = $this->set_information('El examen fue el día de ayer', false);
            if ($number_days == 0 && $exam->start_date == $today->format('Y-m-d'))
                $information = $this->set_information('El examen es el día de hoy.', true);
            if ($number_days == 0 && $exam->start_date > $today->format('Y-m-d'))
                $information = $this->set_information('El examen es el día de mañana.', true);
            if ($number_days > 1 && $exam->start_date > $today->format('Y-m-d')) 
                $information = $this->set_information('El examen sera en '.$number_days.' día(s).', true);
            
            return view('users.student.exam', compact('exam', 'information'));
        }
    }

    public function set_information($message, $state){
        return collect([ 'message' => $message, 'state' => $state ]);
    }

    public function results($exam_id){
        // $advances = Advance::where([
        //     'student_id' => auth()->user()->student->id, 
        //     'exam_id' => $exam_id
        // ])->with('level', 'instruction.topic', 'results.question', 'results.track')->get();
        // $skills = \DB::table('categorie_exam_student')
        //                 ->join('categories', 'categorie_exam_student.categorie_id', '=', 'categories.id')
        //                 ->select('categories.categorie', 'points', 'total')
        //                 ->where([
        //                     'exam_id' => $exam->id, 
        //                     'student_id' => $student->id
        //                 ])->get();
        // return view('users.student.results', compact('exam', 'advances', 'skills'));
        $student = Student::find(auth()->user()->student->id);
        $exam = $student->exams()
                        ->where('exam_student.exam_id', $exam_id)
                        ->first();
        return view('users.student.results', compact('exam', 'student'));
    }
}
