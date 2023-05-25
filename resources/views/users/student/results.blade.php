@extends('layouts.app')

@section('content')
    <details-results :exam="{{$exam}}" :student_id="{{$student->id}}"></details-results>
@endsection