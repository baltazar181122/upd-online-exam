@extends('layouts.layout')

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Dashboard</h1>
          </div>

        </div>
      </div><!-- /.container-fluid -->
    </section>

    <section class="content">
      <div class="container-fluid">
        <!-- if with exam -->
        <div class="callout callout-danger">
          @if( !$exam )
            <h4>Batch has not started yet</h4>
          @else
            @if( $exam->with_exam == "0" )
              <h4>No exam schedule available</h4>
            @else 
              @if( now() > $exam->expiration_date )
                <h4>This batch has expired!</h4>
              @elseif( now() < $exam->effectivity_date )
                <h4>Batch has not started yet</h4>
              @else
                @if(now() < $exam->exam_start)
                  <h4><strong>Attention!</strong></h4>
                  <strong>Exam schedule</strong> will be on <strong>{{ date('M d, Y H:s', strtotime($exam->exam_start)) }} up to {{ date('M d, Y H:s', strtotime($exam->exam_end)) }}</strong>

                @elseif( now() >= $exam->exam_start && now() < $exam->exam_end )
                  <h4><strong>Attention!</strong></h4>
                  <strong>Exam is now available!</strong> Kindly go to Exam menu to start the exam. <br/>
                  <i>Note: Once the exam has started, the 2-hour timer will start running and it will continue even you close this page. <br>Don't forget that the exam will only be available until <strong>{{ date('M d, Y H:s', strtotime($exam->exam_end)) }}</strong></i>. <br>
                  Good luck!

                @elseif( now() >= $exam->exam_end )
                  <h4>Reminder!</h4>
                  @if( !$result )
                    Exam has expired. Please wait for the result announcement.
                  @else
                    Exam has expired. Please wait for the result on <strong>{{ date('M d, Y', strtotime($result->result_date)) }}</strong>
                  @endif  
                @endif
              @endif            
            @endif
          @endif
        </div>
        <!-- /. exam -->

        <div class="row">
          <!--reviewe/refresher-->
          @if( $exam )
            @if( $exam->with_reviewer !== 0 )
              <div class="col-lg-6">
                <div class="card">
                  <div class="card-header border-0">
                    <h3 class="card-title"><strong>Reviewer/Refresher</strong></h3>
                  </div>
                  <div class="card-body p-0">
                    <table class="table table-striped table-valign-middle">
                      <thead>
                      <tr>
                        <th style="width:40%">Reviewer Name</th>
                        <th style="width:40%">Expiration</th>
                        <th style="width:20px">Action</th>
                      </tr>
                      </thead>
                      <tbody>
                        @foreach( $reviewer as $r )
                          <tr>
                            <td>
                              {{ $r->reviewer_name }}
                            </td>
                            <td>
                              <strong>{{ date('M d, Y H:m', strtotime($r->expiration_date)) }}</strong>
                            </td>
                            <td>
                              @if( now() > $r->expiration_date )
                                <i>Expired</i>
                              @else
                                <a download href="/reviewer/{{ $r->file_name }}" class="btn btn-sm btn-default" title="Download"><span class=" fa  fa-cloud-download"></span></a>
                              @endif
                            </td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            @endif
          @endif
          <!--/.reviewer/refresher-->

          <!--results-->
          @if( $result )
            @if( now() >= $result->result_date && now() <= $result->expiration_date )
              <div class="col-lg-6">
                <div class="card card-widget">
                  <div class="card-header">
                    <div class="user-block">
                      <span class="card-title"><strong>Exam Result</strong></span>
                    </div>
                  </div>
                  
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-6">
                        <strong>Batch Name:</strong> {{ $result->batch_name }}<br>
                        <strong>Exam Title:</strong> {{ $result->title }}<br>
                        <strong>Date Time Started:</strong> {{ date('M d, Y   H:m:s', strtotime($result->exam_start)) }}<br>
                        <strong>Date Time Submitted:</strong> {{ date('M d, Y   H:m:s', strtotime($result->exam_submitted)) }}

                      </div>

                      <div class="col-md-6">
                        <strong>Batch Code:</strong> {{ $result->batch_code }} <br>
                        <strong>Points:</strong> {{ $result->result }}<br>
                        
                        <strong>Result:</strong> {{ $result->status }}
                      </div>
                    </div>

                    <table class="table table-striped table-valign-middle">
                      <thead>
                      <tr>
                        <th style="width:70%">Question</th>
                        <th class="text-center">Correct Answer</th>
                        <th class="text-center">Answer</th>
                      </tr>
                      </thead>
                      <tbody>
                        @foreach( $result_raw as $rr )
                          <tr>
                            <td>{{ $rr->questions }}</td>
                            <td class="text-center"><strong>{{ $rr->answer }}</strong></td>
                            <td class="text-center">
                              @if( $rr->result == 0 )
                                <span class="badge badge-danger" style="font-size: 12px">{{ $rr->answer_submited }}</span>
                              @else
                                <span class="badge badge-success" style="font-size: 12px">{{ $rr->answer_submited }}</span>
                              @endif
                            </td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>

                  </div>
                  
                </div>
              </div>
            @endif
          @endif
        <!--/.results-->

        </div>


      </div>
    </section>
    <!-- /.content -->
  </div>

@endsection


@section('js')
<script src="{{ asset('js/student_dashboard.js') }}"></script>
@endSection