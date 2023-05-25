<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Topic;

class TopicController extends Controller
{
    public function index(){
        $topics = Topic::orderBy('level_id')->with('level')->get();
        return response()->json($topics);
    }

    // MOSTRAR TODOS LOS TEMAS POR NIVEL
    public function by_level(Request $request){
        $topics = Topic::where('level_id', $request->level_id)->orderBy('topic', 'asc')->get();
        return response()->json($topics);
    }

    // MOSTRAR POR ID
    public function show(Request $request){
        $topic = Topic::whereId($request->topic_id)->with('instructions.questions.answers')->first();
        return response()->json($topic);
    }

    // GUARDAR TOPIC
    public function create(Request $request){
        \DB::beginTransaction();
        try {
            $topic = Topic::create($request->all());
            \DB::commit();
        } catch(Exception $e){
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }
        return response()->json($topic);
    }

    // ACTUALIZAR TOPIC
    public function update(Request $request){
        \DB::beginTransaction();
        try {
            $topic = Topic::find($request->id);
            $topic->update(['topic' => $request->topic]);
            \DB::commit();
        } catch(Exception $e){
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }
        return response()->json($topic);
    }

    // VERIFICAR SI HAY INSTRUCCIONES ASIGNADAS
    public function instruction_assignments(Request $request){
        $topic = Topic::whereId($request->topic_id)->withCount('instructions')->first();
        return response()->json($topic);
    }

    // ELIMINAR TEMA
    public function delete(Request $request){
        \DB::beginTransaction();
        try {
            Topic::whereId($request->topic_id)->delete();
            \DB::commit();
        } catch(Exception $e){
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }
        return response()->json();
    }
}
