@extends('layouts.app')

@section('content')
    <gral-details-exam :exam="{{$datos}}"></gral-details-exam>
@endsection