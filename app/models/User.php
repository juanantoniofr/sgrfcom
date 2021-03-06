<?php
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface{

	protected $table = 'users';
	public $timestamps = true;
	protected $softDelete = false;
	//protected $hidden = array('password');

	/**
		*
		* Un (1) usuario (con rol técnico) registra Muchas (*) snaciones
		*
	*/
	public function sancionesRegistradas(){

		return $this->hasMany('Sancion','tecnico_id');
	}

	/**
		*
		* Un (1) usuario (con rol alumno) tiene muchas (*) sanciones
		*
	*/
	public function sanciones(){

		return $this->hasMany('Sancion','user_id','id');
	}

	/**
		*
		* Comprueba si tiene sanción con f_fin mayor que hoy 
		*
		* @return true|false
	*/
	public function sancionado(){
		
		$strDBToday = strftime('%Y-%m-%d',strtotime('today'));
		return ( $this->sanciones()->where('f_fin','>=',$strDBToday)->get()->count() > 0 );
	}

	/**
		*
		* devuele f_fin de última sanción 
		*
		* @return true|false
	*/
	public function fFinSancion(){
		
		if ($this->sancionado()){

			$strDBToday = strftime('%Y-%m-%d',strtotime('today'));
			$sancion = $this->sanciones()->where('f_fin', '>=', $strDBToday)->first();
		}
		
		if (!empty($sancion))
			return strftime('%d-%m-%Y',strtotime($sancion->f_fin));
		else
			return strftime('%d-%m-%Y',strtotime('today'));;
	}

	/**
		*
		* devuele sanción activa o vacío 
		*
		* @return Objetc Sancion| empty
	*/
	public function motivoSancion(){
		

		if ($this->sancionado()){

			$strDBToday = strftime('%Y-%m-%d',strtotime('today'));
			$sancion = $this->sanciones()->where('f_fin', '>=', $strDBToday)->first();
			return $sancion->motivo;
		}
		
		return '';
	}

	/**
		*
		* devuele id de la sanción activa o vacío 
		*
		* @return Objetc Sancion| empty
	*/
	public function idSancion(){

		if ($this->sancionado()){

			$strDBToday = strftime('%Y-%m-%d',strtotime('today'));
			$sancion = $this->sanciones()->where('f_fin', '>=', $strDBToday)->first();
			return $sancion->id;
		}
		
		return '';	
	}

	/**
		*
		* True so el usuario puede sancionar 
		*
		* @return Booleano 
	*/
	public function puedeSancionar(){

		if ( $this->username == 'falco' || $this->username == 'pasino' || $this->capacidad == 4)
			return true;

		return false;
	}

	//devuelve los recurso que valida
	public function supervisa()
    {
        return $this->belongsToMany('Recurso');
    }

    //devuelve los eventos del usuario
	public function userEvents(){

		return $this->hasMany('Evento','user_id');
	
	}
	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		//return $this->password;
		return null;
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail()
	{
		return $this->email;
	}

	public function getRememberToken()
	{
	    //return $this->remember_token;
	    return null;
	}

	public function setRememberToken($value)
	{
	   // $this->remember_token = $value;
	}

	public function getRememberTokenName()
	{
	    //return 'remember_token';
	    return null;
	}

	/*
  		* Overrides the method to ignore the remember token.
  	*/
 	public function setAttribute($key, $value)
 	{
	   $isRememberTokenAttribute = $key == $this->getRememberTokenName();
	   if (!$isRememberTokenAttribute)
	   {
	     parent::setAttribute($key, $value);
	   }
	}

	//Validador
    public function isValidador(){
        $isValidador = false;
 
        if (Auth::user()->capacidad == 5) $isValidador = true;
 
        return $isValidador;
    }

	//PDI
    public function isPDI(){
        $isPDI = false;
 
        if (Auth::user()->capacidad == 2) $isPDI = true;
 
        return $isPDI;
    }

    //Supervisor
    public function isSupervisor(){
        $isSupervisor = false;
 
        if (Auth::user()->capacidad == 6) $isSupervisor = true;
 
        return $isSupervisor;
    }
    //Admin
    public function isAdmin(){
        $isAdmin = false;
 
        if (Auth::user()->capacidad == 4) $isAdmin = true;
 
        return $isAdmin;
    }

	//Admin
    public function isAlumn(){
        $isAlumn = false;
 
        if (Auth::user()->capacidad == 1) $isAlumn = true;
 
        return $isAlumn;
    }

	public function caducado(){
		return strtotime($this->caducidad) < strtotime('today');
	}

	public function getRol(){
		
		
		switch ($this->capacidad) {
			case '1':
				return 'Usuario (Alumno)';
			case '2':
				return 'PDI // PAS Administración';
			case '3':
				return 'PAS (técnico MAV)';
			case '4':
				return 'Administrador (SGR)';
			case '5': 
				return 'Validador';
			case '6': 
				return 'Supervisor (E.E Unidad)';	
			default:
				return 'No definido..';
			}
	}

	
	public function getHome(){
		
		switch ($this->capacidad) {
			case '6': //validador
				return route('recursos');
			case '5': //validador
				return route('validadorHome.html');
			case '4': //root
				return route('adminHome.html');
			case '3': //pas - técnico MAV
				return route('tecnicoHome.html');
			case '2': //pdi - pas Administración
				return route('calendarios.html');
			case '1': //alumno
				return route('calendarios.html');
			default:
				return 'No definido..';
			}

	}

	public function dropdownMenu(){
		
		switch ($this->capacidad) {
			case '5': //validador
				return 'validador.dropdown';
			case '4': //root
				return 'admin.dropdown';
			case '3': //pas - técnico 
				return 'tecnico.dropdown';
			case '6': //pas - supervisor
				return 'tecnico.dropdown';
			case '2': //pdi
				return 'emptydropdown';
			case '1': //alumno
				return 'emptydropdown';
			default:
				return 'emptydropdown';
			}
	}

}