@extends('admin.master')

@section('page_title','Student | Add Leave')

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
                <form role="form" method="POST" action="{{ route('student.add_leave') }}">
                    @csrf

                    @if($errors->has('add_leave'))
                        <label class="text-danger">{{ $errors->first('add_leave') }}</label>
                    @endif
                    <div class="box-body">
                        <div class="form-group">
                            <label for="leave_reason">Leave Reason</label>
                            <input type="text" class="form-control" id="leave_reason" placeholder="Enter Reason for Leave" name="leave_reason">
                            @if($errors->has('leave_reason'))
                                <label class="text-danger">{{ $errors->first('leave_reason') }}</label>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="leave_to">Leave To</label>
                            <select id="leave_to" class="form-control select2" style="width: 100%;" name="leave_to">
                                <option selected="selected" disabled>Select Any One</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('leave_to'))
                                <label class="text-danger">{{ $errors->first('leave_to') }}</label>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="leave_range">Leave Range:</label>

                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control pull-right" id="leave_range" name="leave_range">
                            </div>
                            @if($errors->has('leave_start') OR $errors->has('leave_end'))
                                <label class="text-danger">{{ $errors->first('leave_start').','. $errors->first('leave_end')}}</label>
                            @endif
                            <!-- /.input group -->
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" rows="3" placeholder="Enter Description" name="leave_description"></textarea>
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
        $('#leave_start').datepicker();
        $('#leave_end').datepicker();
        $('#leave_range').daterangepicker();
    </script>

@endsection