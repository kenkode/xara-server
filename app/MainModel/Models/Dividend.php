<?php

class Dividend extends \Eloquent {
	protected $table="dividends";
	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = [];
	
}