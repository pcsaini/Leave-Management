@extends('admin.master')

@section('page_title','Student | Add Student')

@section('content')

    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3>Edit Student</h3>
                </div>

                <form role="form" method="POST" action="{{ route('student.edit_profile',$student->user_id) }}">
                    @csrf

                    @if(session('error'))
                        <label class="text-danger">{{ session('error') }}</label>
                    @endif
                    <div class="box-body">
                        <div class="form-group">
                            <label for="name">Student Name</label>
                            <input type="text" class="form-control" id="name" placeholder="Enter Student Name" name="name" value="{{ old('name') == '' ? $student->name : old('name') }}">
                            @if($errors->has('name'))
                                <label class="text-danger">{{ $errors->first('name') }}</label>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="father_name">Father Name</label>
                            <input type="text" class="form-control" id="father_name" placeholder="Enter Father Name" name="father_name" value="{{ old('father_name') == '' ? $student->father_name : old('father_name') }}">
                            @if($errors->has('father_name'))
                                <label class="text-danger">{{ $errors->first('father_name') }}</label>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" placeholder="Enter Email Address" name="email" value="{{ old('email') == '' ? $student->email : old('email') }}" disabled>
                            @if($errors->has('email'))
                                <label class="text-danger">{{ $errors->first('email') }}</label>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="class">Class</label>
                            <input type="number" class="form-control" id="class" placeholder="Enter Class" name="class" value="{{ old('class') == '' ? $student->class : old('class') }}">
                            @if($errors->has('class'))
                                <label class="text-danger">{{ $errors->first('class') }}</label>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="contact_no">Contact Number</label>
                            <input type="number" class="form-control" id="contact_no" placeholder="Enter Contact Number" name="contact_no" value="{{ old('contact_no') == '' ? $student->contact_no : old('contact_no') }}">
                            @if($errors->has('contact_no'))
                                <label class="text-danger">{{ $errors->first('contact_no') }}</label>
                            @endif
                        </div>
                        <div class="form-group">
                            <label>Address</label>
                            <textarea class="form-control" rows="3" placeholder="Enter Address" name="address">{{ old('address') == '' ? $student->address : old('address') }}</textarea>
                        </div>


                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                </form>
            </div>
        </div>
    </div>

@endsection