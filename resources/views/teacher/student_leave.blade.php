@extends('admin.master')

@section('page_title','Dashboard')

@section('content')

{{ Route::currentRouteName() }}

@endsection