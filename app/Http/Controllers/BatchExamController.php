<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\BatchExam;
use App\Batch;

class BatchExamController extends Controller
{
 	public function __construct()
    {
        $this->middleware('auth');
    }   

    public function save(Request $request)
    {
        $error = "";
        
        $exam_start = substr($request->exam_start_end, 0,19);
        $exam_end = substr($request->exam_start_end, 22,38);

        $result_date = date('Y-m-d', strtotime($request->result_date));

        // echo $result_date; die;
        // return json_encode($result_date);
        $batch_start = $request->effectivity_date_batch_exam;
        $batch_end = $request->expiration_date_batch_exam;
        
        if ( !($result_date > $exam_end) ) { # result_date more than exam_end
        	$error = 'Result date must be greater than the exam end date';
        }
        if ( !($result_date > $batch_start && $result_date <= $batch_end) ) { # result_date more than exam_end
            $error = 'Result date must be within the effectivity of the Batch Selected';
        }
        if ( !($exam_start >= $batch_start && $exam_end <= $batch_end) ) { # Limit exam's effectivity and expiration within batch's effectivity and expiry
        	$error = 'Date must be within the effectivity of the Batch selected.';
    	}

        if ($error == "") {
	        BatchExam::UpdateOrCreate(
	            ['id' => $request->id],
	            ['batch_id' =>  $request->batch_id_exam,
	            'exam_id' =>  $request->exam_id,
	            'result_date' => $result_date,
	            'user_id' => Auth::user()->id,
	            'exam_start' => $exam_start,
                'exam_end' =>  $exam_end,
                'timer' => $request->timer ]); 
	        Batch::where('id',$request->batch_id_exam)->update(['with_exam'=>'1']);
	        return response()->json(['success']);
        }
        else {
        	return response()->json(['error', $error]);	
        	$error = "";
        }
    }

    public function loadData(Request $request){
        $data = Batch::find($request->data);
        $batch_exam = BatchExam::where('batch_id','=',$request->data)->first();
        return response()->json([$data, $batch_exam]);
    }
}
