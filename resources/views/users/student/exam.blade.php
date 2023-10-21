@extends('layouts.app')

@section('content')
    <check-exam :exam="{{$exam}}" :information="{{$information}}"></check-exam>
@endsection