@extends('layouts.mail')

@section('content')
	<h5>HOLA {{ $user->name }}</h5>
	<p>Tienes un examen programado para el día <b>{{ $exam->start_date }}</b>.</p>
	<p>El examen estará disponible de <b>{{ $exam->start_time }}</b> a <b>{{ $exam->end_time }}</b>.</p>
	<hr>
	<p>Gracias.</p>
	<a href="https://gentle-chamber-76410.herokuapp.com/login">Acceder al sistema</a>
@endsection