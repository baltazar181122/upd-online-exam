<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBatchExamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('batch_exam', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('batch_id');
            $table->bigInteger('exam_id');
            $table->bigInteger('user_id');
            $table->date('result_id');
            $table->dateTime('exam_start');
            $table->dateTime('exam_end');
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
        Schema::dropIfExists('batch_exam');
    }
}
