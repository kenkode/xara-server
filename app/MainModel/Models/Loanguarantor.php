<?php

class Loanguarantor extends \Eloquent {

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = [];



	public function loanaccount(){

		return $this->belongsTo('Loanaccount');
	}

	public function member(){

		return $this->belongsTo('Member');
	}

}