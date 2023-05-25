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
        $all_exams = Exam::where('group_id', auth()->user()->student->group->id)
                        ->with('advances')->get();
        $exams = [];
        $all_exams->map(function($exam) use (&$exams){
            $exams[] = [
                'id'            => $exam->id,
                'name'          => $exam->name, 
                'start_date'    => $exam->start_date, 
                'start_time'    => $exam->start_time, 
                'end_time'      => $exam->end_time,
                'duration'      => $exam->duration,
                'indications'   => $exam->indications,
                'advances_count' => $exam->advances()->where('student_id', auth()->user()->student->id)->count()
            ];
        });
        $exams = collect($exams);
        return view('users.student.home', compact('exams'));
    }

    public function results($exam_id){
        // $advances = Advance::where([
        //     'student_id' => auth()->user()->student->id, 
        //     'exam_id' => $exam_id
        // ])->with('level', 'instruction.topic', 'results.question', 'results.track')->get();

        $student = Student::find(auth()->user()->student->id);
        $exam = $student->exams()
                        ->where('exam_student.exam_id', $exam_id)
                        // ->with('group', 'teacher.user')
                        ->first();
        // $skills = \DB::table('categorie_exam_student')
        //                 ->join('categories', 'categorie_exam_student.categorie_id', '=', 'categories.id')
        //                 ->select('categories.categorie', 'points', 'total')
        //                 ->where([
        //                     'exam_id' => $exam->id, 
        //                     'student_id' => $student->id
        //                 ])->get();
        // return view('users.student.results', compact('exam', 'advances', 'skills'));
        return view('users.student.results', compact('exam', 'student'));
    }
}
