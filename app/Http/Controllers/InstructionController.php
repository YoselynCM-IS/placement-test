<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Instruction;
use App\Categorie;
use App\Level;

class InstructionController extends Controller
{
    // GUARDAR INSTRUCCIÓN
    public function create(Request $request){
        \DB::beginTransaction();
        try {
            $instruction = Instruction::create($request->all());
            \DB::commit();
        } catch(Exception $e){
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }
        return response()->json($instruction);
    }

    // ACTUALIZAR INSTRUCCIÓN
    public function update(Request $request){
        \DB::beginTransaction();
        try {
            $instruction = Instruction::find($request->id);
            $instruction->update([
                'instruction' => $request->instruction,
                'categorie_id' => $request->categorie_id, 
                'link' => $request->link
            ]);
            \DB::commit();
        } catch(Exception $e){
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }
        return response()->json($instruction);
    }

    // ELIMINAR INSTRUCCIÓN
    public function delete(Request $request){
        \DB::beginTransaction();
        try {
            Instruction::whereId($request->instruction_id)->delete();
            \DB::commit();
        } catch(Exception $e){
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }
        return response()->json();
    }

    // MOVER INSTRUCCIÓN
    public function move(Request $request){
        \DB::beginTransaction();
        try {
            $instruction = Instruction::find($request->instruction_id);
            $instruction->update([
                'topic_id' => $request->topic_id
            ]);
            \DB::commit();
        } catch(Exception $e){
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }
        return response()->json();
    }

    // OBTENER CATEGORIAS
    public function get_categories(){
        $categories = \DB::table('categories')->orderBy('categorie', 'asc')->get();
        return response()->json($categories);
    }

    // OBTENER INSTRUCCIONES POR CATEGORIAS SELECCIONADAS
    public function by_categories(Request $request){
        $instructions = Instruction::whereIn('categorie_id', $request->categories)->get();
        
        $levels = collect();
        $ls = Level::orderBy('level', 'asc')->with('topics')->get();
        $ls->map(function($l) use (&$levels, $instructions){
            $topics = collect();
            $ts = $l->topics->whereIn('id', $instructions->pluck('topic_id'));
            $ts->map(function($t) use(&$topics, $instructions){
                $ids_categories = $instructions->where('topic_id', $t->id)->unique('categorie_id')->pluck('categorie_id');
                $topics->push([
                    'id' => $t->id,
                    'level_id' => $t->level_id,
                    'topic' => $t->topic,
                    'skills' => Categorie::whereIn('id', $ids_categories)->get()
                ]);
            });
            $levels->push([
                'id' => $l->id,
                'level' => $l->level,
                'topics' => $topics
            ]);
        });
        return response()->json($levels);
    }

    // // MOSTRAR TODAS LAS INSTRUCCIONES
    // public function index(){
    //     $instructions = Instruction::with('topic.level')->get();
    //     return response()->json($instructions);
    // }
}
