@extends('layouts.app')

@section('content')
    <reagents-list-component 
        :registers="{{$levels}}" :role="{{auth()->user()->role}}">
    </reagents-list-component>
@endsection