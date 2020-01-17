<?php

class UsersController extends BaseController {
 
  //escritorio admin
  public function home(){
    $veractivados = Input::get('veractivados',0);
    $verdesactivados = Input::get('verdesactivados',0);
    
    
    $notificaciones = Notificacion::where('estado','=','abierta')->orderby('id','desc')->get();
    return View::make('admin.index')->with(compact('notificaciones'))->nest('dropdown','admin.dropdown');
  }

    /**
        * llamada ajax, estable sanción a identificado su Id
        * 
        * @param 
        *
        * @return $respuesta :View::make 
    */

    public function ajaxSancionaUsuario(){

        //Input
        $intIdUser = Input::get('userId','');
        $strMotivoSancion = Input::get('motivoSancion','');
        $strF_fin = Input::get('f_fin','');

        //Ouput
        $resultado = array( 'msg' => '',
                            'exito' => false,
                        );
        //Validate Inputs
        $inputs = array(  'userId' => $intIdUser,
                    'f_fin' => $strF_fin,
                    );
        $rules = array(  'userId' => 'required',
                    'f_fin' => 'required|date_format:d-m-Y',
                );
        $messagesError = array( 'required' => ' El campo <strong>:attribute</strong> es obligatorio.',
                                'date_format' =>  '<strong>Formato de fecha de fin de sanción no válido</strong>. Formato admitido: d-m-Y.');

        $validator = Validator::make($inputs, $rules, $messagesError);
        
        if ($validator->fails()) {
            // The given data did not pass validation
            $messages = $validator->messages();
            foreach ($messages->all('<li>:message</li>') as $message) {
                $resultado['msg'] .= $message;
            }
            $resultado['exito'] = false;

            return $resultado;
        }

        // 
        $resultado['exito'] = true;
        $resultado['msg'] = 'UsuerID = ' . $intIdUser . ', strMotivoSancion = ' . $strMotivoSancion . ', fecha fin = ' . $strF_fin . ', id user login = ' . Auth::user()->id;

        return $resultado;
        

    }

  public function listUsers(){
      
      $sortby = Input::get('sortby','username');
      $order = Input::get('order','asc');
      $search = Input::get('search','');
      $veractivados = Input::get('veractivados',0);
      $verdesactivados = Input::get('verdesactivados',0);
      $colectivo = Input::get('colectivo','');
      $perfil = Input::get('perfil','');
      

      $estados = array('-1');//ver usuarios activos (default)
      $userFilterestado = array();
      if (Input::get('veractivados',0)) {$userFilterestado[] = '1';$veractivados = 1;}
      if (Input::get('verdesactivados',0)) {$userFilterestado[] = '0';$verdesactivados = 1;}
      if (!empty($userFilterestado)) $estados = $userFilterestado;//si usuario filtra --> sobreescribe opciones
      
      $offset = Input::get('offset','15');
      
      //if ($perfil == 0) $perfil = '';

      $usuarios = User::where('username','like',"%$search%")->whereIn('estado',$estados)->where('colectivo','like',"%".$colectivo)->where('capacidad','like',"%".$perfil)->orderby($sortby,$order)->paginate($offset);
      

      $colectivos = Config::get('options.colectivos');
      $perfiles = Config::get('options.perfiles');

      return View::make('admin.userList')->with(compact('usuarios','sortby','order','veractivados','verdesactivados'))->nest('dropdown',Auth::user()->dropdownMenu())->nest('menuUsuarios','admin.menuUsuarios',compact('veractivados','verdesactivados','colectivo','colectivos','perfil','perfiles'))->nest('modalAddUser','admin.userModalAdd')->nest('modalSancionaUser','admin.userModalSanciona');
  }

  public function delete(){
    
    $id = Input::get('id','');

    if (empty($id)){
        Session::flash('message', 'No se ha borrado el usuario: Identificador de usuario vacío....');
        return Redirect::back();
    }

    $user = User::find($id);
    $user->supervisa()->detach();
    $user->delete();
    Session::flash('message', 'Usuario borrado con éxito....');
    return Redirect::back();

  }

  public function hometecnico(){
      $dropdown = Auth::user()->dropdownMenu();
      return View::make('tecnico.index')->nest('dropdown',$dropdown)->nest('addModal','tecnico.addReservaModal');
  }
 
  public function newUser(){

      return View::make('admin.userNew')->with("user",Auth::user())->nest('dropdown',Auth::user()->dropdownMenu());
  }
 
  public function activeUserbyajax(){

    $result = array('success' => false);
    
    $username = Input::get('username','');
    $colectivo = Input::get('colectivo','');
    $caducidad = Input::get('caducidad','');
    $observaciones = Input::get('observaciones','');

    $rol = Input::get('rol','1');
    //$id = Input::get('id','');

    $user = User::where('username','=',$username)->first();

    if (!empty($user)) {
      
      $user->estado = true;
      $user->colectivo = $colectivo;
      $user->capacidad = $rol;
      $user->observaciones = $observaciones;
      if (empty($caducidad)) $caduca = date('Y-m-d');
      else $caduca = Date::toDB($caducidad);
      $user->caducidad = $caduca;
      $user->save();

      
      $this->cierraNotificacion($username);
      //mail to User by Activate
      $sgrMail = new sgrMail();
      $sgrMail->notificaActivacionUVUS($user->id);            
      
      $result['success'] = true;

    }

    return $result;
  
  }

  public function ajaxDelete(){

    $result = array('success' => false);
    
    $username = Input::get('username','');
    $colectivo = Input::get('colectivo','');
    $caducidad = Input::get('caducidad','');
    $rol = Input::get('rol','1');
    

    $user = User::where('username','=',$username)->first();

    if (!empty($user)) {
      Notificacion::where('source','=',$username)->delete();
      $user->delete();
      $result['success'] = true;
    }
    return $result;
  }


  public function desactiveUserbyajax(){

    $result = array('success' => false);
    
    $username = Input::get('username','');
    $colectivo = Input::get('colectivo','');
    $caducidad = Input::get('caducidad','');
    $observaciones = Input::get('observaciones','');
    $rol = Input::get('rol','1');
    

    $user = User::where('username','=',$username)->first();

    if (!empty($user)) {
      
      $user->estado = false;
      $user->colectivo = $colectivo;
      $user->capacidad = $rol;
      $user->observaciones = $observaciones;
      
      if (empty($caducidad)) $caduca = date('Y-m-d');
      else $caduca = Date::toDB($caducidad);
      $user->caducidad = $caduca;
      $user->save();

      
      $this->cierraNotificacion($username);
      
      
      $result['success'] = true;

    }

    return $result;
  
  }

  private function cierraNotificacion($username){

      Notificacion::where('source','=',$username)->update(array('estado' => 'cerrada'));
      return true;
  }

  
  public function create()
    {
      $respuesta = array( 'error'   => false,
                          'errors'  => array(),

                          );
    //Creamos un nuevo usuario
    $rules = array(
        'nombre'                => 'required',
        'apellidos'             => 'required',
        'colectivo'             => 'required',
        'username'              => 'required|unique:users',
        'caducidad'             => 'required',//|date|date_format:d-m-Y|after:'. date('d-m-Y'),
        'capacidad'             => 'required|in:1,2,3,4,5',
        'email'                 => 'required|email',
        //'password'              => 'required|min:4|alpha_num|Confirmed',
        'caducidad'             => 'required'
 
      );

     $messages = array(
          'required'      => 'El campo <strong>:attribute</strong> es obligatorio.',
          //'min'           => 'El campo <strong>:attribute</strong> no puede tener menos de :min carácteres.',
          //'alpha_num'     => 'El campo <strong>:attribute</strong> debe ser alfanumérico (caracteres a-z y numeros 0-9)',
          //'confirmed'     => 'Las contraseñas no coinciden',
          'date_es'       => 'El campo <strong>:attribute</strong> debe ser una fecha válida',
          'date_format'   => 'El campo <strong>:attribute</strong> debe tener el formato d-m-Y',
          'after'         => 'El campo <strong>:attribute</strong> debe ser una fecha posterior al día actual',
          'in'            => 'El campo <strong>:attribute</strong> es erroneo.',
          'email'         => 'El campo <strong>:attribute</strong> debe ser una dirección de email válida',
          'unique'        => 'El UVUS ya existe.'
        );

    $validator = Validator::make(Input::all(), $rules, $messages);
    //validación fecha formato d-m-Y
    $fecha = Input::get('caducidad'); 
    if (!empty($fecha)){
      $data = Input::all();
      $validator->sometimes('caducidad','date_es',function($data){
        $date_es = date_parse_from_format("d-m-Y", $data['caducidad']);
        if ($date_es['warning_count'] > 0 || $date_es['error_count'] > 0) return true;        
      });
    }
    
    //$url = URL::route('adduser');     
    if ($validator->fails())
      {
        $respuesta['error'] = true;
        $respuesta['errors'] = $validator->errors();
        return $respuesta;
        //return Redirect::back()->withErrors($validator->errors())->withInput(Input::all());;
      }
    else{  

        // salvamos los datos.....
        $user = new User;

        $user->nombre = Input::get('nombre',''); 
        $user->apellidos = Input::get('apellidos','');
        $user->colectivo = Input::get('colectivo');
       


        $user->username = Input::get('username'); 
        $user->capacidad = Input::get('capacidad');
        // La fecha se debe guardar en formato USA Y-m-d  
        $fecha = DateTime::createFromFormat('j-m-Y',Input::get('caducidad'));
        $user->caducidad = $fecha->format('Y-m-d');
        
        $user->estado = 1; //Activamos al crear
        $user->email = Input::get('email');
        $user->save();
        Session::flash('message', 'Usuario creado con éxito');
        return $respuesta;
        //return Redirect::to($url);
    }

  }
 

 /**
   * Update the specified resource in storage.
   *
   * @param  int  $id
   * @return Response
   */


public function formEditUser(){

    $id = Input::get('id','');
    $veractivados = Input::get('veractivados',1);
    $verdesactivados = Input::get('verdesactivados',0);
    $vercaducados = Input::get('vercaducados',0);

    if (empty($id)) $user = new User();
    else $user = User::find($id);
    
    return View::make('admin.userEdit')->with(compact('user'))->nest('dropdown',Auth::user()->dropdownMenu());//->nest('menuUsuarios','admin.menuUsuarios',compact('veractivados','verdesactivados','vercaducados'));
} 
  
public function updateUser(){

    
    $rules = array(
        'nombre'                => 'required',
        'apellidos'             => 'required',
        'estado'                => 'required',
        'colectivo'             => 'required',
        'caducidad'             => 'required',//|date|date_format:Y-m-d',
        'capacidad'             => 'required|in:1,2,3,4,5',
        'email'                 => 'required|email',
        //'password'              => 'sometimes|confirmed|min:4|alpha_num',
        
      );

    $messages = array(
            'required'   => 'El campo <strong>:attribute</strong> es obligatorio.',
            'date_es'    => 'El campo <strong>:attribute</strong> debe ser una fecha válida.',
            'in'         => 'El campo <strong>:attribute</strong> es erroneo.',
            'email'      => 'El campo <strong>:attribute</strong> debe ser una dirección de email válida',
           );
  
 
    $validator = Validator::make(Input::all(), $rules, $messages);
    //validación fecha formato d-m-Y
    $fecha = Input::get('caducidad'); 
    if (!empty($fecha)){
      $data = Input::all();
      $validator->sometimes('caducidad','date_es',function($data){
        $date_es = date_parse_from_format("j-m-Y", $data['caducidad']);
        if ($date_es['warning_count'] > 0 || $date_es['error_count'] > 0) return true;        
      });
    }
   
    if ($validator->fails())
    {
        return Redirect::back()->withErrors($validator->errors());
    }
    else{  
        $id = Input::get('id','');
        if (empty($id)){
          Session::flash('message', 'id vacio');
          return Redirect::back();

        }
        // salvamos los datos.....
        $user = User::find($id);
        
        // La fecha se debe guardar en formato USA Y-m-d  
        $fecha = DateTime::createFromFormat('j-m-Y',Input::get('caducidad'));
        $user->caducidad = $fecha->format('Y-m-d');
        $user->capacidad = Input::get('capacidad');
        $user->colectivo = Input::get('colectivo');
        $user->estado = Input::get('estado','0');
        $user->email = Input::get('email');
        $user->nombre = Input::get('nombre');
        $user->apellidos = Input::get('apellidos');
        $user->observaciones = Input::get('observaciones','');

        $user->save();

        //cierra notificación de alta en caso de que exista....
        $this->cierraNotificacion($user->username);
        $message = 'Usuario <strong>'. $user->username .' </strong>actualizado con éxito';
        Session::flash('message', 'Usuario <strong>'. $user->username .' </strong>actualizado con éxito');
        $url = URL::route('useredit.html',['id' => $id]); 
        return Redirect::to($url);
        //$message = 'Usuario <strong>'. $user->username .' </strong>actualizado con éxito';
        //return View::make('admin.userEdit')->with(compact('user','message'))->nest('dropdown',Auth::user()->dropdownMenu())->nest('menuAdminUsuarios','admin.menuAdminUsuarios');
    }
  
}
 

  /**
   * Store a newly created resource in storage.
   *
   * @return Response
   */
  public function store()
  {
    //
  }
 
  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function show($id)
  {
    //
  }
 
  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function edit($id)
  {
    //
  }
 
 
  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function destroy($id)
  {
    //
  }

  public function isUserDelegacion (){
    return ACL::isAlumnDelegacion();
  }
 
}
