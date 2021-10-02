@extends('layouts.layout')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/select2/select2.min.css') }}">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.6/css/buttons.dataTables.min.css">
@endSection

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Exam Results</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Exam Results</li>
          </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-header">
                        <div class="form-group col-md-4">
                          <select name="batch" id="batch" class="form-control">
                            <option value="0">Select Batch</option>
                            @foreach($batchs as $batch)
                            <option value="{{ $batch->id }}">{{ $batch->batch_name }}</option>
                            @endforeach
                          </select>
                      </div>
                    </div>
                  
                    <div class="card-body table-responsive">
                      <div class="form-group">
                        <label>Batch Exam Duration:</label><span id="effectivity_date"></span>
                      </div>
                      <hr>
                      <table id="tableBatchReportDetails" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Student Name:</th>
                                <th>Date Exam</th>
                                <th>Result</th>
                                <th>Status</th>
                                <th>View Exam</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                      </table>
                    </div>
                    <!-- /.card-body -->
                </div>
              <!-- /.card -->
            </div>
        </div>
    </section>
</div>


<div class="modal fade" id="viewExam" tabindex="-1" role="dialog" aria-labelledby="viewExamModalLabel" aria-hidden="true" style="z-index: 2000">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewExamModalLabel">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body table-responsive">
        <table  class="table table-bordered table-hover">
          <thead>
              <tr>
                <td>
                  Exam
                </td>
                <td>
                  Answer
                </td>
                <td>
                </td>
              </tr>
              <tbody class="detail_exam">
              </tbody>
          </thead>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
      </div>
    </div>
  </div>
</div>
@endsection

@section('js')
<script src=" {{ asset('assets/plugins/datatables/jquery.dataTables.js') }}"></script>
<script src=" {{ asset('assets/plugins/datatables/dataTables.bootstrap4.js') }}"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="{{ asset('js/exam_result.js') }}"></script>
@endSection
