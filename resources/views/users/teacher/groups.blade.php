@extends('layouts.app')

@section('content')
    <h4>Mis grupos</h4>
    <list-groups :user="{{auth()->user()}}"></list-groups>
@endsection