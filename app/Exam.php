<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $table = 'exams';
	
    protected $fillable = [
        'title', 'effectivity_date', 'expiration_date', 'created_by', 'batch_id', 'result_date', 'exam_start', 'exam_end','type'
    ];

    public function exam(){
        return $this->belongsTo('App\Batch');
    }
}
