<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use App\Exports\ListStudentsExport;
use App\Exports\TemplateExport;
use App\Imports\StudentsImport;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Mail\SendData;
use App\Student;
use App\Group;
use App\User;
use Excel;

class GroupController extends Controller
{
    // MOSTRAR POR USUARIO (PAGINADO)
    public function by_user(Request $request){
        $groups = Group::where('teacher_id', $request->teacher_id)
                    ->withCount('students')
                    ->orderBy('group', 'asc')->paginate(20);
        return response()->json($groups); 
    }

    // MOSTRAR POR USUARIO
    public function s_by_user(Request $request){
        $groups = Group::where('teacher_id', auth()->user()->teacher->id)
                    ->orderBy('group', 'asc')->get();
        return response()->json($groups); 
    }

    // CREAR GRUPO
    public function create(Request $request){
        // VALIDACIÓN DE DATOS
        $this->validate($request, [
            'group' => ['required', 'string', 'min:2']
        ]);

        \DB::beginTransaction();
        try {
            $user = User::find($request->user_id);
            Group::create([
                'school_id'     => $user->teacher->school_id,
                'teacher_id'    => $user->teacher->id, 
                'group'         => Str::of($request->group)->upper()
            ]);
            \DB::commit();
        } catch(Exception $e){
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }
        return response()->json();
    }

    public function update(Request $request){
        // VALIDACIÓN DE DATOS
        $this->validate($request, [
            'group' => ['required', 'string', 'min:2']
        ]);

        \DB::beginTransaction();
        try {
            Group::whereId($request->id)->update([
                'group' => Str::of($request->group)->upper()
            ]);
            \DB::commit();
        } catch(Exception $e){
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }
        return response()->json();
    }

    public function download_template(){
        return Excel::download(new TemplateExport, 'template.xlsx');
    }

    public function upload_students(Request $request){
        $students = Excel::toArray(new StudentsImport, request()->file('file'));
        
        unset($students[0][0]);
        
        $count = 0;
        foreach ($students[0] as $key => $student) {
            if($count < 50){
                $this->store_user_student($student[0], $student[1], $request->group_id);
            }
            $count++;
        }
        return response()->json();
    }

    public function store_user_student($name, $email, $group_id){
        $password = Str::random(6);
            
        $user = User::firstOrCreate([
            'role_id'       => 3,
            'name'          => Str::of($name)->upper(),
            'email'         => trim($email),
            'password'      => bcrypt($password),
            'view_password' => $password
        ]);

        $student = Student::create([
            'user_id' => $user->id, 
            'group_id' => $group_id
        ]);
    }

    public function list_students(Request $request){
        $students = Student::where('group_id', $request->group_id)->with('user')->paginate(20);
        return response()->json($students);
    }

    public function send_access(Request $request){
        $selected = collect($request->selected);

        $selected->map(function($select){
            $user = User::find($select['user']['id']);
            Mail::to($user->email)->send(new SendData($user));
            $user->update([ 'send' => 1 ]);
        });

        return response()->json();
    }

    // AGREGAR STUDENT
    public function add_student(Request $request){
        $this->validate_student($request);
        \DB::beginTransaction();
        try {
            $this->store_user_student($request->name, $request->email, $request->group_id);
            \DB::commit();
        } catch(Exception $e){
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }
        return response()->json();
    }

    // ACTUALIZAR STUDENT
    public function update_student(Request $request){
        $this->validate_student($request);
        \DB::beginTransaction();
        try {
            User::whereId($request->id)->update([
                'name' => Str::of($request->name)->upper(),
                'email' => $request->email
            ]);

            \DB::commit();
        } catch(Exception $e){
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }
        return response()->json();
    }

    public function validate_student($request){
        // VALIDACIÓN DE DATOS
        $this->validate($request, [
            'name' => ['required', 'string', 'min:5'],
            'email' => ['required', 'email', 'max:60', 'unique:users']
        ]);
    }

    // REVISAR SI EL ESTUDIANTE TIENE EXAMENES ASIGNADOS
    public function student_assignments(Request $request){
        $student = Student::where('user_id', $request->user_id)
                    ->with('exams')->first();
        return response()->json($student);
    }

    // ELIMINAR ALUMNO
    public function delete_student(Request $request){
        \DB::beginTransaction();
        try {
            Student::where('user_id', $request->user_id)->delete();
            User::whereId($request->user_id)->delete();
            \DB::commit();
        } catch(Exception $e){
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }
        return response()->json();
    }

    // REVISAR SI EL GRUPO TIENE ALGUN EXAMEN ASIGNADO
    public function exam_assignments(Request $request){
        $group = Group::whereId($request->group_id)
                        ->withCount('exams', 'students')->first();
        return response()->json($group);
    }

    // ELIMINAR GRUPO
    public function delete(Request $request){
        \DB::beginTransaction();
        try {
            Group::whereId($request->group_id)->delete();
            \DB::commit();
        } catch(Exception $e){
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }
        return response()->json();
    }

    // OBTENER AVANCES DE ESTUDIANTES
    public function list_avances(Request $request){
        $students = Student::where('group_id', $request->group_id)
                    ->with('user','exams')->paginate(20);
        return response()->json($students);
    }

    // DESCARGAR LISTA DE ALUMNOS CON CALIFICACIONES
    public function download_list($group_id){
        return Excel::download(new ListStudentsExport($group_id), 'lista-alumnos.xlsx');
    }
}
