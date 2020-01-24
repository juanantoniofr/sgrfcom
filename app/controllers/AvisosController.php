<?php

class AvisosController extends BaseController {


	public function index(){

		$title = Input::get('title','Aviso');
		$alert = Input::get('alert','warning');
		$msg = Input::get('msg','Mensaje no especificado...');

		return View::make('avisos.avisoUsuario',compact('title','alert','msg'));
	}

}