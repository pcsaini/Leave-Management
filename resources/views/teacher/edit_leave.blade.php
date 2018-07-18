@extends('admin.master')

@section('page_title','Teacher')

@section('stylesheet')
    <!-- daterange picker -->
    <link rel="stylesheet" href="{{ asset('AdminLTE/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="{{ asset('AdminLTE/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">

@endsection

@section('content')

    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3>Add Leave</h3>
                </div>
                <form role="form" method="POST" action="">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="leave_reason">Leave Reason</label>
                            <input type="email" class="form-control" id="leave_reason" placeholder="Enter Reason for Leave" name="leave_reason">
                        </div>
                        <div class="form-group">
                            <label for="leave_range">Date range:</label>

                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control pull-right" id="leave_range" name="leave_range">
                            </div>
                            <!-- /.input group -->
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" rows="3" placeholder="Enter Description"></textarea>
                        </div>
                    </div>

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <!-- date-range-picker -->
    <script src="{{ asset('AdminLTE/bower_components/moment/min/moment.min.js') }}"></script>
    <script src="{{ asset('AdminLTE/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <!-- bootstrap datepicker -->
    <script src="{{ asset('AdminLTE/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>

    <script>
        $('#leave_range').daterangepicker()
    </script>

@endsection