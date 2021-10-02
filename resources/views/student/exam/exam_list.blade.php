@extends('layouts.layout')

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <!-- <h1>Blank Page</h1> -->
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Exam/s</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <section class="content">
          @if(Session::has('message'))
              <div class="alert {{ Session::get('alert-class', 'alert-info') }} alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i> Success!</h4>
                {!! Session::get('message') !!}
              </div>
          @endif
      <!-- Default box -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Denstistry ICT</h3>
          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
              <i class="fa fa-minus"></i></button>
            <button type="button" class="btn btn-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
              <i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="card-body">
                    <table id="tableBatch" class="table table-bordered table-hover ">
                      <thead>
                        <tr>
                          <td>
                              Exam
                          </td>
                          <td style="width: 400px;">
                            Exam Date
                          </td>
                          <td>
                            Status
                          </td>
                          <td style="width: 1px;">
                            Action
                          </td>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($exams as $exam)
                          @if($exam->id)
                          <tr>
                            <td>
                               {{  $exam->title }}
                            </td>
                            <td>
                             {{ date("M d, Y h:ia",strtotime($exam->exam_start)) }} - {{ date("M d, Y h:ia",strtotime($exam->exam_end)) }}
                            </td>
                            <td>
                             {{ $exam->status }}
                            </td>
                            <td>
                              <a href="#" class="btn {{ $exam->class }}  btn-sm btn-exam" data-url="{{ $exam->url }}">
                                  {{  $exam->btn_text }}
                              </a>
                            </td>
                          </tr>
                          @endif
                        @endforeach
                      </tbody>
                    </table>
                    
        </div>
        <!-- /.card-body -->
        <div class="card-footer">
           &nbsp;
        </div>
        <!-- /.card-footer-->
      </div>
      <!-- /.card -->

    </section>
    <!-- /.content -->
  </div>

  @endsection


  @section('js')
  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
  <script src="/js/app.js"></script>
  @endsection