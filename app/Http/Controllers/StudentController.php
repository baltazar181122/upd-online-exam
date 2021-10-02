<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\User;
use App\BatchMasterlist;
use App\Reviewer;
use App\ExamResult;
use App\ExamResultRaw;
use App\Question;
use Response;
use Storage;
use DB;
class StudentController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $exam =  BatchMasterlist::join('batch', 'batch.id', '=', 'batch_masterlists.batch_id')
            ->leftJoin('batch_exam', 'batch_exam.batch_id', '=', 'batch.id')
            ->select('batch_masterlists.student_name', 
                    'batch_masterlists.email', 
                    'batch.batch_code', 
                    'batch.batch_name', 
                    'batch.effectivity_date', 
                    'batch.expiration_date', 
                    'batch.with_exam', 
                    'batch.with_reviewer', 
                    'batch_exam.exam_start', 
                    'batch_exam.result_date', 
                    'batch_exam.exam_end')
            ->where('batch_masterlists.email', '=', Auth::user()->email)
            ->first();
            // ->whereRaw(DB::raw('NOW() < batch.expiration_date'))

        $reviewer = Reviewer::where('batch_id','=',Auth::user()->batch_id)->get();
        

        $result = ExamResult::join('batch_exam','batch_exam.batch_id','=','exam_results.batch_id')
                        ->join('batch','batch.id','=','exam_results.batch_id')
                        ->join('exams','exams.id','=','exam_results.exam_id')
                        ->select('exam_results.exam_start',
                                'exam_results.exam_end',
                                'exam_results.exam_submitted',
                                'exam_results.result',
                                'exam_results.status',
                                'batch_exam.result_date',
                                'batch.expiration_date',
                                'batch.batch_name',
                                'batch.batch_code',
                                'exams.title',
                                'exam_results.status')
                        ->where('exam_results.batch_id','=',Auth::user()->batch_id)
                        ->where('exam_results.user_id','=',Auth::user()->id)
                        ->first();
        
        
        
        $result_raw = ExamResultRaw::join('questions','questions.id','=','exam_result_raw.question_id')
                        ->select('questions.questions',
                                'questions.answer',
                                'exam_result_raw.answer_submited',
                                'exam_result_raw.result')
                        ->where('exam_result_raw.batch_id','=',Auth::user()->batch_id)
                        ->where('exam_result_raw.user_id','=',Auth::user()->id)
                        ->get(); 
        /*
        $overall = count($result_raw);
        <!-- <strong>Percentage:</strong> {{ number_format(($result->result/$overall)*100, 2) }} %<br> -->
        */

        if (Auth::user()->user_type != 1) {
            return redirect('/home');die;
        }
        return view('student.index', 
                    [   'exam'=>$exam, 
                        'reviewer'=>$reviewer,
                        'result'=>$result,
                        'result_raw'=>$result_raw
                    ]);
                    //'overall'=>$overall
    }

    public function profile(){
        return view('student.profile');
    }
    
    public function profileUpdate(Request $request){
        $data = User::find(Auth::user()->id);
        $email = $data->email;
        $data->first_name = $request->first_name;
        $data->last_name = $request->last_name;
        $data->email = $request->email;
        if ($request->hasfile('image')) {
            Storage::delete(public_path('images/profile_photos'), $request->image->getClientOriginalExtension());
            $data->avatar = time().'.'.$request->image->getClientOriginalExtension();
            $request->image->move(public_path('images/profile_photos'), $data->avatar);
         }
       if ($data->save() && Auth::user()->user_type != 2) {
        $data           = BatchMasterlist::where('email',$email)->first();       
        $data->email    = $request->email;
        $data->save();
       }
        return response()->json('success');
    }
}
