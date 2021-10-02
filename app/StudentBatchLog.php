<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentBatchLog extends Model
{
    protected $table = 'student_batch_log';


    public function transferStudent($master_list_id, $batch_id, $status){
        $transfer               = new StudentBatchLog;
        $transfer->batch_id     = $batch_id;
        // master list id
        $transfer->student_id   = $master_list_id;
        $transfer->status       = $status;
        $transfer->save();

        return  "success";
    }

    public function UpdateStudent($master_list_id, $status){
        // student_id = master_list_id
        $transfer               =  StudentBatchLog::where('student_id',$master_list_id)->orderBy('id','desc')->first();
        $transfer->status       = $status;
        $transfer->save();
        return  "success";
    }
}
