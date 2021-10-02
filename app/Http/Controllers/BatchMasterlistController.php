<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Notifications\RegistrationLink;
use App\BatchMasterlist;
use App\User;
use App\StudentBatchLog;
use Excel;
use DateTime;
use DateTimeZone;
class BatchMasterlistController extends Controller
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

    public function index($batch_id = null)
    {
        $batch_masterlists = BatchMasterlist::where('batch_id','=',$batch_id)->get();
        return response()->json($batch_masterlists);
    }

    public function save(Request $request){

        $checkExist = new BatchMasterlist;
        $checkExist = $checkExist->allowTransfer($request->student_email)->first();

        // check if user masterlist exist
        if (!empty($checkExist) && $request->batch_id != $checkExist->batch_id) {
            return json_encode(array('exist', 'user_id' => $checkExist->id, 'batch' => $request->batch_id, 'email' => $checkExist->email, 'batch_name' => $checkExist->batch->batch_name));die;
        }
        $error = "";
        $res = "";
        $check_student = User::where('email', $request->email)->first();
        $batch_masterlists = BatchMasterlist::where('batch_id',$request->batch_id)
                                        ->where('id','<>',$request->masterlist_id)
                                        ->get();
        

        $get_email = BatchMasterlist::where('batch_id',$request->batch_id)
                                        ->where('id','<>',$request->masterlist_id)
                                        ->whereRaw('LOWER(`email`) LIKE ? ',[trim(strtolower($request->student_email)).'%'])
                                        ->get();

        if ( !(count($batch_masterlists) < 200) ) {
            $error = "You have reached the limit of 200 students per batch. ";
        }
        if (!empty(count($get_email))) {
            $error = "Email already on the list.";
        }

        if ($error == "") {
            $data = BatchMasterlist::UpdateOrCreate(
                ['id' => $request->masterlist_id],
                ['student_name' =>  $request->student_name,
                'email' =>  $request->student_email,
                'batch_id' =>  $request->batch_id,
                'code' =>  time().Auth::user()->id,
                'status' => 0]); 

                  
            $transfer = new StudentBatchLog;
            $res =  $transfer->transferStudent($data->id,$request->batch_id, '0');
        }
        else {
            $res="errror";
        }
        return json_encode([$res, $error, $get_email]);
        $error = "";
    }

    public function  emailRegistrationCode(Request $request){
        $data = BatchMasterlist::find($request->id);

        $user = new User();
        $user->email = $data->email;   // This is the email you want to send to.
        $arr = ['code' => $data->code, 'email' => $data->email
            ];
        $user->notify(new RegistrationLink($arr));
        $data->status = 1;
        $data->save();
        return response()->json('success');
    }


    public function importExportView()
    {
       return view('import');
    }

    public function export() 
    {
        return Excel::download(new BatchMasterlistExport, 'test.xlsx');
    }

    public function import(Request $request) 
    {
        $path = $request->file('file')->getRealPath();
        $data = Excel::load($path)->get();
        $arr = array();
        $today = date('Y-m-d H:m:s');
        if($data->count()){
        $x = 0;

            foreach ($data as $key => $value) {
                $x++;
                if (isset($value->email) && isset($value->name)) {
                    $checkExist = new BatchMasterlist;
                    $checkExist = $checkExist->allowTransfer($value->email)->first();
                    if (!empty($checkExist) && $checkExist->email && $checkExist->id && $today > $checkExist->effectivity_date && $today < $checkExist->expiration_date) {
                        $notAllow[] =  array('email' => $checkExist->email);
                    }elseif(!empty($checkExist) && $checkExist->email && !$checkExist->id){
                        $exist[] =  array('email' => $checkExist->email, 'batch' => $checkExist->batch_name, 'current_batch_id' => $checkExist->current_batch_id, 'batch_transfer_id' => $request->batch_id_upload, 'x' => $x, 'batch_masterlist_id'=>$checkExist->batch_masterlist_id );
                    }else{
                        $arr[] = [  'batch_id'      => $request->batch_id_upload, 
                        'student_name'  => $value->name, 
                        'email'         => $value->email, 
                        'code'          =>  time().Auth::user()->id.$x,
                        'status'        => 0,
                        'created_at'    => date('Y-m-d G:i:s'),
                        'updated_at'    => date('Y-m-d G:i:s')
                        ];
                    }
                }
                
            }
            $error = "";
            $res = "";
            // return json_encode($exist);die;
            $batch_masterlists = BatchMasterlist::where('batch_id',$request->batch_id)
                                        ->where('id','<>',$request->masterlist_id)
                                        ->get();
            $count_masterlists = count($batch_masterlists) + count($arr);

            $get_email = BatchMasterlist::where('batch_id',$request->batch_id)
                                        ->where('id','<>',$request->masterlist_id)
                                        ->whereRaw('LOWER(`email`) LIKE ? ',[trim(strtolower($request->student_email)).'%'])
                                        ->get();

            if ( !($count_masterlists <= 200) ){
                $error = "You have reached the limit of 200 students per batch. ";
            }

            if (empty($arr[0]['email']) && empty($exist)) {
                $error = "Please check your uploaded Excel file.";
            }else {
                $res = "error";
            }

            if (isset($exist)) {
                $htmlexist = view('batch.exist', compact('exist'))->render();
            }
          
            if ($error == "" && isset($exist) && empty($arr[0]['email'])) {
                BatchMasterlist::insert($arr);
                $res = array('exist' => isset($exist) ? $htmlexist:'', 'notAllowed'  => isset($notAllow) ? $notAllow:'');
            }

            if ($error == "" && !empty($arr) && $arr[0]['email']) {
                BatchMasterlist::insert($arr);
                $res = array("success", 'exist' => isset($exist) ? $htmlexist:'', 'notAllowed'  => isset($notAllow) ? $notAllow:'');
            }
            return json_encode([$res, $error]);
        }
    }

    public function loadData(Request $request){
        $data = BatchMasterlist::find($request->data);
        return response()->json($data);
    }

    public function destroy($id) 
    {
        $data = BatchMasterlist::find($id);
        $batch_id = $data['batch_id'];
        $data->delete();
        return json_encode(["success", $batch_id]);
    }

    public function transfer(Request $request){
       return $this->transferSave($request->batch_transfer_id,$request->transfer_student_email,$request->batch_masterlist_id);
    }

    public function transferConfirm(Request $request){
        // return json_encode($request->batch_masterlist_id);
        return $this->transferSave($request->batch_transfer_id,$request->transfer_student_email,$request->batch_masterlist_id);
     }

    public function transferSave($batch_transfer_id, $transfer_student_email, $batch_masterlist_id = NULL){

        $error = "";
        $res = "";
        $if_email_exist_batch = BatchMasterlist::where('batch_id',$batch_transfer_id)
                                        ->where('email',$transfer_student_email)
                                        ->where('id','<>',$batch_masterlist_id)
                                        ->count();
        if (!empty($if_email_exist_batch)) {
            $error = "This email was already existed in the selected batch.";
        }
        else {
            $masterlist_count = BatchMasterlist::where('batch_id',$batch_transfer_id)
                                        ->where('id','<>',$batch_masterlist_id)
                                        ->count();
            if ($masterlist_count >= 200) {
                $error = "The selected batch was already reached max number of 200 students per batch. Please select another batch.";
            }
        }
        if ($error == "") {
            // $data           = BatchMasterlist::find($batch_masterlist_id);  
            $data = BatchMasterlist::where('email', $transfer_student_email)->first();     
            $data->batch_id = $batch_transfer_id;
            $data->save();
            
            if (!empty($user = User::where('email', $transfer_student_email)->first())) {
                $user->batch_id = $batch_transfer_id;
                $user->save();
            }
            
            $transfer = new StudentBatchLog;
            $get_email_masterlist = BatchMasterlist::where('email', $transfer_student_email)->first();
            $res =  $transfer->transferStudent($get_email_masterlist->id, $batch_transfer_id, '1');

        }
        else {
            $res="errror";
        }
        return json_encode([$res, $error]);
        $error = "";
    }

    public function transferConfirmBatch(){
        
    }



    public function sendBatchMail(Request $request){
        $masterlist = BatchMasterlist::where('batch_id', $request->id)->get();

      
        foreach ($masterlist as $key ) {
            $user = new User();
            $user->email = $key->email;   // This is the email you want to send to.
            $arr = ['code' => $key->code, 'email' => $key->email];
            $user->notify(new RegistrationLink($arr));


            $data = BatchMasterlist::find($key->id);
            $data->status = 1;
            $data->save();
        }
        return json_encode('success');

       
    }
  
   
}
