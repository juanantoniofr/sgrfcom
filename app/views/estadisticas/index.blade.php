@extends('layout')

@section('head')
<style>
  .container{margin-top:10px;}
</style>
@stop
@section('title')
    SGR: formulario de contacto
@stop

@section('content')
<div class="container">

	<div class = "row">
		<table style = "table-layout: fixed;width: 100%;">
			<tr>
				<td>Dia</td>
				@foreach($franjasHorarias as $key => $value)
					<td>{{ $key }}</td>
				@endforeach

			</tr>
		@foreach($fhBydias as $dia => $fh)
				
			<tr >
				<td> {{ $dia }} </td>
				@foreach($fh as $key => $dato)
					<td> {{ $dato/3600 }} </td>
				@endforeach
			</tr>
		@endforeach
		</table>
	</div>

	<div class = "row">
		<ul>
			@foreach($franjasHorarias as $key => $dato)

				<li> {{ $key }}: {{ $dato/3600 }} </li>
			@endforeach
		</ul>
	</div>

	<div class = "row">
		<ul>
			@foreach($horas as $key => $dato)

				<li> {{ $key }}: {{ $dato }} </li>
			@endforeach
		</ul>
	</div>

	
	<div class = "row">
		<p>{{ $timestamp_fecha_inicio_curso }}</p>
		<p>{{ strftime('%Y-%m-%d',$timestamp_fecha_inicio_curso) }}</p>
		<ul>
		
		@foreach($eventosMac1 as $evento)

			<li> {{ $evento->titulo }} <small>({{ $evento->fechaEvento }}, {{ $evento->horaInicio }}, {{ $evento->horaFin }}, {{ $evento->dia}} )</small> </li>
		@endforeach
		</ul>
	</div>

	<div class="alert-info" role="alert">
		<ul>
			<li>Horas lectivas por día: 12. (No cuento de 14:30 - 15:30)</li>
			<li>1 nov --> Viernes, 6 dic --> viernes, 28 de feb --> viernes, 1 de may --> viernes </li>
			<li>9 dic --> lunes</li>
			<li>29 abr --> miércoles </li>
			<li>11 Jun --> jueves </li>
		</ul>
		<div>
			Primer Cuatrimestre:<br />
			<h3>TÍTULOS DE GRADO:</h3>
			<p>Del 23 de septiembre de 2019 al 17 de enero de 2020 (15 Semanas Lectivas)</p>
			<h3>TÍTULOS DE MASTER:</h3>
			<p>Del 21 de octubre de 2019 al 14 de febrero de 2020 (15 Semanas Lectivas)</p>
			Segundo Cuatrimestre:<br />
			<h3>TÍTULOS DE GRADO:</h3>
			<p>Del 10 de febrero al 5 de junio de 2020 (15 Semanas Lectivas)</p>
			<h3>TÍTULOS DE MASTER:</h3>
			<p>Del 24 de febrero al 19 de junio de 2020 (15 Semanas Lectivas)</p>
		</div>
	</div>
</div><!-- /.container -->
@stop
