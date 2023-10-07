<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Dropbox\Client;
use App\Events\ExamUpdate;
use App\Mail\AccessExam;
use App\Jobs\ExamSJob;
use App\Instruction;
use App\Categorie;
use Carbon\Carbon;
use App\Question;
use App\Teacher;
use App\Advance;
use App\Student;
use App\Answer;
use App\Result;
use App\Level;
use App\Track;
use App\User;
use App\Exam;
use PDF;

class ExamController extends Controller
{
    public function __construct() {
        // Necesitamos obtener una instancia de la clase Client la cual tiene algunos métodos
        // que serán necesarios.
        $this->dropbox = Storage::disk('dropbox')->getDriver()->getAdapter()->getClient();   
    }

    // CREAR EXAMENES
    public function create(Request $request){
        $error_range = (int)$request->error_range;
        $indications = $request->indications;
        $group_id = $request->group_id;
        // $topics_count = count($request->topics);
        // $topics = collect($request->topics);

        if(auth()->user()->role->role == 'admin'){
            $teacher_id = $request->teacher_id;
            $school_id = $request->school_id;
            $send = false;
        } else {
            $teacher_id = auth()->user()->teacher->id;
            $school_id  = auth()->user()->teacher->school_id;
            $send = true;
        }
        
        \DB::beginTransaction();
        try {
            if($group_id == null){
                $datos = [
                    'teacher_id'    => $teacher_id,
                    'school_id'     => $school_id, 
                    'name'          => Str::of($request->name)->upper(),
                    'indications'   => $indications,
                    'error_range'   => $error_range,
                    'send'          => $send
                ];
            } else {
                $datos = [
                    'teacher_id'    => $teacher_id,
                    'school_id'     => $school_id, 
                    'name'          => Str::of($request->name)->upper(),
                    'indications'   => $indications,
                    'error_range'   => $error_range,
                    'send'          => $send,
                    'group_id'      => $group_id,
                    'start_date'    => $request->start_date, 
                    'start_time'    => $request->start_time, 
                    'end_time'      => $request->end_time, 
                    'duration'      => $request->duration,
                ];
            }

            $exam = Exam::create($datos);

            \DB::commit();
        }  catch (Exception $e) {
            \DB::rollBack();
        }

        return response()->json($exam);
    }

    // GUARDAR TEMAS
    public function save_topics(Request $request){
        $categories = collect($request->categories);
        $topics = collect($request->topics);
        $exam = Exam::find($request->id);

        \DB::beginTransaction();
        try {
            $categories->map(function($categorie) use(&$a, $exam){
                \DB::table('categorie_exam')->insert([
                    'categorie_id' => $categorie,
                    'exam_id' => $exam->id
                ]);
            });

            $topics->map(function($topic) use(&$exam){
                $exam->topics()->attach($topic['id']);
            });
            \DB::commit();
        }  catch (Exception $e) {
            \DB::rollBack();
        }

        return response()->json($exam);
    }

    // ACTUALIZAR TEMAS GUARDADOS
    public function update_topics(Request $request){
        $topics = collect($request->topics);
        $exam = Exam::find($request->id);

        \DB::beginTransaction();
        try {

            // $topics->map(function($topic) use(&$exam){
            //     $exam->topics()->attach($topic['id']);
            // });
            \DB::commit();
        }  catch (Exception $e) {
            \DB::rollBack();
        }

        return response()->json($exam);
    }

    // OBTENER REACTIVOS ALEATORIOS
    public function random_instructions($level_id, $exam){
        $instructions = \DB::table('instructions')
                ->select('instructions.id as instruction_id', 'levels.id as level_id')
                ->join('topics', 'instructions.topic_id', '=', 'topics.id')
                ->join('levels', 'topics.level_id', '=', 'levels.id')
                ->where('levels.id', $level_id)
                ->inRandomOrder()->limit($exam->number_reagents)
                ->get();

        $this->attach_instructions($instructions, $exam);
    }

    public function choose_instructions($topics, $exam){
        foreach ($topics as $topic) {
            $instructions = \DB::table('instructions')
                    ->select('instructions.id as instruction_id', 'levels.id as level_id')
                    ->join('topics', 'instructions.topic_id', '=', 'topics.id')
                    ->join('levels', 'topics.level_id', '=', 'levels.id')
                    ->where('topics.id', $topic['id'])
                    ->inRandomOrder()->limit($exam->number_reagents)
                    ->get();

            $this->attach_instructions($instructions, $exam);
        }
    }

    // CONVERTIR PARA INSERTAR EN ATTACH
    public function attach_instructions($instructions, $exam){
        foreach ($instructions as $instruction) {
            $exam->instructions()->attach($instruction->instruction_id, [
                'level_id' => $instruction->level_id
            ]);
        }
    }

    // MOSTRAR POR ESCUELA
    public function by_school(Request $request){
        $exams = Exam::where('school_id', $request->school_id)
                    ->with('teacher.user') 
                    ->orderBy('created_at', 'desc')
                    ->paginate(20);
        return response()->json($exams);
    }

    // ACTUALIZAR EXAMEN
    public function update(Request $request){
        \DB::beginTransaction();
        try {
            $exam = Exam::find($request->id);
            $exam->update([
                'group_id' => $request->group_id, 
                'name' => Str::of($request->name)->upper(), 
                'start_date' => $request->start_date, 
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'duration' => (int)$request->duration, 
                'error_range' => (int)$request->error_range,
                'indications' => $request->indications
            ]);
            \DB::commit();
        }  catch (Exception $e) {
            \DB::rollBack();
        }

        // event(new ExamUpdate($exam));
        // broadcast(new ExamUpdate($exam))->toOthers();
        // ExamUpdate::dispatch($exam);
        // broadcast(new ExamUpdate($exam))->toOthers();
        return response()->json();
    }

    // MOSTRAR DETALLES DEL EXAMEN
    public function show(Request $request){
        $exam = Exam::whereId($request->exam_id)
                ->with(
                    'group', 
                    'teacher.user',
                    'instructions.topic',
                    'questions.answers',
                    'topics'
                )->first();
        return response()->json($exam);
    }

    // REVISAR SI EL EXAMEN NO HA SIDO CREADO
    public function enter_exam(Request $request){
        $exam = Exam::find($request->exam_id);
        $status = $exam->students()->where('student_id', auth()->user()->student->id)->count();
        return response()->json($status);
    }

    // CREAR EXAMEN PARA STUDIANTE Y OBTENER EL NIVEL 1 DEL EXAMEN
    public function start_exam(Request $request){
        $exam = Exam::find($request->id);
        $datos = $this->get_instQuestions($exam, 1);

        \DB::beginTransaction();
        try {
            $fecha_actual = Carbon::now();
            $exam->students()->attach(auth()->user()->student->id,[
                'level_id'      => 1,
                'start_time'    => $fecha_actual->format('h:i:s'),
                'created_at'    => $fecha_actual
            ]);

            $skills = $this->array_ces($exam->id);
            $c_e_s = \DB::table('categorie_exam_student')->insert($skills);
        \DB::commit();
        }  catch (Exception $e) {
            \DB::rollBack();
        }

        return response()->json($datos);
    }

    public function array_ces($exam_id){
        $categories = \DB::table('categorie_exam')->where([
            'exam_id' => $exam_id
        ])->get();

        $array_datos = array();
        $categories->map(function($categorie) use(&$array_datos, $exam_id){
            array_push($array_datos, [
                'categorie_id' => $categorie->categorie_id, 
                'exam_id' => $exam_id, 
                'student_id' => auth()->user()->student->id, 
                'points' => 0,
                'total' => 0
            ]);
        });
        return $array_datos;
    }

    // OBTENER INSTRUCCIONES
    public function get_instQuestions($exam, $level){
        $ids_is = \DB::table('exam_instruction')
                            ->select('instruction_id')
                            ->where('exam_id', $exam->id)
                            ->where('level_id', $level)
                            ->pluck('instruction_id');
        $instructions = Instruction::whereIn('id', $ids_is)->get();
        $ids_qs = \DB::table('exam_question')
                            ->whereIn('instruction_id', $ids_is)
                            ->select('question_id')
                            ->where('exam_id', $exam->id)
                            ->pluck('question_id');
        
        $questions = Question::whereIn('id', $ids_qs)
                        ->with('answers')->get();
        $datos = [
            'instructions' => $instructions,
            'questions' => $questions,
        ];
        return $datos;
    }

    // REVISAR PREGUNTAS DEL NIVEL
    public function check_level(Request $request){
        $exam = Exam::find($request->exam_id);
        
        $n = $this->save_results($exam, $request);
        $level_id = $n['level_id'];
        $number_questions = $n['number_questions'];
        $number_correct = $n['number_correct'];

        // 1 => Continuar, 2 => Finalizado
        $respuesta = [
            'status' => 2,
            'datos' => null
        ];

        
        if(($number_correct == $number_questions) || ($number_correct >= ($number_questions - $exam->error_range))){
            $number_levels = \DB::table('exam_instruction')->where('exam_id', $exam->id)->max('level_id');
            $number_level = $level_id + 1;
            if($number_level <= $number_levels){
                $datos = $this->get_instQuestions($exam, $number_level);
                $respuesta = [
                    'status' => 1,
                    'datos' => $datos
                ];
            }
        }

        // FINALIZAR EXAMEN
        if($respuesta['status'] == 2){
            if($level_id > 1 && $number_correct < ($number_questions - $exam->error_range))
                $level_id = $level_id - 1;
            $this->insert_pivot_es($exam, $level_id);

            $advances = Advance::where([
                'student_id' => auth()->user()->student->id, 
                'exam_id' => $exam->id
            ])->get();

            $respuesta = [
                'status' => 2,
                'datos' => $advances
            ];
        }

        return response()->json($respuesta);
    }

    // REVISAR RESPUESTAS
    public function save_results($exam, $request){
        \DB::beginTransaction();
        try {
            $level_id = null;
            $number_questions = 0;
            $number_correct = 0;

            $_instructions = collect($request->instructions);
            $ids = array();
            $_instructions->map(function ($instruction) use(&$ids){
                array_push($ids, $instruction['id']);
            });

            $instructions = Instruction::whereIn('id', $ids)
                                ->where('categorie_id', '!=', 3)->get();
            
            $list_advance = [];
            $instructions->map(function ($instruction) use(&$list_advance, $exam, &$level_id){
                $hora_actual = Carbon::now();
                $level_id = $instruction->topic->level_id;

                $list_advance[] = [
                    'student_id' => auth()->user()->student->id, 
                    'exam_id' => $exam->id, 
                    'level_id' => $level_id, 
                    'instruction_id' => $instruction->id, 
                    'created_at' => $hora_actual,
                    'updated_at' => $hora_actual
                ];
            });
            
            Advance::insert($list_advance);

            $questions = collect($request->questions);
            $list_results = [];
            $questions->map(function ($question) use(&$list_results, $exam, &$number_correct) {
                $write = $question['answer'];
                $value = 'incorrect';
                $points = 0;

                $answer = Answer::where([
                    'question_id' => $question['id'],
                    'answer' => $write
                ])->first();

                if($answer != null) {
                    // ABIERTA
                    if($question['type_id'] == 1){
                        $value = 'correct';
                        $points = 1;
                        $number_correct++;
                    }
                    // SELECT
                    if($question['type_id'] == 2 || $question['type_id'] == 3){
                        if($answer->value == 'correct') {
                            $value = 'correct';
                            $points = 1;
                            $number_correct++;
                        }
                    }
                }

                $advance = Advance::where([
                    'instruction_id' => $question['instruction_id'],
                    'student_id' => auth()->user()->student->id,
                    'exam_id' => $exam->id
                ])->first();

                $result = Result::where([
                    'advance_id' => $advance->id, 
                    'question_id' => $question['id'],
                ])->first();

                if($result == null){
                    $list_results[] = [
                        'advance_id' => $advance->id,
                        'question_id' => $question['id'], 
                        'answer' => $write, 
                        'value' => $value,
                        'points' => $points
                    ];
                }
            });

            Result::insert($list_results);

            $advances = Advance::where([
                    'student_id' => auth()->user()->student->id,
                    'exam_id' => $exam->id,
                    'level_id' => $level_id
                ])->with('results')->get();
            $advances->map(function($advance){
                $total = 0;
                $correct = 0;
                $advance->results->map(function($result) use(&$correct, &$total){
                    $total++;
                    if($result->value == 'correct') $correct++;
                });
                $advance->update([ 'total' => $total, 'correct' => $correct ]);

                // GRAMMAR, LISTENING, SPEAKING (AQUI NO SE EVALUA), READING (NO DISPONIBLE AUN)
                $this->set_ces_points($advance->instruction->categorie_id, $advance->exam_id, auth()->user()->student->id, $correct, $total);
            });
            \DB::commit();
        }  catch (Exception $e) {
            \DB::rollBack();
        }
        
        return [
            'level_id' => $level_id,
            'number_questions' => $questions->count(),
            'number_correct' => $number_correct
        ];
    }

    // OBTENER CATEGORIA POR ALUMNO Y EXAMEN
    public function set_ces_points($categorie_id, $exam_id, $student_id, $points, $total){
        $search = [
            'categorie_id' => $categorie_id, 
            'exam_id' => $exam_id, 
            'student_id' => $student_id
        ];
        $tm = $total;
        if($categorie_id == 3) $tm = $total * 100;
        
        \DB::table('categorie_exam_student')->where($search)->increment('points', $points);
        \DB::table('categorie_exam_student')->where($search)->increment('total', $tm);
    }

    // TIEMPO AGOTADO (GUARDAR AVANCE)
    public function time_out(Request $request){
        $exam = Exam::find($request->exam_id);

        $n = $this->save_results($exam, $request);
        $level_id = $n['level_id'];
        $number_questions = $n['number_questions'];
        $number_correct = $n['number_correct'];

        if($level_id > 1 && $number_correct < ($number_questions - $exam->error_range))
            $level_id = $level_id - 1;

        $this->insert_pivot_es($exam, $level_id);

        $advances = Advance::where([
            'student_id' => auth()->user()->student->id, 
            'exam_id' => $exam->id
        ])->get();
        return response()->json($advances);
    }

    // OBTENER DETALLES DEL EXAMEN
    public function get_advances(Request $request){
        $advances = Advance::where([
            'student_id' => $request->student_id, 
            'exam_id' => $request->exam_id
        ])->with('level', 'instruction.topic', 'results.question', 'results.track')->get();

        $student = Student::find($request->student_id);
        $exam = $student->exams()
                        ->where('exam_student.exam_id', $request->exam_id)
                        ->with('group', 'teacher.user')->first();
        $skills = \DB::table('categorie_exam_student')
                    ->join('categories', 'categorie_exam_student.categorie_id', '=', 'categories.id')
                    ->select('categories.categorie', 'points', 'total')
                    ->where([
                        'exam_id' => $exam->id, 
                        'student_id' => $student->id
                    ])->get();
        $data = [
            'advances' => $advances,
            'exam' => $exam,
            'skills' => $skills
        ];
        return response()->json($data);
    }

    // REVISAR SI EL EXAMEN TIENE ALUMNOS ASIGNADOS
    public function check_students(Request $request){
        $exam = Exam::find($request->exam_id);
        $count_students = $exam->students()->count();
        return response()->json($count_students);
    }

    // ELIMINAR EXAMEN
    public function delete(Request $request){
        $exam = Exam::find($request->exam_id);

        \DB::beginTransaction();
        try {
            if($exam->topics->count() > 0) {
                $exam->topics()->detach();
                \DB::table('categorie_exam')->where([
                    'exam_id' => $exam->id
                ])->delete();
            }
            if($exam->questions->count() > 0){
                $exam->instructions()->detach();
                $exam->questions()->detach();
            }
            $exam->delete();
            \DB::commit();
        }  catch (Exception $e) {
            \DB::rollBack();
        }
        return response()->json();
    }

    // ENVIAR EXAMEN AL PROFESOR
    public function send(Request $request){
        $exam = Exam::find($request->exam_id);

        \DB::beginTransaction();
        try {
            $exam->update(['send' => 1]);
            \DB::commit();
        }  catch (Exception $e) {
            \DB::rollBack();
        }
        return response()->json();
    }

    // MOSTRAR POR PROFESOR
    public function by_teacher(Request $request){
        $exams = Exam::where('teacher_id', $request->teacher_id)
                    ->with('teacher.user') 
                    ->orderBy('created_at', 'desc')
                    ->paginate(20);
        return response()->json($exams);
    }

    public function insert_pivot_es($exam, $level_id){
        $relation = $exam->students()
                        ->where('exam_student.student_id', auth()->user()->student->id)
                        ->first();
        $start_time = new Carbon($relation->pivot->start_time);
        $end_time = Carbon::now()->format('h:i:s');

        $advances = Advance::where([
            'student_id' => auth()->user()->student->id,
            'exam_id' => $exam->id,
            'level_id' => $level_id
        ])->get();
        
        $correct = $advances->sum('correct');
        $total = $advances->sum('total');

        $exam->students()->updateExistingPivot(auth()->user()->student->id, [
            'level_id'      => $level_id,
            'duration'      => $start_time->diffInMinutes($end_time),
            'end_time'      => $end_time,
            'correct'       => $correct,
            'total'         => $total
        ]);
    }

    // REVISAR SKILLS
    public function check_skills(Request $request){
        $questions = collect($request->questions);

        $levels = \DB::table('levels')->get();
        $inst_skills = collect();
        foreach ($levels as $level) {
            $dato = [ 'level_id' => $level->id, 'instructions' => collect() ];
            $inst_skills->push($dato);
        }
        
        foreach ($questions as $instruction) {
            $i = $inst_skills->firstWhere('level_id', $instruction['level_id']);
            if(!$i['instructions']->contains('categorie_id', $instruction['categorie_id']))
                $i['instructions']->push(['categorie_id' => $instruction['categorie_id']]);
        }

        $categories = \DB::table('categorie_exam')->where([
            'exam_id' => $request->exam_id
        ])->get();

        foreach ($inst_skills as $cs) {
            $c = $cs['instructions'];
            if($c->count() == $categories->count()){
                foreach ($categories as $categorie) {
                    if(!$c->contains('categorie_id', $categorie->categorie_id)) 
                        return response()->json(false);
                }
            } else {
                return response()->json(false);
            }
        }
        
        return response()->json(true);
    }

    // GUARDAR PREGUNTAS
    public function save_questions(Request $request){
        $questions = collect($request->questions);
        $exam = Exam::find($request->exam_id);
        \DB::beginTransaction();
        try {
            $questions->map(function($question) use (&$exam){
                $exam->questions()->attach($question['id'], [
                    'instruction_id' => $question['instruction_id']
                ]);
            });

            $exam->questions->map(function($question) use (&$exam){
                $q = \DB::table('exam_instruction')->where([
                    'exam_id' => $exam->id,
                    'instruction_id' => $question->instruction_id
                ])->first();
                if($q == null){
                    $exam->instructions()->attach($question->instruction_id, [
                        'level_id' => $question->instruction->topic->level_id
                    ]);
                }
            });
            
            \DB::commit();
        }  catch (Exception $e) {
            \DB::rollBack();
        }
        // ExamSJob::dispatch($questions, $exam);
        return response()->json();
    }

    // OBTENER TEMAS DEL EXAMEN
    public function get_topics(Request $request){
        $exam = Exam::whereId($request->exam_id)
                ->with('topics.level', 'topics.instructions.questions', 'topics.instructions.categorie')->first();
        return response()->json($exam);
    }

    // NOTIFICACIÓN DE EXAMEN A CADA ALUMNO
    public function notification(Request $request){
        $exam = Exam::find($request->id);
        $students = Student::where('group_id', $exam->group_id)->get();
        
        $respuesta = false;
        if($students->count() > 0) {
            $students->map(function($student) use($exam){
                Mail::to($student->user->email)->send(new AccessExam($exam, $student->user));
            });
            $respuesta = true;
        }
        
        return response()->json($respuesta);
    }

    // IR A PANTALLA PARA GRABAR AUDIO
    public function go_record($exam_id, $question_id){
        $question = Question::find($question_id);
        return view('exams.questions.record-audio', compact('exam_id', 'question'));
    }

    // METODO DE PREUBA PARA GUARDAR EL AUDIO
    public function save_record(Request $request){
        $question = Question::find($request->question_id);

        $student_id = auth()->user()->student->id;
        $exam_id = (int)$request->exam_id;
        $level_id = $question->instruction->topic->level_id; 
        $instruction_id = $question->instruction_id;

        \DB::beginTransaction();
        try {
            $advance = Advance::where([
                'student_id' => $student_id, 
                'exam_id' => $exam_id, 
                'level_id' => $level_id, 
                'instruction_id' => $instruction_id
            ])->first();

            if($advance == null){
                $advance = Advance::create([
                    'student_id' => $student_id, 
                    'exam_id' => $exam_id, 
                    'level_id' => $level_id, 
                    'instruction_id' => $instruction_id,
                    'correct' => 0, 
                    'total' => 1
                ]);
            } else {
                $result = Result::where([
                    'advance_id' => $advance->id, 
                    'question_id' => $question->id,
                ])->first();

                if($result == null) {
                    $advance->update([
                        'total' => $advance->total + 1
                    ]);
                } else {
                    return response()->json(1);
                }

            }
            
            $result = Result::create([
                'advance_id' => $advance->id, 
                'question_id' => $question->id, 
                'answer' => 'recording', 
                'value' => 'pending'
            ]);

            $size = $_FILES['audio_data']['size']; //the size in bytes
            $input = $_FILES['audio_data']['tmp_name']; //temporary name that PHP gave to the uploaded file
            
            $user = auth()->user();
            $name_student = Str::slug($user->name, '-');
            $name_file = "id-".$user->id."_".$name_student."_".time().".wav";

            Storage::disk('dropbox')->putFileAs(
                '/', $input, $name_file
            );

            $response = $this->dropbox->createSharedLinkWithSettings(
                '/'.$name_file, 
                ["requested_visibility" => "public"]
            );

            Track::create([
                'result_id' => $result->id,
                'name' => $response['name'],
                'extension' => 'wav',
                'size' => $response['size'],
                'public_url' => $response['url']
            ]);

            \DB::commit();
        }  catch (Exception $e) {
            \DB::rollBack();
        }
        
        return response()->json(2);
    }

    // GUARDAR CALIFICACIÓN
    public function save_points(Request $request){
        $this->validate($request, [
            'points' => ['required', 'numeric', 'min:0', 'max:100']
        ]);
        \DB::beginTransaction();
        try {
            $points = (int) $request->points;
            $result = Result::find($request->result_id);
            $result->update([
                'value' => 'correct', 'points' => $points
            ]);
            $advance = Advance::find($result->advance->id);
            $advance->update([
                'correct' => $advance->correct + 1
            ]);

            // GRAMMAR, LISTENING, SPEAKING (AQUI NO SE EVALUA), READING (NO DISPONIBLE AUN)
            $this->set_ces_points($advance->instruction->categorie_id, $advance->exam_id, $advance->student_id, $points, 0);
                
            \DB::commit();
        }  catch (Exception $e) {
            \DB::rollBack();
        }
        return response()->json([
            'points' => $result->points,
            'correct' => $advance->correct
        ]);
    }

    // OBTENER RESULTADOS DETALLADO
    public function get_results(Request $request){
        $advances = Advance::where([
                        'exam_id' => $request->exam_id,
                        'student_id' => $request->student_id, 
                    ])
                    ->select('level_id', \DB::raw('SUM(correct) as correct'), \DB::raw('SUM(total) as total'))
                    ->groupBy('level_id')
                    ->with('level')
                    ->get();
        
        return response()->json($advances);
    }

    // DESCARGAR EXAMEN
    public function download($exam_id, $skills){
        $categories = [];
        for ($i=1; $i <= 4; $i++) { 
            if($this->get_categorie_id($skills, ''.$i.'')){
                $categories[] = $i;
            }
        }

        $exam = Exam::find($exam_id);
        
        // $exam_is = \DB::table('exam_instruction')->where('exam_id', $exam_id)->pluck('instruction_id');
        // $instructions = Instruction::whereIn('categorie_id', $categories)->whereIn('id', $exam_is)->skip(180)->take(45)->get();
        $data['exam'] = $exam;
        $data['instructions'] = $exam->instructions->whereIn('categorie_id', $categories);
        $pdf = PDF::loadView('exports.exam_pdf', $data); 
        return $pdf->download('examen.pdf');
    }

    public function get_categorie_id($skills, $id){
        if(strpos($skills, $id) !== false)
            return true;
        return false;
    }
    
}
