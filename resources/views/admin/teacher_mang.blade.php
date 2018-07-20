@extends('admin.master')

@section('page_title','Teacher Management')

@section('stylesheet')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('AdminLTE/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endsection

@section('content')

    <div class="row">
        <div class="col-xs-12">
            @if(session('success'))
                <div class="alert alert-success alert-dismissable">
                    {{ session('success') }}
                </div>
            @endif



            @if(session('error'))
                <div class="alert alert-danger alert-dismissable">
                    {{ session('error') }}
                </div>
            @endif

            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Teacher Management</h3>
                    <a href="{{ route('get_ass_teacher') }}" class="btn btn-primary pull-right">Add Teacher</a>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="teacher_leave" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>Sr. No</th>
                            <th>Teacher Name</th>
                            <th>Email</th>
                            <th>Contact No.</th>
                            <th>Address</th>
                            <th>Option</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>Sr. No</th>
                            <th>Teacher Name</th>
                            <th>Email</th>
                            <th>Contact No.</th>
                            <th>Address</th>
                            <th>Option</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
    </div>

@endsection

@section('script')
    <!-- DataTables -->
    <script src="{{ asset('AdminLTE/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('AdminLTE/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>

    <script>
        $(function () {
            $('#teacher_leave').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax":{
                    "url": "{{ route('admin.get_all_teacher') }}",
                    "dataType": "json",
                    "type": "POST",
                    "data":{ _token: "{{csrf_token()}}"}
                },
                "columns": [
                    { "data": "id" },
                    { "data": "name" },
                    { "data": "email"},
                    { "data": "contact_no" },
                    { "data": "address" },
                    { "data": "options" },
                ]
            })
        })
    </script>
@endsection
