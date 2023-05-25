<?php

namespace App\Exports;

use App\User;
use App\Student;
use App\Advance;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class ListStudentsExport implements FromView,WithColumnWidths
{

    protected $group_id;

    public function __construct($group_id)
    {
        $this->group_id = $group_id;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 50,
            'B' => 30,
            'C' => 20,
            'D' => 20,            
        ];
    } 

    public function view(): View
    {
        return view('exports.list_students', [
            'users' => $this->get_students()
        ]);
    }

    public function get_students(){
        // $ids_students = Student::where('group_id', $this->group_id)
        //     ->select('user_id')->pluck('user_id');
        // $users = User::whereIn('id', $ids_students)
        //             ->with('student.exams')
        //             ->orderBy('name', 'asc')->get();

        $students = Student::where('group_id', $this->group_id)
                            ->with('user')->get();
        
        $datos = collect();
        $students->map(function($student) use(&$datos){
            $advances = Advance::where('student_id', $student->id)
                    ->select('level_id', \DB::raw('SUM(correct) as correct'), \DB::raw('SUM(total) as total'))
                    ->groupBy('level_id')
                    ->with('level')
                    ->get();
            $skills = \DB::table('categorie_exam_student')
                    ->join('categories', 'categorie_exam_student.categorie_id', '=', 'categories.id')
                    ->select('categories.categorie as categorie', 'points', 'total')
                    ->where('student_id', $student->id)->get();
            $datos->push([
                'name' => $student->user->name,
                'email' => $student->user->email,
                'advances' => $advances,
                'skills' => $skills
            ]);
        });

        return $datos;
    }
}
