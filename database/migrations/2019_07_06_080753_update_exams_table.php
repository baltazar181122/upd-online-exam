<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->datetime('effectivity_date')->nullable()->change();
            $table->datetime('expiration_date')->nullable()->change();
            $table->date('result_date')->nullable()->change();
            $table->datetime('exam_start')->nullable()->change();
            $table->datetime('exam_end')->nullable()->change();
            $table->datetime('batch_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn('effectivity_date');
            $table->dropColumn('expiration_date');
            $table->dropColumn('result_date');
            $table->dropColumn('exam_start');
            $table->dropColumn('exam_end');
            $table->dropColumn('batch_id');

        });
    }
}
