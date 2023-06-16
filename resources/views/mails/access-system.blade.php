@extends('layouts.mail')

@section('content')
	@if($user->role_id == 2)
		<h5>Buen dia profesor(a): {{ $user->name }}</h5>
		<p>Sus datos para acceder al sistema son:</p> 
	@else
		<h5>HOLA {{ $user->name }}</h5>
		<p>Tus datos para acceder al examen de colocación son:</p> 
	@endif
	<ul>
		<li>Correo electrónico: {{ $user->email }}</li>
		<li>Contraseña: {{ $user->view_password }}</li>
	</ul>
	<a href="https://placementtestmajesticeducation.com/login">Acceder al sistema</a>
@endsection