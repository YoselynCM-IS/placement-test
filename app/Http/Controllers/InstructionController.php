<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Instruction;

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

    // // MOSTRAR TODAS LAS INSTRUCCIONES
    // public function index(){
    //     $instructions = Instruction::with('topic.level')->get();
    //     return response()->json($instructions);
    // }
}
