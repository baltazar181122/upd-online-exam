<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reviewer extends Model
{
    protected $fillable = [
        'file_name','batch_id', 'effectivity_date', 'expiration_date','reviewer_name'
    ];
}
