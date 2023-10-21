@extends('layouts.app')

@section('content')
    <list-teachers :role_id="{{auth()->user()->role_id}}"></list-teachers>
@endsection