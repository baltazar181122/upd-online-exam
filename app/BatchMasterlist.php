<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BatchMasterlist extends Model
{
	protected $fillable = [
        'batch_id','email','student_name','code','status'
    ];

    public function batch(){
        return $this->belongsTo('App\Batch');
    }

    public function checkExistStudent($email){
        return $this->where('email', $email);
    }

    public function allowTransfer($email){
        return $this->select('batch_masterlists.id as batch_masterlist_id','batch_masterlists.batch_id as current_batch_id','batch_masterlists.email', 'exam_results.id', 'users.batch_id', 'effectivity_date', 'expiration_date', 'batch.batch_name')
                ->leftJoin('users', 'users.email', 'batch_masterlists.email')
                ->leftJoin('exam_results', 'users.id', 'exam_results.user_id')
                ->leftJoin('batch', 'batch.id', 'batch_masterlists.batch_id')
                ->where('batch_masterlists.email', $email);
    }
}
