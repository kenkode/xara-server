<?php

class Charge extends \Eloquent {

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = [];


	public function loanproducts(){

		return $this->belongsToMany('Loanproduct');
	}


	public function savingproducts(){

		return $this->belongsToMany('Savingproduct');
	}

}