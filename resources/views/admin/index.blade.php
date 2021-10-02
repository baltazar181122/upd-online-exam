@extends('layouts.layout')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.css') }}">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.6/css/buttons.dataTables.min.css">
@endSection

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Dashboard123</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Dashboard</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- admin -->
      @if(Auth::user()->user_type !== 1) 

        <div class="container-fluid">
          
          <div class="row">
            
            <div class="col-3">
              <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fa fa-list"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Active Batch</span>
                  <span class="info-box-number">1,410</span>
                </div>
              </div>

              <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fa fa-list"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Ongoing Batch</span>
                  <span class="info-box-number">1,410</span>
                </div>
              </div>
            
              <!-- <div class="info-box bg-info">
                <span class="info-box-icon"><i class="fa fa-bookmark-o"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Passing Rate</span>
                  <span class="info-box-number">41,410</span>
                  <div class="progress">
                    <div class="progress-bar" style="width: 70%"></div>
                  </div>
                  <span class="progress-description">
                    For the Last 30 Days
                  </span>
                </div>
              </div> -->

            </div>


            <div class="col-9">
                <div class="card card-danger card-outline">
                    <div class="card-header">
                      <h3 class="card-title" style="font-weight: bold">Upcoming Exams</h3>
                    </div>

                    <div class="card-body table-responsive p-0">
                      <table id="active_batch" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Batch Code</th>
                                <th>Batch Name</th>
                                <th>Exam Title</th>
                                <th>Exam Schedule</th>
                                <th>Batch Expiration</th>
                                <th>Batch Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($active_batch as $batch)
                              <tr>
                                <td>{{ $batch->batch_code }}</td>
                                <td>{{ $batch->batch_name }}</td>
                                <td>{{ $batch->title }}</td>
                                <td>{{ date('M d, Y H:m', strtotime($batch->exam_start))." to ".date('M d, Y H:m',strtotime($batch->exam_end)) }}</td>
                                <td>{{ date('M d, Y', strtotime($batch->expiration_date)) }}</td>
                                <td>
                                    @if(now() < $batch->effectivity_date )
                                      <span class="badge badge-warning" style="font-size: 13px">Inactive</span>
                                    @elseif( now() >= $batch->effectivity_date && now() < $batch->expiration_date)
                                      @if( now() >= $batch->exam_start && now() <= $batch->exam_end)
                                        <span class="badge badge-primary" style="font-size: 13px">On-going Exam</font>
                                      @elseif( now() > $batch->exam_end)
                                        <span class="badge badge-info" style="font-size: 13px">Exam Done</font>
                                      @else 
                                        <span class="badge badge-success" style="font-size: 13px">Active</font>
                                      @endif
                                    @endif
                                  </td>
                              </tr>
                            @endforeach
                        </tbody>

                    </table>
                    </div>
                </div>
              </div>

          </div>
          
        </div>

      @endif

    </section>
    <!-- /.content -->
  </div>

  @endsection

@section('js')
<script src=" {{ asset('assets/plugins/datatables/jquery.dataTables.js') }}"></script>
<script src=" {{ asset('assets/plugins/datatables/dataTables.bootstrap4.js') }}"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>

<script src="{{ asset('js/dashboard.js') }}"></script>
@endSection