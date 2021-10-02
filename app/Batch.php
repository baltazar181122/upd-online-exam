<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
	protected $table = 'batch';
	
    protected $fillable = [
        'batch_code','batch_name', 'effectivity_date', 'expiration_date', 'with_exam', 'with_reviewer'
    ];
}
