<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Batch;
use DB;

class HomeController extends Controller
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
        $active_batch_query = Batch::join('batch_exam', 'batch.id', '=', 'batch_exam.batch_id')
            ->join('exams', 'exams.id', '=', 'batch_exam.exam_id')
            ->select('batch.*', 'batch_exam.exam_id', 'batch_exam.result_date', 'batch_exam.exam_start', 'batch_exam.exam_end', 'exams.title')
            ->whereRaw(DB::raw('NOW() < batch.expiration_date'))
            ->orderBy('batch_exam.exam_start', 'asc')
            ->get();

        // $active_reviewer = 

        if (Auth::user()->user_type == 1) {
            return redirect('/student');die;
        }
        return view('admin.index', ['active_batch'=>$active_batch_query]);
    }
}
