<?php

class EstadisticasOcupacionController extends BaseController {

	
	public function index(){

		//$eventos_filtrados = array();
		

		//ordernados por fechaEvento y horaIncio
		//$eventosMac1 = Evento::where('recurso_id','=','4')->where('fechaEvento','>=','2019-09-01')->orderBy('fechaEvento')->orderBy('horaInicio')->get();

		$timestamp_fecha_inicio_curso = strtotime('2019-09-23');
		$timestamp_fecha_vacaciones_navidad = strtotime('2019-12-23');

		
		$eventosMac1 = Evento::where('recurso_id','=','4')->where('fechaEvento','>=',strftime('%Y-%m-%d',$timestamp_fecha_inicio_curso))->where('fechaEvento','<=',strftime('%Y-%m-%d',$timestamp_fecha_vacaciones_navidad))->orderBy('fechaEvento')->orderBy('horaInicio')->get();
		/*->filter(function($evento) {
				
				return ( 1 == $evento->dia);
		}*/

		$horas = array(	'lunes' => 0,
						'martes' => 0,
						'miercoles' => 0,
						'jueves' => 0,
						'viernes' => 0,
						'sabado' => 0,
						'domingo' => 0,
					);

		$eventosMac1->each(function($evento) use(&$horas){

				switch ($evento->dia) {
					case 1:
						$horas['lunes'] = $horas['lunes'] + Date::diffHours($evento->horaInicio,$evento->horaFin);
						break;
					case 2:
						$horas['martes'] = $horas['martes'] + Date::diffHours($evento->horaInicio,$evento->horaFin);
						break;
					case 3:
						$horas['miercoles'] = $horas['miercoles'] + Date::diffHours($evento->horaInicio,$evento->horaFin);
						break;
					case 4:
						$horas['jueves'] = $horas['jueves'] + Date::diffHours($evento->horaInicio,$evento->horaFin);
						break;
					case 5:
						$horas['viernes'] = $horas['viernes'] + Date::diffHours($evento->horaInicio,$evento->horaFin);
						break;
					case 6:
						# code...
						break;
					case 7:
						# code...
						break;
					default:
						break;
				}
		});

		$franjasHorarias = array( '08:30-09:30' => 0,
								  '09:30-10:30' => 0,
								  '10:30-11:30'	=> 0,
								  '11:30-12:30'	=> 0,
								  '12:30-13:30'	=> 0,
								  '13:30-14:30'	=> 0,
								  '14:30-15:30'	=> 0,
								  '15:30-16:30'	=> 0,
								  '16:30-17:30'	=> 0,
								  '17:30-18:30'	=> 0,
								  '18:30-19:30'	=> 0,
								  '19:30-20:30'	=> 0,
								  '20:30-21:30'	=> 0,
							);
		$fhBydias = array(	'lunes' => $franjasHorarias,
							'martes' => $franjasHorarias,
							'miercoles' => $franjasHorarias,
							'jueves' => $franjasHorarias,
							'viernes' => $franjasHorarias,
							'sabado' => $franjasHorarias,
							'domingo' => $franjasHorarias,
					);

		$eventosMac1->each(function($evento) use(&$franjasHorarias){

			$h_inicio = strftime('%H:%M',strtotime($evento->horaInicio));
			$h_fin = strftime('%H:%M',strtotime($evento->horaInicio) + 3600);

			$franjasHorarias[$h_inicio.'-'.$h_fin] = $franjasHorarias[$h_inicio.'-'.$h_fin] + 3600; //3600sg => 1h
		});

		$eventosMac1->each(function($evento) use(&$fhBydias){
			
			$h_inicio = strftime('%H:%M',strtotime($evento->horaInicio));
			$h_fin = strftime('%H:%M',strtotime($evento->horaInicio) + 3600);

			switch ($evento->dia) {
					
				case 1:
					$fhBydias['lunes'][$h_inicio.'-'.$h_fin] = $fhBydias['lunes'][$h_inicio.'-'.$h_fin] + 3600;
					break;
				case 2:
					$fhBydias['martes'][$h_inicio.'-'.$h_fin] = $fhBydias['martes'][$h_inicio.'-'.$h_fin] + 3600;
					break;
				case 3:
					$fhBydias['miercoles'][$h_inicio.'-'.$h_fin] = $fhBydias['miercoles'][$h_inicio.'-'.$h_fin] + 3600;
					break;
				case 4:
					$fhBydias['jueves'][$h_inicio.'-'.$h_fin] = $fhBydias['jueves'][$h_inicio.'-'.$h_fin] + 3600;
					break;
				case 5:
					$fhBydias['viernes'][$h_inicio.'-'.$h_fin] = $fhBydias['viernes'][$h_inicio.'-'.$h_fin] + 3600;
					break;
				case 6:
					# code...
					break;
				case 7:
					# code...
					break;
				default:
					break;
			}
		});

		return View::make('estadisticas.index',compact('eventosMac1','horas','timestamp_fecha_inicio_curso','franjasHorarias','fhBydias'));
	}
}