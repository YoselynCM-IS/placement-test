@extends('layouts.app')

@section('content')
    <assigned-exams :registers="{{$exams}}"></assigned-exams>
@endsection