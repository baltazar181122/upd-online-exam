<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exam;
use App\Batch;
use App\Question;
use Auth;
use Excel;
use App\BatchExam;
use DateTime;
use DateTimeZone;
use App\ExamResultRaw;
use App\ExamResult;
use App\BatchMasterlist;
use App\StudentBatchLog;
use Session;
use App\ImageAnswer;
use DB;
use App\ImageExam;
use App\Notifications\ExamResult as ExamResultEmail;
use App\User;
class ExamController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $exams = Exam::all();
        $exams = collect($exams);
        $exams = $exams->map(function($exam) {
            $exam->taken  = ExamResult::where('exam_id', $exam->id)->count();
            return $exam;
        });
		return view('exam.exam', ['exams'=>$exams]);
    }
    
    public function saveExamMain(Request $request)
    {
        // TODO: add function that rejects saving of similar exam titles
        $saveExam = Exam::UpdateOrCreate(
            ['id' => $request->id],
            ['title' =>  $request->exam_title,
            'created_by' => Auth::user()->id]); 
        return $saveExam;
    }

    public function save(Request $request)
    {
		$path = $request->file('file')->getRealPath();
        $data = Excel::load($path)->get();
 
        if ( !($data->count()) ) {
        	return response()->json(['error','No Data on the Uploaded Excel']);
        }
        else {
            DB::beginTransaction();
            try {
                foreach ($data as $key => $value) {
                    if ($value->question) {
                        $arr[] = [  
                            'exam_id'       => $request->exam_id,
                            'questions'     => $value->question, 
                            'a'             => $value->a, 
                            'b'             => $value->b, 
                            'c'             => $value->c, 
                            'd'             => $value->d, 
                            'e'             => $value->e, 
                            'f'             => $value->f, 
                            'answer'        => strtoupper($value->answer), 
                            'points'        => $value->points == '' ? 1 : $value->points,
                            'user_id'       => Auth::user()->id,
                            'created_at'    => date('Y-m-d G:i:s'),
                            'updated_at'    => date('Y-m-d G:i:s')
                        ];
                    }
                }
                Question::insert($arr);
                DB::commit();
    	        return response()->json(['success', $request->exam_id]);        	
            } 
            catch(Exception $e){
                DB::rollback();
            }
       
        }
    }

    public function exams(){
        $data = Exam::select('exams.id','exams.title', 'batch_exam.exam_start', 'batch_exam.exam_end','img.id as img_id', 'img.title as img_title')
                ->leftjoin('batch_exam', 'exam_id', 'exams.id')
                ->leftjoin('exams as img', 'img.id', 'exams.id')
                ->where('batch_exam.batch_id', Auth::user()->batch_id)
                ->get();

                
                $collection = collect($data);
                $data = $collection->map(function($exam) {
                    $date = new DateTime("now", new DateTimeZone('Asia/Manila'));
                    $date->format('Y-m-d H:i:s');
                    $now  = strtotime($date->format('Y-m-d H:i:s'));
                    $start = strtotime($exam->exam_start);
                    $end = strtotime($exam->exam_end);
                    if ($now > $start) {
                       if ($now > $end) {
                           $exam->status = 'Expired';
                           $exam->class = 'btn-danger disabled';
                           $exam->url = '';
                           $exam->btn_text = 'Expired';
                       }else{   
                        $exam_result = ExamResult::where('exam_id',$exam->id)
                        ->where('user_id', Auth::user()->id)
                        ->where('batch_id', Auth::user()->batch_id)
                        ->first();
                        $exam->url = '/student/exam/'.encrypt($exam->id);
                        $exam->status = 'In-Progress';

                        if (empty($exam_result)) {
                            $exam->class = 'btn-success';
                            $exam->btn_text = 'Start';
                        }else{
                            if ($exam_result->result != NULL ) {
                                $exam->btn_text = 'Done';
                                $exam->class = 'btn-primary disabled';
                                $exam->url = '';
                                Session::flash('message', 'The result will be send via Email,  2 days after the examination period. Thank you!'); 
                                Session::flash('alert-class', 'alert-success'); 
                            }elseif($exam_result->exam_end < (new DateTime("now", new DateTimeZone('Asia/Manila')))->format('Y-m-d H:i:s')){
                                $exam->btn_text = 'Times Up';
                                $exam->class = 'btn-danger disabled';
                                $exam->url = '';
                                Session::flash('message', '<strong>Sorry!</strong>, Times Up!, The result will be send via Email,  2 days after the examination period. Thank you!!'); 
                                Session::flash('alert-class', 'alert-danger'); 
                            }else{
                                $exam->btn_text = 'Continue';
                                $exam->class = 'btn-warning';
                            }
                        }
                       }
                    }else{
                        $exam->status = 'Not Yet Started';
                        $exam->class = 'btn-warning disabled';
                        $exam->url = '';
                        $exam->btn_text = 'Soon';
                    }
                    return $exam;
                });

        // return json_encode($data);
        return view('student.exam.exam_list',['exams'=>$data]);
    }

    public function exam($id){

        $batchExam = BatchExam::where('batch_id', Auth::user()->batch_id)
                ->where('exam_id', decrypt($id))->first();



        $tz = 'Asia/Manila';
        $tz_obj = new DateTimeZone($tz);
        $today = new DateTime("now", $tz_obj);
        $time_start = $today->format('Y-m-d H:i:s');
        $time_end  = date('Y-m-d H:i:s',strtotime("+$batchExam->timer minutes",strtotime($time_start)));

        $exam_result = ExamResult::where('exam_id', decrypt($id))
        ->where('user_id', Auth::user()->id)
        ->where('batch_id', Auth::user()->batch_id)
        ->first();

       
        if (empty($exam_result)) {
            $exam_result = New ExamResult;
            $exam_result->exam_id = decrypt($id);
            $exam_result->user_id = Auth::user()->id;
            $exam_result->batch_id = Auth::user()->batch_id;
            $exam_result->exam_start = $time_start;
            $exam_result->exam_end = $time_end;
            $exam_result->save();
        }else{
            if ( ($exam_result->exam_end < $time_start || $exam_result->result != NULL) ) {
                return redirect('/student/exams');
            }
        }

        $datetime1 = new DateTime("NOW");
        $datetime2 = new DateTime($exam_result->exam_end);
        $interval = $datetime1->diff($datetime2);
        $elapsed = $interval->h*3600 + $interval->i*60 +  $interval->s ;

        $exam = Exam::select('exams.id as examId', 'title', 'timer')
                ->leftJoin('batch_exam', 'batch_exam.exam_id', 'exams.id')
                ->where('batch_exam.batch_id', Auth::user()->batch_id)
                ->where('exams.id', decrypt($id))
                ->first();

        $questions = Question::where('exam_id', decrypt($id))->orderBy('id', 'ASC')->get();
        $imgQuestions = ImageExam::where('exam_id', decrypt($id))->orderBy('id', 'ASC')->get();
        $itemImage = ImageAnswer::where('exam_id', decrypt($id))->count();

        // return json_encode($imgQuestions);
        return view('student.exam.exam',['questions'=>$questions, 'exam' => $exam, 'time' =>  $elapsed, 'imgQuestions'=>$imgQuestions, 'itemImg' => $itemImage ]);
    }

    public function submit(Request $request){

        // return json_encode($request->items);
        $tz = 'Asia/Manila';
        $tz_obj = new DateTimeZone($tz);
        $today = new DateTime("now", $tz_obj);
        $submitted_time = $today->format('Y-m-d H:i:s');
        
        $questions = Question::where('exam_id', $request->exam_)->get();
        


        $i = 1;
        $x = 0;
        if ($request->items) {
            foreach ($request->items as $key => $value) {
                $img_question = ImageAnswer::where('exam_id', $request->exam_)->where('sequence', $i)->first();
                $data = new ExamResultRaw;
                $data->user_id = Auth::user()->id;
                $data->batch_id =  Auth::user()->batch_id;
                $data->exam_id = $request->exam_;
                $data->question_id = 'sequnce_'.$i;
                $data->answer_submited  = $value;
                if (strtolower($img_question->sequence_name) == strtolower($value)) {
                    $data->result = 1;
                }else{
                    $data->result = 0;
                }
                $data->points = $img_question->points;
                $data->save();
                $x = $x + $img_question->points;
                $i++;
            }
        }
       

        // $x = 0;
        foreach ($questions as $question) {
            $data = new ExamResultRaw;
            $data->user_id = Auth::user()->id;
            $data->batch_id =  Auth::user()->batch_id;
            $data->exam_id = $request->exam_;
            $data->question_id = $question->id;
            $data->answer_submited  = $request['q_'.$question->id.''];
            if ($question->answer == $request['q_'.$question->id.'']) {
                $data->result = 1;
            }else{
                $data->result = 0;
            }
            $data->points = $question->points;
            $data->save();
            $x = $x + $question->points;
        }
       
        $score = ExamResultRaw::selectRaw('sum(points) as score')
        ->where('result', 1)
        ->where('user_id', Auth::user()->id)
        ->where('batch_id', Auth::user()->batch_id)
        ->first();
            
        $result =   ExamResult::
                    where('user_id', Auth::user()->id)
                    ->where('batch_id', Auth::user()->batch_id)
                    ->where('exam_id', $request->exam_)
                    ->first();

        $result->result = ($score->score)? $score->score:0;
        $result->exam_submitted = $submitted_time;
        $result->save();

        $pass = $x*0.5;
        if ($score->score < $pass) {
            $masterlist_id = new BatchMasterlist;
            $masterlist_id = $masterlist_id->checkExistStudent(Auth::user()->email)->orderBy('id', 'desc')->first();

            $transfer = new StudentBatchLog;
            $res =  $transfer->UpdateStudent($masterlist_id->id, '2');

            $result->status = 'FAILED';
            $result->save();
        }else{
            $result->status = 'PASSED';
            $result->save();
        }
        return redirect('/student/exams');
    }

    /** reports **/
    public function viewReports () {
        $batchs = Batch::All();
        return view('reports.report_exam', ['batchs' => $batchs]);
    }

    public function examDetails(Request $request){
        $exam = ExamResultRaw::select('questions.questions', 'answer_submited', 'result', 'exam_result_raw.points', 'exam_result_raw.question_id')
                            ->leftJoin('questions', 'questions.id', 'question_id')
                            ->where('exam_result_raw.user_id', $request->user)
                            ->where('exam_result_raw.exam_id', $request->exam)
                            ->where('exam_result_raw.batch_id', $request->batch)
                            ->get();

        return json_encode($exam);
    }

    public function batchDetails(Request $request){
        $batch = Batch::select('effectivity_date', 'expiration_date')
                ->leftJoim('')
                ->where('id', $request->batch_id)->first();
        return json_encode($batch);
    }

    public function batchReportDetails($id){
        $data = ExamResult::select('exam_results.exam_id', 'exam_results.user_id', 'exam_results.batch_id', 'exam_results.exam_start', 'exam_results.exam_end', 'exam_results.exam_submitted', 'exam_results.result', 'exam_results.status', 'users.email', 'users.first_name', 'users.middle_name', 'users.last_name')
                ->leftJoin('users', 'users.id', 'user_id')
                ->where('exam_results.batch_id', $id)
                ->orderBy('result', 'desc')
                ->get();
        return response()->json($data);
    }

    public function loadExamResults() {
        $data = ExamResult::find($request->data);
        return response()->json($data);
    }

    public function delete(Request $request){
        $batch_exam = BatchExam::where('exam_id', $request->exam_id)->get();
        foreach ($batch_exam as $key => $value) {
                $batch = Batch::where('id', $value->batch_id)->first();
                $batch->with_exam = 0;
                $batch->save();
        }
        BatchExam::where('exam_id', $request->exam_id)->delete();
        Exam::where('id', $request->exam_id)->delete();

        return json_encode('success');
    }

    public function print_exam($id = NULL, $batch_id = NULL, $exam_id = NULL) {
        $get_student_details = ExamResult::select('exam_results.user_id', 'users.first_name', 'users.middle_name', 'users.last_name','users.email','batch.batch_name','batch.batch_code','exam_results.exam_start','exam_results.exam_end','exam_results.result','exam_results.status')
            ->join('users','users.id','exam_results.user_id')
            ->join('batch','batch.id','exam_results.batch_id')
            ->where('exam_results.user_id',$id)
            ->where('exam_results.batch_id', $batch_id)
            ->where('exam_results.exam_id',$exam_id)
            ->first();
        
        $title = $get_student_details['batch_code']."_".$get_student_details['batch_name']."_".$get_student_details['last_name']."_".$get_student_details['first_name']."_".$get_student_details['middle_name'];

        $get_answer_multchoice = ExamResultRaw::select('questions.questions', 'exam_result_raw.answer_submited', 'questions.answer', 'exam_result_raw.result', 'exam_result_raw.points','a', 'b', 'c', 'd','e', 'f')
            ->join('questions','questions.id','exam_result_raw.question_id')
            ->where('exam_result_raw.user_id',$id)
            ->where('exam_result_raw.batch_id', $batch_id)
            ->where('exam_result_raw.exam_id',$exam_id)
            ->get();
        
        $get_answer_image = ExamResultRaw::select('image_answer.sequence_name', 'exam_result_raw.answer_submited', 'exam_result_raw.result', 'exam_result_raw.points')
            ->join('image_answer',DB::raw('SUBSTR(exam_result_raw.question_id, 9, LENGTH(exam_result_raw.question_id))'),'image_answer.sequence','limit 1')
            ->where('exam_result_raw.user_id',$id)
            ->where('exam_result_raw.batch_id', $batch_id)
            ->where('exam_result_raw.exam_id',$exam_id)
            ->where('image_answer.exam_id',$exam_id)
            ->get();
            
        // echo $get_answer_image;die;

        Excel::create($title, function($excel) use($get_student_details, $title, $get_answer_multchoice, $get_answer_image) {
            // Set the title   
            $excel->setTitle($title);
            $excel->setCreator('no no creator')->setCompany('no company');
            $excel->setDescription($title);

    
            $excel->sheet('sheet1', function($sheet) use($get_student_details, $get_answer_multchoice, $get_answer_image){
                $data = array(
                    array('Student Name:', $get_student_details['first_name']." ".$get_student_details['middle_name']." ".$get_student_details['last_name']),
                    array('Email', $get_student_details['email']),
                    array('Batch Name', $get_student_details['batch_name']." (".$get_student_details['batch_code'].")"),
                    array('Exam DateTime:', $get_student_details['exam_start']." to ".$get_student_details['exam_end']),
                    array('Score:', $get_student_details['result']),
                    array('Remarks:', $get_student_details['status']),
                    array(''),
                    array('Multiple Choice'),
                    array('#','QUESTION', 'STUDENT ANSWER', 'CORRECT ANSWER', 'REMARK', 'POINT')
                );

                $count1 = count($get_answer_multchoice);
                for($i=0; $i < $count1; $i++){
                    $remark = ($get_answer_multchoice[$i]['result'] === 1 ? 'CORRECT' : 'X');
                    $point = ($get_answer_multchoice[$i]['result'] === 1 ? $get_answer_multchoice[$i]['points'] * $get_answer_multchoice[$i]['result'] : 0);
                    $answer = strtolower($get_answer_multchoice[$i]['answer_submited']);
                    $correct = strtolower($get_answer_multchoice[$i]['answer']);
                    array_push($data, array($i+1, $get_answer_multchoice[$i]['questions'], $get_answer_multchoice[$i]['answer_submited']."). ".$get_answer_multchoice[$i]["$answer"], $get_answer_multchoice[$i]['answer']."). ".$get_answer_multchoice[$i]["$correct"],$remark, $point));    
                }
                $total1 = $count1+10-1;
                array_push($data, array('','','','','Total:','=SUM(F10:F'.$total1.')'));
                

                $count2 = count($get_answer_image);
                if($count2 > 0){
                    array_push($data, array('Image Exam'));
                    array_push($data, array('#','ITEM/CORRECT ANSWER', 'STUDENT ANSWER', 'REMARK', 'POINT'));
                    
                    for($i=0; $i < $count2; $i++){
                        $remark = ($get_answer_image[$i]['result'] === 1 ? 'CORRECT' : 'X');
                        $point = ($get_answer_image[$i]['result'] === 1 ? $get_answer_image[$i]['points'] * $get_answer_image[$i]['result'] : 0);
                        array_push($data, array($i+1, $get_answer_image[$i]['sequence_name'], $get_answer_image[$i]['answer_submited'], $remark, $point));
                    }

                    $total2 = $total1+4;
                    $total3 = $total2+$count2-1;
                    array_push($data, array('','','','Total:','=SUM(E'.$total2.':E'.$total3.')'));
                }

                $sheet->fromArray($data, null, 'A1', false, false);
                $sheet->cells('A1:B1', function($cells) {
                    $cells->setBackground('#AAAAFF');
                });
            });
            
        })->download();

    }


    public function examResultEmail(Request $request){
        $data = BatchMasterlist::leftJoin('users', 'users.email', 'batch_masterlists.email')
                ->where('batch_masterlists.email', $request->email)->first();
            //    return json_encode($data);

        $user = new User();
        $user->email = $data->email;   // This is the email you want to send to.
        $arr = ['code' => $data->code, 'email' => $data->email,
                'name' => ucwords($data->first_name), 'file' =>  $this->save_excel($request->user_id, $data->batch_id, $request->exam_id)
            ];
        $user->notify(new ExamResultEmail($arr));
        $data->status = 1;
        $data->save();

        // unlink(public_path().'/exports/'.$this->save_excel($request->user_id, $request->batch, $request->exam_id).'.xls');
        return response()->json('success');
    }

    private function save_excel($id = NULL, $batch_id = NULL, $exam_id = NULL) {
        
        $get_student_details = ExamResult::select('exam_results.user_id', 'users.first_name', 'users.middle_name', 'users.last_name','users.email','batch.batch_name','batch.batch_code','exam_results.exam_start','exam_results.exam_end','exam_results.result','exam_results.status')
            ->join('users','users.id','exam_results.user_id')
            ->join('batch','batch.id','exam_results.batch_id')
            ->where('exam_results.user_id',$id)
            ->where('exam_results.batch_id', $batch_id)
            ->where('exam_results.exam_id',$exam_id)
            ->first();
       
        $title = $get_student_details['batch_code']."_".$get_student_details['batch_name']."_".$get_student_details['last_name']."_".$get_student_details['first_name']."_".$get_student_details['middle_name'];
        
        $get_answer_multchoice = ExamResultRaw::select('questions.questions', 'exam_result_raw.answer_submited', 'questions.answer', 'exam_result_raw.result', 'exam_result_raw.points')
            ->join('questions','questions.id','exam_result_raw.question_id')
            ->where('exam_result_raw.user_id',$id)
            ->where('exam_result_raw.batch_id', $batch_id)
            ->where('exam_result_raw.exam_id',$exam_id)
            ->get();
          
        $get_answer_image = ExamResultRaw::select('image_answer.sequence_name', 'exam_result_raw.answer_submited', 'exam_result_raw.result', 'exam_result_raw.points')
            ->join('image_answer',DB::raw('SUBSTR(exam_result_raw.question_id, 9, LENGTH(exam_result_raw.question_id))'),'image_answer.sequence','limit 1')
            ->where('exam_result_raw.user_id',$id)
            ->where('exam_result_raw.batch_id', $batch_id)
            ->where('exam_result_raw.exam_id',$exam_id)
            ->where('image_answer.exam_id',$exam_id)
            ->get();
        // echo $get_answer_image;die;

        Excel::create($title, function($excel) use($get_student_details, $title, $get_answer_multchoice, $get_answer_image) {
            // Set the title   
            $excel->setTitle($title);
            $excel->setCreator('no no creator')->setCompany('no company');
            $excel->setDescription($title);

    
            $excel->sheet('sheet1', function($sheet) use($get_student_details, $get_answer_multchoice, $get_answer_image){
                $data = array(
                    array('Student Name:', $get_student_details['first_name']." ".$get_student_details['middle_name']." ".$get_student_details['last_name']),
                    array('Email', $get_student_details['email']),
                    array('Batch Name', $get_student_details['batch_name']." (".$get_student_details['batch_code'].")"),
                    array('Exam DateTime:', $get_student_details['exam_start']." to ".$get_student_details['exam_end']),
                    array('Score:', $get_student_details['result']),
                    array('Remarks:', $get_student_details['status']),
                    array(''),
                    array('Multiple Choice'),
                    array('#','QUESTION', 'STUDENT ANSWER', 'CORRECT ANSWER', 'REMARK', 'POINT')
                );

                $count1 = count($get_answer_multchoice);
                for($i=0; $i < $count1; $i++){
                    $remark = ($get_answer_multchoice[$i]['result'] === 1 ? 'CORRECT' : 'X');
                    $point = ($get_answer_multchoice[$i]['result'] === 1 ? $get_answer_multchoice[$i]['points'] * $get_answer_multchoice[$i]['result'] : 0);
                    array_push($data, array($i+1, $get_answer_multchoice[$i]['questions'], $get_answer_multchoice[$i]['answer_submited'], $get_answer_multchoice[$i]['answer'],$remark, $point));    
                }
                $total1 = $count1+10-1;
                array_push($data, array('','','','','Total:','=SUM(F10:F'.$total1.')'));
                

                $count2 = count($get_answer_image);
                if($count2 > 0){
                    array_push($data, array('Image Exam'));
                    array_push($data, array('#','ITEM/CORRECT ANSWER', 'STUDENT ANSWER', 'REMARK', 'POINT'));
                    
                    for($i=0; $i < $count2; $i++){
                        $remark = ($get_answer_image[$i]['result'] === 1 ? 'CORRECT' : 'X');
                        $point = ($get_answer_image[$i]['result'] === 1 ? $get_answer_image[$i]['points'] * $get_answer_image[$i]['result'] : 0);
                        array_push($data, array($i+1, $get_answer_image[$i]['sequence_name'], $get_answer_image[$i]['answer_submited'], $remark, $point));
                    }

                    $total2 = $total1+4;
                    $total3 = $total2+$count2-1;
                    array_push($data, array('','','','Total:','=SUM(E'.$total2.':E'.$total3.')'));
                }

                $sheet->fromArray($data, null, 'A1', false, false);
                $sheet->cells('A1:B1', function($cells) {
                    $cells->setBackground('#AAAAFF');
                });
            });
            
        })->store('xls',public_path('/exports'));


        return $title;
    }
}
