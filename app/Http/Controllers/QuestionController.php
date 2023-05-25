<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Question;
use App\Topic;

class QuestionController extends Controller
{
    // GUARDAR QUESTIONS
    public function create(Request $request){
        \DB::beginTransaction();
        try {
            $question = Question::create([
                'instruction_id' => $request->instruction_id,
                'type_id' => $request->type_id, 
                'question' => $request->question
            ]);
            \DB::commit();
        } catch(Exception $e){
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }
        return response()->json($question);
    }

    // ACTUALIZAR QUESTION
    public function update(Request $request){
        \DB::beginTransaction();
        try {
            $question = Question::find($request->id);
            $question->update([
                'type_id' => $request->type_id, 
                'question' => $request->question
            ]);
            \DB::commit();
        } catch(Exception $e){
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }

        return response()->json($question);
    }

    // ELIMINAR PREGUNTA
    public function delete(Request $request){
        \DB::beginTransaction();
        try {
            Question::whereId($request->question_id)->delete();
            \DB::commit();
        } catch(Exception $e){
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }
        return response()->json();
    }

    // OBTENER TIPOS DE PREGUNTA
    public function get_types(){
        $types = \DB::table('types')->orderBy('type', 'asc')->get();
        return response()->json($types);
    }

}
