<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateBatchExamColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('batch_exam', function (Blueprint $table) {
            $table->renameColumn('result_id', 'result_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('batch_exam', function (Blueprint $table) {
            $table->renameColumn('result_date', 'result_id');
        });
    }
}
