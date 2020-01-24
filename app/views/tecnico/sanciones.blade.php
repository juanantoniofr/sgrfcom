@foreach ($sanciones as $sancion)
	
	<ul>Sanción
		<li> <b>Fecha fin: </b>{{ strftime('%d-%m-%Y',strtotime($sancion->f_fin)) }} </li>
		<li> <b>Motivo: </b><br />{{ $sancion->motivo }} </li>
	</ul>
@endforeach