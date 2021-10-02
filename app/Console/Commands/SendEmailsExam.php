<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
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
class SendEmailsExam extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'say:sendExamResult';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $date = new DateTime("now", new DateTimeZone('Asia/Manila'));
        $this->info($date->format('Y-m-d'));

        $batch_exam = BatchExam::select('id')->where('result_date', $date->format('Y-m-d'))->get();

        
        $exam_results = BatchMasterlist::whereIn('batch_id', $batch_exam)->get();

        foreach ($exam_results as $key) {
            $data = BatchMasterlist::leftJoin('users', 'users.email', 'batch_masterlists.email')
            ->find($key->id);
            $user = new User();
            $user->email = $data->email;   // This is the email you want to send to.
            $arr = ['code' => $data->code, 'email' => $data->email,
                    'name' => ucwords($data->first_name), 'file' =>  $this->save_excel($data->user_id, $key->id, $data->exam_id)
                ];
            $user->notify(new ExamResultEmail($arr));
            $data->status = 1;
            $data->save();
        }

        $this->info('success');
    }



    private function examResultEmail($batch){
        $data = BatchMasterlist::leftJoin('users', 'users.email', 'batch_masterlists.email')
                ->find($batch);

      

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


                $user = new User();
                $user->email = $data->email;   // This is the email you want to send to.
                $arr = ['code' => $data->code, 'email' => $data->email,
                        'name' => ucwords($data->first_name), 'file' => $title
                    ];
                $user->notify(new ExamResultEmail($arr));
                $data->status = 1;
                $data->save();

                unlink(public_path().'/exports/'. $this->save_excel($data->user_id, $batch, $data->exam_id).'.xls');
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
