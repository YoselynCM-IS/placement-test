@extends('layouts.app')

@section('content')
    <h4>Resultados</h4>
    <list-results :user="{{auth()->user()}}" :registers="{{$groups}}"></list-results>
@endsection