<?php

class Savingproduct extends \Eloquent {

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = [];

	public function savingproductcoa(){

		return $this->hasMany('Account');
	}


	public function savingaccounts(){

		return $this->hasMany('Savingaccount');
	}


	public function savingpostings(){

		return $this->hasMany('Savingposting');
	}


	public function charges(){

		return $this->belongsToMany('Charge');
	}


}