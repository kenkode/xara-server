<?php

class Investmentcategory extends \Eloquent {

	protected $table='investmentcategories';
	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = [];

}