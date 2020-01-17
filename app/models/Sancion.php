<?php

class Sancion extends Eloquent{

 	protected $table = 'sanciones';

 	protected $fillable = array('motivo','f_fin','user_id','tecnico_id');

 	/**
 		*
 		* Una sanción pertecene a un (1) Usuario
 		* 
 	*/
 	public function user(){

 		return $this->belongsTo('User','user_id','id');
 	}

 	/**
 		*
 		* Una sanción fue registrada por un (1) Usuario (con rol técnico MAV)
 		*
 	*/
 	public function tenico(){

 		return $this->belongsTo('User','tecnico_id','id');
 	}
}