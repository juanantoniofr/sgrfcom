@foreach ($sanciones as $sancion)
	
	<ul>
		<li> <b>Fecha fin: </b>{{ $sancion->f_fin }} </li>
		<li> <b>Motivo: </b><br />{{ $sancion->motivo }} </li>
	</ul>
@endforeach