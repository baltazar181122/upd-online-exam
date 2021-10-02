<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Question;
use App\ImageAnswer;
use App\ImageExam;
use Auth;

class QuestionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function view($id)
    {
    	$data = Question::where('exam_id',$id)->orderBy('id', 'DESC')->get();
    	return json_encode($data);
    }
    public function viewImage($id)
    {
        $data = ImageExam::where('exam_id',$id)->orderBy('id', 'DESC')->get();
        $html = view('exam.imageExamModal',compact('data'))->render();
        return json_encode($html);
    }

//     public function import(Request $request) 
//     {
//         return response()->json($request->all());
//         // if(!empty($arr)){
// 	       //  Question::insert($arr);
//         //     return json_encode('success');
//         // } 
//     }
}
