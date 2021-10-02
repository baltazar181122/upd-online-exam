<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImageAnswer extends Model
{
    protected $table = 'image_answer';
    
    protected $fillable = [
        'exam_id',          
        'sequence',          
        'points',            
        'sequence_name'     
    ];
}
