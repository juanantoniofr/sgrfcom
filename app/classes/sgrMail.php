<?php

class sgrMail {
	
	private $mailAdminSGR = 'juanantonio.fr@gmail.com';
	private $subject =	array(	'addEvento'		=> 'Nueva solicitud de reserva',
								'editEvento'	=> 'Modificación de solicitud de reserva',
								'delEvento'		=> 'Eliminación de evento',
								'aprobar'		=> 'Solicitud aprobada',
								'denegar'		=> 'Solicitud denegada',
								'registro'		=> 'Nueva solicitud de registro pendiente',
								'caducada'		=> 'Cuenta caducada',
								'contacto'		=> 'Notificación formulario de contacto',
								'activacion'	=> 'Usuario UVUS activado en SGR (Sistema de Gestión de Reservas fcom)',
								'deshabilita'	=> 'Espacio o medio deshabilitado',
								'habilita'		=> 'Espacio o medio habilitado',
							);

	
	public function notificaDeshabilitaRecurso($id){

		if (empty($id)) return true;

		//Subject 
		$s = date('d-m-Y H:i') .': '.$this->subject['deshabilita'];
		//Notifica a todos los usuarios con reservas futuras en el recurso deshabilitado
		$eventos = Evento::where('recurso_id','=',$id)->get();
		$eventosToMail = $eventos->filter(function($evento){
			return strtotime($evento->fechaEvento) >= strtotime('today');
		});

		foreach ($eventosToMail as $evento) {
			
			$data = array('evento' => serialize($evento));

			if (!empty($evento->userOwn->email)){
				$mailUser = $evento->userOwn->email;
				Mail::queue(array('html'=>'emails.deshabilitaRecurso'),$data,function($m) use ($mailUser,$s){
				$m->to($mailUser)->subject($s);});
			}//fin if		
		}//fin foreach			
	}//fin notificaDeshabilitaRecurso
	
	public function notificaHabilitaRecurso($id){

		if (empty($id)) return true;

		//Subject 
		$s = date('d-m-Y H:i') .': '.$this->subject['habilita'];
		//Notifica a todos los usuarios con reservas futuras en el recurso deshabilitado
		$eventos = Evento::where('recurso_id','=',$id)->get();
		$eventosToMail = $eventos->filter(function($evento){
			return strtotime($evento->fechaEvento) >= strtotime('today');
		});

		foreach ($eventosToMail as $evento) {
			
			$data = array('evento' => serialize($evento));

			if (!empty($evento->userOwn->email)){
				$mailUser = $evento->userOwn->email;
				Mail::queue(array('html'=>'emails.habilitaRecurso'),$data,function($m) use ($mailUser,$s){
				$m->to($mailUser)->subject($s);});
			}//fin if		
		}//fin foreach			
	}//fin notificahabilitaRecurso	

	public function notificaActivacionUVUS($idUser){

		//Subject 
		$s = date('d-m-Y H:i') .': '.$this->subject['activacion'];
		//Notifica solicitante
		$user = User::find($idUser);
		$data = array('user' => serialize($user));

		if (!empty($user)){
			$mailUser = $user->email;
			if ( !empty($mailUser) ){
				Mail::queue(array('html'=>'emails.activaUvus'),$data,function($m) use ($mailUser,$s){
				$m->to($mailUser)->subject($s);
			});
			}		
		}
	}

	public function notificaValidacion($acccion,$evento_id){

		$evento = Evento::where('evento_id','=',$evento_id)->first();//datos de la solicitud de uso
		
		//exclude Aulas de informática
		if ($evento->recursoOwn->id == 2 || $evento->recursoOwn->id == 3 || $evento->recursoOwn->id == 4 || $evento->recursoOwn->id == 5) return true;

		$data = array('evento' => serialize($evento),'validador' => Auth::user()->nombre .' '. Auth::user()->apellidos);
		$s = date('d-m-Y H:i') .': '. $evento->recursoOwn->nombre . ': '. $this->subject[$acccion];
		
		$validadores = User::where('capacidad','=',5)->where('email','!=','')->get(); //Todos los Validadores incluido Auth::user() (validador autenticado)
		//Notifica validadores
		foreach ($validadores as $validador) {
			if ( !empty($validador->email) )
				Mail::queue(array('html' => 'emails.validacion'),$data,function($m) use ($validador,$s){
					$m->to($validador->email)->subject($s);
				});	
		}
		
		//Notifica solicitante
		$mailSolicitante = $evento->userOwn->email;
		if ( !empty($mailSolicitante) ){
			Mail::queue(array('html'=>'emails.validacion'),$data,function($m) use ($mailSolicitante,$s){
				$m->to($mailSolicitante)->subject($s);
			});
		}	
	}//fin notificaValidacion

	//Notifica nuevo evento a los validadores.
	public function notificaNuevoEvento($evento){

		//exclude Aulas de informática
		if ($evento->recursoOwn->id == 2 || $evento->recursoOwn->id == 3 || $evento->recursoOwn->id == 4 || $evento->recursoOwn->id == 5) return true;
		

		$data = array('evento' => serialize($evento),'solicitante' => Auth::user()->nombre .' '. Auth::user()->apellidos);
		
		$s = 	date('d-m-Y H:i') .': '. $this->subject['addEvento'] . ' en ' . $evento->recursoOwn->nombre;
		$validadores = User::where('capacidad','=',5)->where('email','!=','')->get(); //todos  los validadores
		//Notifica validadores
		foreach ($validadores as $validador) {
			if ( !empty($validador->email) )
				Mail::queue(array('html' => 'emails.detalleReserva'),$data,function($m) use ($validador,$s){
					$m->to($validador->email)->subject($s);
				});	
		}


	}//fin notificaNuevoEvento

	public function notificaMe($evento){

		$data = array('evento' => serialize($evento),'solicitante' => Auth::user()->nombre .' '. Auth::user()->apellidos);
		
		$s = 	date('d-m-Y H:i') .': '. $this->subject['addEvento'] . ' en ' . $evento->recursoOwn->nombre;
		Mail::queue(array('html' => 'emails.detalleReserva'),$data,function($m) use($s){
			$m->to('juanantonio.fr@gmail.com')->subject($s);
		});

	}//fin notificaNuevoEvento

	public function notificaSancion($mailto,$motivo,$f_fin){

		//$data = array('evento' => serialize($evento),'solicitante' => Auth::user()->nombre .' '. Auth::user()->apellidos);
		$data = array(	'motivo' 	=> $motivo,
						'f_fin'		=> $f_fin);

		$to = $mailto;				
				
		Mail::queue(array('html' => 'emails.sancion'),$data,function($m)  {
			
			$subject = 	date('d-m-Y H:i') ." SGR Notificación sanción";	
			$m->to('juanantonio.fr@gmail.com')->subject($subject);
		});

		Mail::queue(array('html' => 'emails.sancion'),$data,function($m) use($mailto){
			
			$subject = 	date('d-m-Y H:i') ." SGR Notificación sanción";
			$m->to($mailto)->subject($subject);
		});

	}

	public function notificaSancionDelete($mailto,$motivo,$f_fin){

		//$data = array('evento' => serialize($evento),'solicitante' => Auth::user()->nombre .' '. Auth::user()->apellidos);
		$data = array(	'motivo' 	=> $motivo,
						'f_fin'		=> $f_fin);

		$to = $mailto;				
				
		Mail::queue(array('html' => 'emails.sanciondelete'),$data,function($m)  {
			
			$subject = 	date('d-m-Y H:i') ." SGR Notificación sanción eliminada";	
			$m->to('juanantonio.fr@gmail.com')->subject($subject);
		});

		Mail::queue(array('html' => 'emails.sanciondelete'),$data,function($m) use($mailto){
			
			$subject = 	date('d-m-Y H:i') ." SGR Notificación sanción eliminada";
			$m->to($mailto)->subject($subject);
		});

	}

	public function noticicaDocEmergencias($msg){
		//Notifica solicitante

		$mailSolicitante = Auth::user()->email;
		//$mailSolicitante = 'juanantonio.fr@gmail.com';
		if ( !empty($mailSolicitante) ){
			$s = 'SGR fcom: Instrucciones de seguridad generales y normas de actuación ante emergencias';	
			Mail::queue(array('html'=>'emails.avisoParaUsuario'),array('msg' => $msg),function($m) use ($mailSolicitante,$s){
				$m->to($mailSolicitante)->subject($s);
			});	
		}
	}

	public function notificaEdicionEvento($evento){

		//exclude Aulas de informática
		if ($evento->recursoOwn->id == 2 || $evento->recursoOwn->id == 3 || $evento->recursoOwn->id == 4 || $evento->recursoOwn->id == 5) return true;

		$data = array('evento' => serialize($evento),'solicitante' => Auth::user()->nombre .' '. Auth::user()->apellidos );
		$s = 	date('d-m-Y H:i') .': '. $this->subject['editEvento'] . ' en ' . $evento->recursoOwn->nombre;
		$validadores = User::where('capacidad','=',5)->where('email','!=','')->get(); //todos  los validadores
		//Notifica validadores
		foreach ($validadores as $validador) {
			if ( !empty($validador->email) )
				Mail::queue(array('html' => 'emails.detalleReserva'),$data,function($m) use ($validador,$s){
					$m->to($validador->email)->subject($s);
				});	
		}


	}//fin notificaEdicionEvento
	
	/***
		@param in $user=array('nombre','apellidos','uvus','relacionUSES','colectivo','ubicacion')
		//todos los valores devueltos por sso
	*/
	public function notificaRegistroUser($user){

		$s = date('d-m-Y H:i') .': '.$this->subject['registro'];
		$data = array('user' => serialize($user));
		$mailAdminSGR = $this->mailAdminSGR;
		Mail::queue(array('html' => 'emails.registroNuevoUsuario'),$data,function($m) use($mailAdminSGR,$s){
							$m->to($mailAdminSGR)->subject($s);});
	}

	public function notificaCaducada($user){

		$s = date('d-m-Y H:i') .': '.$this->subject['caducada'];
		$data = array('user' => serialize($user));
		$mailAdminSGR = $this->mailAdminSGR;
		Mail::queue(array('html' => 'emails.cuentacaducada'),$data,function($m) use($mailAdminSGR,$s){
							$m->to($mailAdminSGR)->subject($s);});
	}

	public function notificaContacto($data){

		$s = date('d-m-Y H:i') .': '. $this->subject['contacto'];
		$mailAdminSGR = $this->mailAdminSGR;
		Mail::queue(array('html' => 'emails.contacto'),$data,function($m) use($mailAdminSGR,$s){
							$m->to($mailAdminSGR)->subject($s);});
		
	}//fin function notificaContacto


}//fin sgrMail

?>