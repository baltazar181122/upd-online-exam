<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $table = 'questions';
	
    protected $fillable = [
        'exam_id', 'user_id', 'questions', 'a', 'b', 'c', 'd', 'e', 'f', 'answer','points'
    ];
}
