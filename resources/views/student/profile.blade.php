@extends('layouts.layout')

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1><span class="fa fa-user"></span> Profile</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/student">Student</a></li>
              <li class="breadcrumb-item active">Profile</li>
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
          <h3 class="card-title">User Details</h3>

          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
              <i class="fa fa-minus"></i></button>
            <button type="button" class="btn btn-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
              <i class="fa fa-times"></i></button>
          </div>
        </div>
        <form enctype="multipart/form-data" method="POST" class="profile_form">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                  <div class="container">
                      <img src='{{ (Auth::user()->avatar) ? "/images/profile_photos/".Auth::user()->avatar : "https://i.stack.imgur.com/34AD2.jpg"  }}'  alt="Avatar" class="image-profile  img-circle img-photo" >
                          <div class="overlay change_photo">
                              <div class="text ">Change Photo</div>
                          </div>
                          <input type="file" class="image" hidden name="image">
                      </div>
                  </div>
                  <div class="col-md-9 vl">
                      <div class="row">
                          <div class="col-md-4">
                              <div class="form-group">
                                  <label>First Name</label>
                                  <input type="text" name="first_name" class="form-control profile-text" placeholder="First Name" value="{{ Auth::user()->first_name }}">
                              </div>
                          </div>
                          <div class="col-md-4">
                              <div class="form-group">
                                  <label>Middle Name</label>
                                  <input type="text" name="middle_name" class="form-control profile-text" placeholder="Middle Name" value="{{ Auth::user()->middle_name }}">
                              </div>
                          </div>
                          <div class="col-md-4">
                              <div class="form-group">
                                  <label>Last Name</label>
                                  <input type="text" name="last_name" class="form-control profile-text" placeholder="Last Name" value="{{ Auth::user()->last_name }}">
                              </div>
                          </div>
                          <div class="col-md-4">
                              <div class="form-group">
                                  <label>Email</label>
                                  <input type="text"  name="email" class="form-control profile-text" placeholder="Email" value="{{ Auth::user()->email }}">
                              </div>
                          </div>
                          @if(isset(Auth::user()->batch_id))
                            <div class="col-md-4">
                              <div class="form-group">
                                  <label>Batch</label>
                                  <input type="text" class="form-control profile-text" placeholder="Batch" value="{{ Auth::user()->batch->batch_name }}" readonly>
                              </div>
                          </div>
                          @endif
                      </div>
                  </div>
            </div>

        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-primary pull-right">Update Profile</button>
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
  <script src="/js/app.js"></script>
  @endsection