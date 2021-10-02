<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Batch;
use App\Exam;
use App\BatchExam;
use Auth;
use DB;

class BatchController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $exam_dropdown = Exam::pluck('title', 'id');

        $active_batch = Batch::whereRaw(DB::raw('NOW() < expiration_date'))->pluck('batch_name', 'id');
    	$batch = Batch::All();

        $batch_all = Batch::select('batch.id', 'batch.batch_code', 'batch.batch_name', 'batch_exam.exam_id', 'batch.effectivity_date', 'batch.expiration_date', 'batch_exam.exam_start', 'batch_exam.exam_end', 'batch.with_exam', 'batch.with_reviewer')
                ->leftJoin('batch_exam','batch_exam.batch_id','=','batch.id')
                ->get();

        $collection = collect($batch_all);
        $data = $collection->map(function($batch) {
            $batch->status = null;
            $today = date('Y-m-d H:m:s');
            if ($batch->exam_id == NULL ) {
                if ($today < $batch->effectivity_date) {
                    $batch->status_badge = '<span class="badge badge-warning" style="font-size: 13px">In-active</span>';    
                    $batch->status = 'In-active';
                }
                else if ($today > $batch->expiration_date) {
                    $batch->status_badge = '<span class="badge badge-secondary" style="font-size: 13px">Expired</span>';
                    $batch->status = 'Expired';
                }
                else {
                    $batch->status_badge = '<span class="badge badge-success" style="font-size: 13px">Active</span>';
                    $batch->status = 'Active';
                }
            }
            else {
                if ($today > $batch->expiration_date) {
                    $batch->status_badge = '<span class="badge badge-secondary" style="font-size: 13px">Expired</span>';
                    $batch->status = 'Expired';
                }
                else if ($today >= $batch->exam_start && $today < $batch->exam_end) {
                    $batch->status_badge = '<span class="badge badge-primary" style="font-size: 13px">Exam On-going</span>';
                    $batch->status = 'Exam Ongoing';
                }
                else if ($today >= $batch->exam_end) {
                    $batch->status_badge = '<span class="badge badge-info" style="font-size: 13px">Exam Done</span>';
                    $batch->status = 'Exam Done';
                }
                else {
                    $batch->status_badge = '<span class="badge badge-success" style="font-size: 13px">Active</span>';
                    $batch->status = 'Active';   
                }
                
            }
            
            return $batch;            
        });

        // return json_encode($data);die;
        
        return view('batch.batch',compact('exam_dropdown', 'active_batch'), ['batch'=>$data]);
    }

    public function view(Request $request){
        $batch = Batch::find($request->data);
        return response()->json($batch);
    }

    public function save(Request $request)
    {
        $effectivity_date = substr($request->range_date, 0,19);
        $expiration_date = substr($request->range_date, 22,38);

        Batch::UpdateOrCreate(
            ['id' => $request->id],
            ['batch_code' =>  $request->batch_code,
            'batch_name' =>  $request->batch_name,
            'effectivity_date' => $effectivity_date,
            'expiration_date' =>  $expiration_date ]); 
        return response()->json('success');
    }

    public function batchCode()
    {
        $last_id = Batch::latest()->first();
        if (empty($last_id['id'])) {
            $new_batch_code = strtotime("now")."-1";   
        }
        else {
            $new_id = $last_id['id']+1;
            $new_batch_code = strtotime("now")."-".$new_id;
        }
        return response()->json($new_batch_code);
    }

}

