@foreach ($sanciones as $sancion)
	
	<ul>Sanción
		<li> <b>Fecha fin: </b>{{ $sancion->f_fin }} </li>
		<li> <b>Motivo: </b><br />{{ $sancion->motivo }} </li>
	</ul>
@endforeach