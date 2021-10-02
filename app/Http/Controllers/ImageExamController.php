<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exam;
use Auth;
use App\ImageExam;
use App\ImageAnswer;
class ImageExamController extends Controller
{

    public function imagesUpload()
    {
        return view('exam.images');
    }

    public function imagesUploadPost(Request $request)
    {
        request()->validate([
            'uploadFile' => 'required',
            'image_exam_id' => 'required',
        ]);

        $exam = Exam::find($request->image_exam_id);
        $exam->type = 2;
        $exam->save();

        foreach ($request->file('uploadFile') as $key => $value) {
            $imageName = time(). $key . '.' . $value->getClientOriginalExtension();
            $value->move(public_path('images'), $imageName);
            ImageExam::create([  // <= the error is Here!!!
                'exam_id' => $exam->id,
                'image' => $imageName
            ]);
        }

        for ($i=1; $i <= $request->count_label ; $i++) { 
            ImageAnswer::create([  // <= the error is Here!!!
                'exam_id'           => $exam->id,
                'sequence'          => $i,
                'points'            => $request['labelpoints'.$i],
                'sequence_name'     => strtolower($request['labelname'.$i]),
            ]);
        }

        return response()->json('success');
    }

    public function viewImageExam(Request $request){
            $result = ImageExam::where('exam_id', $request->id)->get();
            return json_encode($result);
    }
}
