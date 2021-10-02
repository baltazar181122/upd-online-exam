<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BatchExam extends Model
{
    protected $table = 'batch_exam';
	
    protected $fillable = [
        'batch_id','exam_id', 'user_id', 'result_date', 'exam_start', 'exam_end','timer',
    ];
}
