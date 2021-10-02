@extends('layouts.layout')

@section('css')
<link rel="stylesheet" href="{{ asset('/assets/plugins/icheck/all.css')}}">
<link rel="stylesheet" href="{{ asset('/assets/plugins/ekko-lightbox/ekko-lightbox.css')}}">

@endsection

@section('content')
<div class="content-wrapper time_" data-time="{{ $time }}">
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
              <input type="text" id="timer" value="{{ $exam->timer}}">

            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
      <!-- Default box -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Denstistry ICT |<small style="color:#105d5d"> <span class="fa fa-clock-o"></span> Time Remaining:</small> <small id="time">--:--:--</small></h3>
          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
              <i class="fa fa-minus"></i></button>
            <button type="button" class="btn btn-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
              <i class="fa fa-times"></i></button>
          </div>
        </div>
        <form id="submit_exam" method="POST" action="/exams/submit">
          @csrf
          <input type="hidden" value="{{ $exam->examId }}" name="exam_">
          <div class="card-body">
                  <div class="row">
                    <div class="col-md-12">
                      <h2>{{ $exam->title }}</h2>
                      <hr>
                    </div>
                    @php
                     $x = 1
                    @endphp

                   <h4> Multiple Choice</h4>
                    @foreach($questions as $questions)
                    <div class="col-md-12">
                          <div class="form-group">
                              <div class="formp-control">
                              <label>{{ $x.')  '.$questions->questions }}?</label>
                              </div>
                              @if($questions->a)
                                <div class="form-check icheck-success d-inline">
                                    <input class="form-check-input" type="radio" value="A" name="q_{{$questions->id}}">
                                    <label class="form-check-label">{{ $questions->a }}</label>
                                </div>
                              @endif
                              @if($questions->b)
                                <div class="form-check icheck-success d-inline">
                                    <input class="form-check-input" type="radio" value="B" name="q_{{$questions->id}}">
                                    <label class="form-check-label">{{ $questions->b }}</label>
                                </div>
                              @endif
                              @if($questions->c)
                                <div class="form-check icheck-success d-inline">
                                    <input class="form-check-input" type="radio" value="C" name="q_{{$questions->id}}">
                                    <label class="form-check-label">{{ $questions->c }}</label>
                                </div>
                              @endif
                              @if($questions->d)
                                <div class="form-check icheck-success d-inline">
                                    <input class="form-check-input" type="radio" value="D" name="q_{{$questions->id}}">
                                    <label class="form-check-label">{{ $questions->d }}</label>
                                </div>
                              @endif
                              @if($questions->e)
                                <div class="form-check icheck-success d-inline">
                                    <input class="form-check-input" type="radio" value="E" name="q_{{$questions->id}}">
                                    <label class="form-check-label">{{ $questions->e }}</label>
                                </div>
                              @endif
                              @if($questions->f)
                                <div class="form-check icheck-success d-inline">
                                    <input class="form-check-input" type="radio" value="F" name="q_{{$questions->id}}">
                                    <label class="form-check-label">{{ $questions->f }}</label>
                                </div>
                              @endif
                          </div>
                      </div>
                    @php
                     $x++;
                    @endphp
                    @endforeach
                  </div>
                  @if(!empty($imgQuestions))
                  <div class="col-md-12">
                    <hr>
                    <h4> Name the Item on the Image</h4>
                    <div class="row">
                      <div class="col-md-12">
                      <div class="imgrow">
                      
                      <div class="col-12">
                          <div class="card card-primary">
                            <div class="card-body">
                              <div class="row">
                              @foreach($imgQuestions as $imgQuestion)
                              <div class="col-sm-2">
                                  <a href="/images/{{ $imgQuestion->image }}" data-toggle="lightbox" data-gallery="gallery">
                                    <img src="/images/{{ $imgQuestion->image }}" class="img-fluid mb-2" alt="white sample"/>
                                  </a>
                                </div>
                              @endforeach
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                      </div>
                      <div class="col-md-12">
                        <div class="row">
                          @for ($i = 1; $i < $itemImg+1; $i++)
                            <div class="col-md-6">
                            <div class="form-group">
                                <label>Item No.{{ $i }}</label>
                                <input type="text" class="form-control" autocomplete="off" name="items[]" >
                            </div>
                            </div>
                          @endfor
                        </div>
                      </div>
                    </div>
                  </div>
                  @endif
          </div>
          <div class="card-footer">
              <div class="form-group">
                  <button type="submit" class="btn btn-danger pull-right">Submit</button>
              </div>
          </div>
        </form>
        <!-- /.card-footer-->
      </div>
      <!-- /.card -->
    </section>
    <!-- /.content -->
  </div>

  @endsection


  @section('js')
  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
  <script src="{{ asset('/assets/plugins/icheck/icheck.min.js')}}"></script>
  <script src="{{ asset('assets/plugins/ekko-lightbox/ekko-lightbox.min.js')}}"></script>
  <script src="/js/app.js"></script>
  
  @endsection