@extends('layouts.app')

@section('content')
    <assigned-exams :exams="{{$exams}}"></assigned-exams>
@endsection