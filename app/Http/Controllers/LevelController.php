<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Level;

class LevelController extends Controller
{
    // PAGINA PRINCIPAL, OBTENER TODOS LOS NIVELES
    public function home(){
        if(request()->ajax()){
            $levels = Level::orderBy('level', 'asc')->with('topics.instructions.categorie')->get();
            return response()->json($levels);
        } else {
            $levels = Level::orderBy('level', 'asc')->get();
            return view('reagents.home', compact('levels'));
        }
    }

    // OBTENER TODOS LOS NIVELES
    public function get_all(){
        $levels = Level::all();
        return response()->json($levels);
    }

    // OBTENER NOMBRE DEL NIVEL
    public function show(Request $request){
        $level = Level::find($request->level_id);
        return response()->json($level);
    }

    // CREAR NIVEL
    public function create(Request $request){
        // VALIDACIÓN DE DATOS
        $this->validate($request, [
            'level' => ['required', 'string', 'min:1', 'max:20']
        ]);

        \DB::beginTransaction();
        try {
            $level = Level::where('level', $request->level)->onlyTrashed()->first();
            if($level != NULL) $level->restore();
            else {
                $level = Level::create(['level' => $request->level]);
            }
            \DB::commit();
        } catch(Exception $e){
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }
        return response()->json($level);
    }

    // ACTUALIZAR NIVEL
    public function update(Request $request){
        \DB::beginTransaction();
        try {
            $level = Level::find($request->id);
            $level->update(['level' => $request->level]);
            \DB::commit();
        } catch(Exception $e){
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }
        return response()->json($level);
    }

    // BORRAR NIVEL
    public function delete(Request $request){
        \DB::beginTransaction();
        try {
            Level::whereId($request->level_id)->delete();
            \DB::commit();
        } catch(Exception $e){
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }
        return response()->json();
    }
}
