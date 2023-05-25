<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('teacher_id');
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('group_id')->nullable();
            $table->string('name');
            $table->date('start_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->integer('duration')->default(40);
            $table->integer('number_reagents')->default(0);
            $table->integer('error_range')->default(2);
            $table->text('indications')->nullable();
            $table->boolean('send')->default(0);
            $table->foreign('teacher_id')->references('id')->on('teachers');
            $table->foreign('school_id')->references('id')->on('schools');
            $table->foreign('group_id')->references('id')->on('groups');
            $table->softDeletes();
            $table->timestamps();
        }); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exams');
    }
}
