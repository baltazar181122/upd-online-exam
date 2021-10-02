<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImageExam extends Model
{
    protected $table = 'exam_images';

    protected $fillable = [
        'exam_id', 'image',
    ];
}
