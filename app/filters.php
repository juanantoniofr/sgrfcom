<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
	//
});


App::after(function($request, $response)
{
	//
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/
/*		El filtro req2 permite bloquear el acceso al calendario para reservar a los alumnos con el número de horas máximo a la semana (12h) agotado
*/

//Developed
Route::filter('auth', function()
{
	$user = User::find('30');
	Auth::login($user);
	//if (Auth::guest()) return Redirect::to(route('loginsso'));
	
});
//Producction
/*Route::filter('auth', function()
{
	
    if (!Cas::isAuthenticated() || !Auth::check()) 
    	if (Request::ajax()) {

    		return Response::make('Necesitas iniciar sesión de nuevo. Por favor, recarga la página', 401);
    	}
    	else 
    		
    		return Redirect::to(route('wellcome'));
});*/

//Comprueba si la petición se realizó por ajax y el usaurio está autenticado
Route::filter('ajax_check',function(){
		
	if(!Request::ajax()) return Redirect::to(route('wellcome'));
	
});

//Comprobar si el usuario autentivcado tiene privilegios para realizar la acción requerida
Route::filter('capacidad',function($ruta,$peticion,$capacidad,$redirect) {
	
	$roles  = explode("-",$capacidad);
	if (!in_array(Auth::user()->capacidad, $roles)){
		$msg = 'Privilegios insuficientes';
		$title = 'Error de acceso';
		return Redirect::to($redirect)->with(compact('title','msg'));
	}
	if (strtotime(Auth::user()->caducidad) < strtotime('now')){
		$msg = 'Cuenta caducada';
		$title = 'Error de acceso';
		return Redirect::to($redirect)->with(compact('title','msg'));
	}
	if (Auth::user()->estado == 0){
		$msg = 'Cuenta desactivada';
		$title = 'Error de acceso';
		return Redirect::to($redirect)->with(compact('title','msg'));
	}
});
//Comprobar si el sistema permite a los usarios registrar reservas.

Route::filter('inicioCurso',function(){

	//if (ACL::isUser() || ACL::isAvanceUser()){
	if (ACL::isUser() && Auth::user()->username != 'juanafr'){
		$hoy = strtotime('today');
		$diaInicio = strtotime(Config::get('options.inicio_gestiondesatendida'));
		
		if ($diaInicio > $hoy) {
			$title = 'Acceso limitado';
			$msg = '<p align="center">El sistema le permitirá el acceso una vez registrado el Plan Docente de las títulaciones oficiales del Centro, <br />
			<b>Fecha prevista: ' . date('d-m-Y',strtotime(Config::get('options.inicio_gestiondesatendida'))) ."</b></p>";
			return Redirect::to('loginerror')->with(compact('title','msg'));
		}
	}

	if (	ACL::isAvanceUser() 
		|| 	ACL::isTecnico() 
		&& 	!in_array(Auth::user()->username, Config::get('options.userexcluded')) 
		){
		$hoy = strtotime('today');
		$diaInicio = strtotime(Config::get('options.inicio_titulospropios'));
		
		if ($diaInicio > $hoy) {
			$title = 'Acceso limitado';
			$msg = '<p align="center">Una vez completada la ocupación destinada a la docencia oficial del curso 2019-2020 en el sistema informático de reservas de espacios y medios de la Facultad de Comunicación, <b>del día 23 de septiembre hasta el 30 de septiembre</b> se abrirá para los <b>títulos propios, seminarios y encuentros del PDI, PAS y Delegación de Alumnos de la Facultad de Comunicación</b>.<br /> A partir del día '
			 	. date('d-m-Y',strtotime(Config::get('options.inicio_gestiondesatendida'))) .
			 	', el sistema se abrirá para toda la Comunidad del Centro.<br /></p>';
			return Redirect::to('loginerror')->with(compact('title','msg'));
		}
	}
});
//Comprobar si el sistema permite a los usarios registrar reservas.

/*Route::filter('inicioCurso',function(){

	//if (ACL::isUser() || ACL::isAvanceUser()){
	if (ACL::isUser()){
		$hoy = strtotime('today');
		$diaInicio = strtotime(Config::get('options.inicio_gestiondesatendida'));
		if ($diaInicio > $hoy) return Redirect::to('user/msg');
	}
});*/

Route::filter('auth.basic', function()
{
	return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to(ACL::getHome());
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() != Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});



