@extends('layouts.layout')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/select2/select2.min.css') }}">

<!-- <link rel="stylesheet" href="{{ asset('assets/plugins/daterangepicker/daterangepicker-bs3.css') }}"> -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.6/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="{{ asset('assets/plugins/ekko-lightbox/ekko-lightbox.css')}}">

<style type="text/css">
    .input_image[type=file]{
      display: inline;
    }
    #image_preview img{
      width: 200px;
      padding: 5px;
    }
  </style>
@endSection

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Exam Management</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
              <li class="breadcrumb-item active">Exam Management</li>
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
                    <div class="row pull-right">
                      <div class="form-group">
                        <button class="btn btn-primary add_exam_main"><span class="fa fa-plus"></span> Add Exam</button>
                        <button class="btn btn-info  add_exam"><span class="fa fa-plus"></span> Add Multiple Choice</button>  
                        <button class="btn btn-info  btn_exam_image"><span class="fa fa-image"></span> Add Image Exam</button>
                      </div>
                  </div>
                  <div class="card-body">
                    <div class="col-md-8 offset-md-2">
                      <br>
                      <br>
                      <div class="table-responsive">
                        <table id="tableExam" class="table table-bordered table-hover">
                          <thead>
                            <tr>
                              <th>Exam Title</th>
                              <th style="width: 200px">Action</th>
                            </tr>
                          </thead>
                          <tbody>
                              @foreach($exams as $exam)
                                <tr>
                                  <td>{{ $exam->title }}</td>
                                  <td>
                                    <button class="btn btn-sm btn-info" onclick="viewQuestions('{{ $exam->id }}', 1, '{{ $exam->title }}')">View Questions</button>
                                    @if($exam->taken < 1)
                                      <button class="btn btn-sm btn-danger" onclick="deleteExam('{{ $exam->id }}')" title="Delete"><i class="fa fa-trash"></i></button>
                                    @endif
                                  </td>
                                </tr>
                              @endforeach
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                  <!-- /.card-body -->
              </div>
          </div>
      </div>
    </section>
</div>

<!-- modal: main exam -->
<div class="modal fade" id="exammainform_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" style="z-index: 2000;">
  <div class="modal-dialog modal-dialog-centered modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Exam Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="exammainform">
        <div class="modal-body">
          <input type="hidden" name="id">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                  <label>Exam Title</label>
                  <input type="text" name="exam_title" class="form-control" placeholder="Enter Exam Title" autocomplete="off" required>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary" id="btn_save_exam"> <span class="fa fa-save"></span> Save</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </form>
    </div>
  </div>
</div>
</div>

<!-- modal: exam details -->
<div class="modal fade" id="examform_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" style="z-index: 2000;">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Exam Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <form id="viewexamtitle">
          <div class="col-md-12">
            <div class="form-group">
              <input id="exam_title_view" class="form-control" type="text" readonly>
            </div>
          </div>
        </form>
        <form id="examform">
          <div class="col-md-12">
            <div class="form-group">
            <label>Select Exam:</label>
            <select name="exam_id" id="exam_id" class="form-control">
              <option value="" selected disabled >Select Exam</option>
                @foreach($exams as $exam)
                  @if($exam->taken < 1)
                    <option value="{{ $exam->id }}" >{{ $exam->title }}</option>
                  @endif
                @endforeach
            </select>
            </div>
          </div>
          <div class="col-md-12">
            <div class="form-group">
              <input type="hidden" id="batch_id_upload" name="batch_id_upload">
              @csrf
              <div class="row">
                <div class="col-md-9">
                  <input type="file" id="file" name="file" class="form-control" required>
                    <small class="text-danger">*Upload via Excel 
                      <a href="/assets/template/upload_questionaires.xlsx">Download Template </a> 
                    </small> 
                </div>
                <div class="col-md-3">
                  <button type="submit" class="btn btn-primary" id="btn_upload"><span class="fa fa-upload"></span> Upload Questionaires</button>
                </div>
              </div>
            </div>
          </div>
        </form>

        <hr>
        <div class="row">
          <div class="col-md-12">
            <div class="table-responsive">
              <table id="tableQuestions" class="table table-bordered table-hover">
                  <thead>
                      <tr>
                        <th>#</th>
                        <th>Question</th>
                        <th>Choices</th>
                        <th>Answer</th>
                        <th>Points</th>
                      </tr>
                  </thead>
                </table>
              </div>
          </div>
        </div>
        <div class="img-div">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
    </div>
  </div>
</div>
</div>

<!-- image modal -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false" style="z-index: 2000;">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header title-header-red">
        <h5 class="modal-title" id="imageModalLabel">Exam Images</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <form id="imageForm"  method="post" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="row">
        <div class="col-md-12">
          <div class="form-group">
          <label>Select Exam:</label>
          <select name="image_exam_id" id="image_exam_id" class="form-control">
            <option value="" selected disabled >Select Exam</option>
            @foreach($exams as $exam)
            @if($exam->taken < 1)
            <option value="{{ $exam->id }}" >{{ $exam->title }}</option>
            @endif
            @endforeach
          </select>
          </div>
        </div>
        <div class="col-md-9">
          <input required type="file" id="uploadFile"  class="form-control" name="uploadFile[]" multiple/>
        </div>
        </div>
      <br/>
      <div class="row" id="image_preview">
      </div>
     <div class="label_div" hidden>
        <hr>
        <div class="form-group">
          <label>Label Count:</label>
          <input type="number" class="form-control"  style="width:100px !important" id="count_label" name="count_label" required> 
        </div>
        <hr>
        <div class="col-md-12">
                  <div class="row">
                    <label for="labelName" class="col-sm-10 col-form-label">&nbsp;</label>
                    <label for="labelName" class="col-sm-2 col-form-label">Label Points:</label>
                  </div>
          </div>
          <div class="row label_count">
          
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <input type="submit" class="btn btn-success" name='submitImage' value="Upload Image"/>
      </div>
      </form>
    </div>
  </div>
</div>




<div class="modal fade" id="addImageModal" tabindex="-1" role="dialog" aria-labelledby="addImageModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false"  style="z-index: 1 !important">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header title-header-red">
        <h5 class="modal-title" id="addImageModalLabel">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">  
      <div class="row">
        <div class="col-md-12">
          <div class="row">
            <div class="col-md-12 " >
              <div class="row img_div">
                <div class="col-md-4">
                <img src="https://i.stack.imgur.com/34AD2.jpg" alt="exam Image" style="height: 250px !important;">
                </div>
                <div class="col-md-4">
                  <img src="https://i.stack.imgur.com/34AD2.jpg" alt="exam Image" style="height: 250px !important;">
                </div>
              </div>
            </div>
            <div class="col-md-12">
             <hr>
                <div class="form-group">
                  <label>Add Label Count</label>
                  <input type="number" class="form-control" style="width: 100px !important">
                </div>
            </div>
          </div>
        </div>
      </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
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
<script src="{{asset('assets/plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="{{ asset('assets/plugins/ekko-lightbox/ekko-lightbox.min.js')}}"></script>
<script src="{{ asset('js/exam.js') }}"></script>
@endSection
