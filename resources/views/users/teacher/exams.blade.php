@extends('layouts.app')

@section('content')
    <my-exams :registers="{{$exams}}" :role="{{auth()->user()->role}}"></my-exams>
@endsection