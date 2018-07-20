@extends('admin.master')

@section('page_title','Student Leave Management')

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
                    <h3 class="box-title">Stduent Leave Management</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="teacher_leave" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>Sr. No</th>
                            <th>Student Name</th>
                            <th>Leave Reason</th>
                            <th>Leave Description</th>
                            <th>Leave Start</th>
                            <th>Leave End</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>Sr. No</th>
                            <th>Student Name</th>
                            <th>Leave Reason</th>
                            <th>Leave Description</th>
                            <th>Leave Start</th>
                            <th>Leave End</th>
                            <th>Status</th>
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
                    "url": "{{ route('teacher.get_all_student_leave') }}",
                    "dataType": "json",
                    "type": "POST",
                    "data":{ _token: "{{csrf_token()}}"}
                },
                "columns": [
                    { "data": "id" },
                    { "data": "name" },
                    { "data": "leave_reason" },
                    { "data": "leave_description" },
                    { "data": "leave_start" },
                    { "data": "leave_end" },
                    { "data": "status" },
                ]
            })
        })
    </script>
@endsection
