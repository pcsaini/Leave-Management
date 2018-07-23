@extends('admin.master')

@section('page_title','Admin | Add Student')

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
                    <h3>Add Stduent</h3>
                </div>
                <form role="form" method="POST" action="{{ route('admin.add_student') }}">
                    @csrf

                    @if($errors->has('add_leave'))
                        <label class="text-danger">{{ $errors->first('add_leave') }}</label>
                    @endif
                    <div class="box-body">
                        <div class="form-group">
                            <label for="name">Student Name</label>
                            <input type="text" class="form-control" id="name" placeholder="Enter Student Name" name="name">
                            @if($errors->has('name'))
                                <label class="text-danger">{{ $errors->first('name') }}</label>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="father_name">Father Name</label>
                            <input type="text" class="form-control" id="father_name" placeholder="Enter Father Name" name="father_name">
                            @if($errors->has('father_name'))
                                <label class="text-danger">{{ $errors->first('father_name') }}</label>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" placeholder="Enter Email Address" name="email">
                            @if($errors->has('email'))
                                <label class="text-danger">{{ $errors->first('email') }}</label>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" placeholder="Enter Password" name="password">
                            @if($errors->has('password'))
                                <label class="text-danger">{{ $errors->first('password') }}</label>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="class">Class</label>
                            <input type="number" class="form-control" id="class" placeholder="Enter Class" name="class">
                            @if($errors->has('class'))
                                <label class="text-danger">{{ $errors->first('class') }}</label>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="contact_no">Contact Number</label>
                            <input type="number" class="form-control" id="contact_no" placeholder="Enter Contact Number" name="contact_no">
                            @if($errors->has('contact_no'))
                                <label class="text-danger">{{ $errors->first('contact_no') }}</label>
                            @endif
                        </div>
                        <div class="form-group">
                            <label>Address</label>
                            <textarea class="form-control" rows="3" placeholder="Enter Address" name="address"></textarea>
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