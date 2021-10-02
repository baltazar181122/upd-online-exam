<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Reviewer;
use App\Batch;
use Auth;
use Illuminate\Support\Facades\Storage;

class ReviewerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        

        }

    public function view($id)
    {
    	$data = Reviewer::where('batch_id',$id)->get();
        // $path = public_path('reviewer')."\\";
    	return json_encode($data);
    }

    public function save(Request $request)
    {


    	$error = "";
    	$reviewer_start = substr($request->start_end_date_reviewer, 0,19);
        $reviewer_end = substr($request->start_end_date_reviewer, 22,38);


        // return json_encode($reviewer_start.'----'.$reviewer_end);die;
        $batch_start = $request->effectivity_date_reviewer;
        $batch_end = $request->expiration_date_reviewer;

        if ( !($reviewer_start >= $batch_start && $reviewer_end <= $batch_end) ) { # Limit effectivity and expiration within batch
        	$error = 'Date must be within the effectivity of the Batch selected.';
    	}

    	if ($error == "") {
	    	if ($request->hasfile('filename')) {
	            Storage::delete(public_path('reviewer'), $request->filename->getClientOriginalExtension());
	            $data = time().'.'.$request->filename->getClientOriginalExtension();
	            $request->filename->move(public_path('reviewer'), $data);
	        }
            // var_dump($request->filename->getClientOriginalExtension());die;
    		Reviewer::UpdateOrCreate(
	            ['id' => $request->id],
	            ['batch_id' =>  $request->batch_id_reviewer,
                'file_name' =>  $data,
                'reviewer_name' =>$request->filename->getClientOriginalName(),
	            'effectivity_date' => $reviewer_start,
	            'expiration_date' =>  $reviewer_end ]);
	        Batch::where('id',$request->batch_id_reviewer)->update(['with_reviewer'=>1]);
	        return json_encode(["success", $request->batch_id_reviewer]);
    	}
        else {
        	return json_encode(["error", $error]);
        	$error = "";
        }
    }

    public function destroy($id) 
    {
        $data = Reviewer::find($id);
        $file_name = $data['file_name'];
        $batch_id = $data['batch_id'];
        // $path = public_path('reviewer')."\\".$file_name;
        // $path = "reviewer"."\\".$file_name;
        // Storage::delete($file_name);
        $data->delete();
        return json_encode(["success", $batch_id]);
    }

}
