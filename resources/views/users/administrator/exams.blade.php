@extends('layouts.app')

@section('content')
    <list-exams :registers="{{$schools}}" :role="{{auth()->user()->role}}"></list-exams>
@endsection