@extends('layouts.layout')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.css') }}">
@endSection

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1><span class="fa fa-users"></span> User Management</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Users Managment</li>
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
          <h3 class="card-title">List of Users</h3>

          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
              <i class="fa fa-minus"></i></button>
            <button type="button" class="btn btn-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
              <i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                <button class="btn btn-primary pull-right" data-toggle="modal" data-target="#userview_modal"> <span class="fa fa-user-plus">Add</span></button>

                </div>
                <br>
                <br>
            </div>
            <div class="table-responsive p-0">
              <table id="userstbl" class="table table-bordered table-hover" style="width: 100%">
                  <thead>
                  <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Register Date</th>
                    <th>Action</th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($users as $user)
                      <tr>
                          <td>{{ $user->first_name." ".$user->last_name }}</td>
                          <td>{{ $user->email }}
                          </td>
                          <td>{{ $user->created_at }}</td>
                          <td><button class="btn btn-xs btn-primary" onclick="viewUser({{$user->id}})"><span class="fa fa-eye"></span></button></td>
                      </tr>
                  @endforeach
                  </tfoot>
              </table>
            </div>
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

   <!-- modal -->
   <div class="modal fade" id="userview_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" style="z-index: 2000;">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">View User</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="userform">
      <input type="hidden" name="id">
      <div class="modal-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="firstname">First Name</label>
                    <input type="text" name="firstname" class="form-control" placeholder="First Name" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="lastname">Last Name</label>
                    <input type="text" name="lastname" class="form-control" placeholder="Last Name" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="batchid">Batch ID</label>
                    <input type="text" name="batch" class="form-control" placeholder="Batch ID" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="password_confirmation">Confirm Password:</label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Password" required>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
      </form>
    </div>
  </div>
</div>
  @endsection

  @section('js')
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.js')}}"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="{{ asset('js/admin.js') }}"></script>
    
  @endSection