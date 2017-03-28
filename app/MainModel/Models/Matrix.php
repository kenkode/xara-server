<?php

class Matrix extends \Eloquent {

	protected $table='matrices';
	// Add your validation rules here
	public static $rules = [
		 'name' => 'required',
		 'maximum'=>'required'
	];


}