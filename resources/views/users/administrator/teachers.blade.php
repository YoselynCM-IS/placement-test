@extends('layouts.app')

@section('content')
    <list-teachers :registers="{{$schools}}"></list-teachers>
@endsection