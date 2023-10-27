<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Answer;
use App\Topic;

class AnswerController extends Controller
{
    // GUARDAR RESPUESTAS
    public function create(Request $request){
        \DB::beginTransaction();
        try {
            if($request->type_id == 1) {
                Answer::create([
                    'question_id' => $request->id,
                    'answer' => $request->answer,
                    'value' => 'correct'
                ]);
            }
            if($request->type_id == 4) {
                Answer::create([
                    'question_id' => $request->id,
                    'answer' => $request->answer
                ]);
            }
            if($request->type_id == 2 || $request->type_id == 3){
                $answers = $request->answers;
                foreach ($answers as $answer) {
                    if($answer['answer'] != ''){
                        $value = 'incorrect';
                        if($answer['value'] == $request->answer) $value = 'correct';
                        Answer::create([
                            'question_id' => $request->id,
                            'answer' => $answer['answer'],
                            'value' => $value
                        ]);
                    }
                }
            }

            \DB::commit();
        } catch(Exception $e){
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }

        $topic = Topic::whereId($request->topic_id)->with('instructions.questions.answers')->first();

        return response()->json($topic);
    }

    public function update(Request $request){
        \DB::beginTransaction();
        try {
            $answers = collect($request->answers);

            if($request->type_id == 1 || $request->type_id == 4) {
                $answers->map(function($a) use($request){
                    Answer::whereId($a['id'])->update([
                        'answer' => $request->answer
                    ]);
                });
            }
            if($request->type_id == 2 || $request->type_id == 3){
                $answers->map(function($a) use($request){
                    if($a['answer'] != ''){
                        $value = 'incorrect';
                        if($a['value'] == $request->answer) $value = 'correct';
                        
                        Answer::whereId($a['id'])->update([
                            'question_id' => $request->id,
                            'answer' => $a['answer'],
                            'value' => $value
                        ]);
                    }
                });
            }

            \DB::commit();
        } catch(Exception $e){
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }

        $topic = Topic::whereId($request->topic_id)->with('instructions.questions.answers')->first();

        return response()->json($topic);
    }
}
