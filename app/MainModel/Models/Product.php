<?php

class Product extends \Eloquent {

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];
	
	// Don't forget to fill this array
	protected $fillable = [];


	/*
	* vendor relations
	*/
	public function vendor(){

		return $this->belongsTo('Vendor');
	}


	// orders relations
	public function orders(){
		return $this->hasMany('Order');
	}

}