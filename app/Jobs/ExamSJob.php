<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExamSJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $questions;
    protected $exam;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($questions, $exam)
    {
        $this->questions = $questions;
        $this->exam = $exam;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \DB::beginTransaction();
        try {
            $this->questions->map(function($question){
                $this->exam->questions()->attach($question['id'], [
                    'instruction_id' => $question['instruction_id']
                ]);
            });

            $this->exam->questions->map(function($question){
                $q = \DB::table('exam_instruction')->where([
                    'exam_id' => $this->exam->id,
                    'instruction_id' => $question->instruction_id
                ])->first();
                if($q == null){
                    $this->exam->instructions()->attach($question->instruction_id, [
                        'level_id' => $question->instruction->topic->level_id
                    ]);
                }
            });
            
            \DB::commit();
        }  catch (Exception $e) {
            \DB::rollBack();
        }
    }
}
