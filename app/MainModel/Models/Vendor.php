<?php

class Vendor extends \Eloquent {

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = [];


	/*
	* products relation
	*/

	public function products(){

		return $this->hasMany('Product');
	}

}