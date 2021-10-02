@extends('layouts.layout')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/daterangepicker/daterangepicker-bs3.css') }}">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.6/css/buttons.dataTables.min.css">
@endSection

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Batch Management</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Batch Management</li>
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
                        <i>*Once exam has started, you can never modify the details of the batch</i>
                        <button class="btn btn-primary pull-right add_batch"><span class="fa fa-user-plus"></span>  Add New Batch</button>
                    </div>
                    <div class="card-body table-responsive p-0">
                      <table id="tableBatch" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Batch Code</th>
                                <th style="width: 250px; text-align: center">Batch Name</th>
                                <th>Effectivity Date</th>
                                <th>Expiration Date</th>
                                <th style="width: 10px; text-align: center">With Exam</th>
                                <th style="width: 10px; text-align: center">With Reviewer</th>
                                <th style="width: 15px; text-align: center">Status</th>
                                <th style="width: 350px; text-align: center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($batch as $batchL)
                              <tr>
                                  <td>{{ $batchL->batch_code }}</td>
                                  <td>{{ $batchL->batch_name }}</td>
                                  <td>{{ date('M d, Y H:m', strtotime($batchL->effectivity_date)) }}</td>
                                  <td>{{ date('M d, Y H:m',strtotime($batchL->expiration_date)) }}</td>
                                  <td style="text-align: center">
                                    @if( $batchL->with_exam == "1" )
                                      <i class="fa fa-check text-success"></i>
                                    @else
                                      <i class="fa fa-times text-danger"></i>
                                    @endif
                                  </td>
                                  <td style="text-align: center">
                                    @if( $batchL->with_reviewer == "1" )
                                      <i class="fa fa-check text-success"></i>
                                    @else
                                      <i class="fa fa-times text-danger"></i>
                                    @endif
                                  </td>
                                  <td style="text-align: center">
                                      {!! $batchL->status_badge !!}
                                  </td>
                                  <td>
                                      @if ( $batchL->status == "In-active" || $batchL->status == "Active" )
                                        <button class="btn btn-sm btn-warning" onclick="editBatch('{{ $batchL->id }}')" title="Edit Batch Details"><i class="fa fa-edit"></i></button>
                                        &nbsp;<button class="btn btn-sm btn-info" onclick="viewMasterlist('{{ $batchL->id }}')">View Masterlist</button>
                                      @else 
                                        <button class="btn btn-sm btn-warning" title="Unable to Edit Batch Details" disabled=""><i class="fa fa-edit"></i></button>

                                        @if( $batchL->status == "Exam Ongoing" )
                                          &nbsp;<button class="btn btn-sm btn-info" onclick="viewMasterlist('{{ $batchL->id }}')">View Masterlist</button>
                                        @else
                                          &nbsp;<button class="btn btn-sm btn-info" onclick="viewMasterlist('{{ $batchL->id }}', 1)">View Masterlist</button>
                                          @endif  
                                      @endif

                                      &nbsp;<button class="btn btn-sm btn-success" onclick="addReviewer('{{ $batchL->id }}', 0)" title="Add/View Reviewers">Reviewer</button>
                                      
                                      &nbsp;<button class="btn btn-sm btn-primary" onclick="addExam('{{ $batchL->id }}')">Exam</button>

                                      @if ( $batchL->status == "In-active" || $batchL->status == "Active" )
                                        &nbsp;<button class="btn btn-sm btn-danger" title="Delete Batch" onclick=""><i class="fa fa-trash"></i></button>
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
    </section>
</div>

<!-- modal: batch details -->
<div class="modal fade" id="batchform_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Batch Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="batchform">
        <input type="hidden" name="id">

        <div class="modal-body">
          <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                    <label>Batch Code</label>
                    <input type="text" name="batch_code" class="form-control" readonly>
                </div>
              </div>
          </div>
          <div class="row">
              <div class="col-md-12">
                  <div class="form-group">
                      <label>Batch Name</label>
                      <input type="text" name="batch_name" class="form-control" placeholder="Enter Batch Name" required>
                  </div>
              </div>
          </div>
          <div class="row">
              <div class="col-md-12">
                  <div class="form-group">
                      <label>Effectivity Date & Expiry Date *</label>
                      <div class="input-group">
                          <div class="input-group-prepend">
                              <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                          </div>
                          <input type="text" name="range_date" class="form-control float-right" id="reservationtime" required>
                      </div>
                  </div>
              </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary" id="btn_new_batch_record">Save</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- modal: batch masterlist -->
<div class="modal fade" id="masterlist_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" style="z-index: 2000;">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Masterlist Email</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="masterlistform">
          @csrf
          <input type="hidden" id="batch_id" name="batch_id">
          <input type="hidden" id="masterlist_id" name="masterlist_id">
              <div class="row">
                <div class="col-md-5">
                  <div class="form-group">
                    <label for="student_name">Student Name:</label>
                    <input type="text" name="student_name" class="form-control" placeholder="Student Name" required>
                  </div>
                </div>
                <div class="col-md-5">
                  <div class="form-group">
                     <label for="student_name">Student Email:</label>
                     <input type="email" name="student_email" class="form-control" placeholder="Student Name" required>
                   </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <button style="margin-top: 30px;" class="btn btn-block btn-primary" id="btn_save_student"> <span class="fa fa-save"></span> Save</button>
                   </div>
                </div>
            </div>
        </form>
        <span style="font-weight: bold;">OR</span><br>
        <hr>
        <form id="form_upload_masterlist" >
        <input type="hidden" id="batch_id_upload" name="batch_id_upload">
            @csrf
            <div class="row">
              <div class="col-md-6">
                <input type="file" id="file" name="file" class="form-control" required>
                  <small class="text-danger">*Upload via Excel Only </small> | <small> Download template <a href="/assets/template/batch_student_infor_template.xlsx">Here!</a> </small>
              </div>
              <div class="col-md-6">
                <button class="btn btn-primary" id="btn_save_masterlist"><span class="fa fa-upload"></span> Upload Students Data</button>
              </div>
            </div>
        </form>
        
          <div class="exist">
          </div>
        <hr>
        <div class="row">
          <div class="col-md-12">
            <div class="table-responsive">
              <table id="tableMasterlist" class="table table-bordered table-hover" >
                  <thead>
                      <tr>
                          <th>Student Name</th>
                          <th>Email</th>
                          <th>Action</th>
                      </tr>
                  </thead>
                </table>
              </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- modal: Add Exam -->
<div class="modal fade" id="addexamform_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" style="z-index: 2000;">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Exam Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
    </div>
      <form id="addexamform">
        <div class="modal-body">
          <input type="hidden" name="id">
          <input type="hidden" name="batch_id_exam">
          <input type="hidden" name="effectivity_date_batch_exam">
          <input type="hidden" name="expiration_date_batch_exam">

          <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label>Select Exam</label>
                  <select class="form-control select2" name="exam_id" id="exam_id" style="width: 100%;" placeholder="Select Exam" required>
                    <option value="" disabled selected>Select Exam</option>
                    @foreach($exam_dropdown as $id => $title)
                    <option value="{{ $id }}">{{ $title }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
          </div>
          <div class="row">
              <div class="col-md-12">
                  <div class="form-group">
                      <label>Exam Start & Exam End</label>
                      <div class="input-group">
                          <div class="input-group-prepend">
                              <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                          </div>
                          <input type="text" name="exam_start_end" autocomplete="off" class="form-control float-right" id="reservationtime2" required>
                      </div>
                  </div>
              </div>
          </div>
          <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                  <label>Result Date</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                    </div>
                    <input type="date" class="form-control" autocomplete="off" name="result_date"  required>
                  </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                <label>Exam Timer:(minutes)</label>
                    <input type="number" class="form-control" name="timer" style="width:100px" required>
                </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary" id="btn_add_exam_to_batch">Save</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- modal: Reviewer -->
<div class="modal fade" id="batchreviewerform_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" style="z-index: 2000;">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Reviewer/Refresher</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    
      <div class="modal-body">

        <div class="card card-info card-outline">
          <div class="card-header">
            <h3 class="card-title">Add Reviewer</h3>
            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-widget="collapse">
                <i class="fa fa-minus"></i>
              </button>
            </div>
          </div>
          <div class="card-body p-0">
            <ul class="products-list product-list-in-card pl-2 pr-2">
              <br>
              <form id="addreviewerform">
                <input type="hidden" name="batch_id_reviewer">
                <input type="hidden" name="effectivity_date_reviewer">
                <input type="hidden" name="expiration_date_reviewer">
                @csrf
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <input type="file" name="filename" id="filename" class="form-control" required>
                      <small class="text-danger">*Upload .PDF/.pdf file </small> 
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Effectivity & Expiration Date</label>
                      <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                        </div>
                        <input type="text" name="start_end_date_reviewer" autocomplete="off" class="form-control float-right" id="reservationtime3" required>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-group pull-right">
                      <button type="submit" class="btn btn-primary btn-block">Save</button>
                    </div>
                  </div>
                </div>
              </form>
            </ul>
          </div>
        </div>
        <br>
        <hr>
        <div class="row">
          <div class="col-md-12">
            <div class="table-responsive">
              <table id="tableReviewer" class="table table-bordered table-hover">
                <thead>
                  <tr>
                    <th style="width: 50%">Reviewer</th>
                    <th style="width: 30%">Expiration</th>
                    <th style="width: 20%">Action</th>
                  </tr>
                </thead>
                
              </table>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- modal: Transfer Batch -->
<div class="modal fade" id="transferbatch_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" style="z-index: 2500;">
  <div class="modal-dialog modal-dialog-centered modal" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Batch Transfer</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      
      <form id="transferbatch_form">
        <input type="hidden" name="batch_masterlist_id">
        <input type="hidden" name="transfer_student_email">
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label>Select Batch</label>
                <select class="form-control select2" name="batch_transfer_id" id="batch_transfer_id" style="width: 100%;" placeholder="Select Exam" required>
                  @foreach($active_batch as $id => $title)
                    <option value="{{$id}}">{{ $title }}</option>
                  @endforeach 
                </select>
              </div>
            </div>
          </div>        
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="overlay_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-body email_url">
      </div>
    </div>
  </div>
</div>
@endsection


@section('js')
<script src=" {{ asset('assets/plugins/datatables/jquery.dataTables.js') }}"></script>
<script src=" {{ asset('assets/plugins/datatables/dataTables.bootstrap4.js') }}"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
<script src="{{ asset('assets/plugins/select2/select2.full.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>
<script src="{{asset('assets/plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{ asset('assets/plugins/input-mask/jquery.inputmask.js')}}"></script>
<script src="{{ asset('assets/plugins/input-mask/jquery.inputmask.date.extensions.js')}}"></script>
<script src="{{ asset('assets/plugins/input-mask/jquery.inputmask.extensions.js')}}"></script>

<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="{{ asset('js/batch.js') }}"></script>
@endSection
