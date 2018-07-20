@extends('admin.master')

@section('page_title','Dashboard')

@section('content')

    <div class="row">
        <div class="col-md-offset-4 col-md-4">

            <!-- Profile Image -->
            <div class="box box-primary">
                <div class="box-body box-profile">
                    <img class="profile-user-img img-responsive img-circle" src="{{ asset('AdminLTE/dist/img/user4-128x128.jpg') }}" alt="User profile picture">

                    <h3 class="profile-username text-center">{{ $user->name }}</h3>

                    <p class="text-muted text-center">Admin</p>
                    <p class="text-muted text-center">{{ $user->email }}</p>

                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>

@endsection