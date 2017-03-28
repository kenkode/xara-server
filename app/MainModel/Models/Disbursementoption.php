<?php

class Disbursementoption extends \Eloquent {
	protected $table='disbursementoptions';

public static $rules = [
		'name' => 'required',
		'min' => 'required',
		'max' => 'required'
	];

public static $messages = array(
        'name.required'=>'Please insert disbursement option name!',
        'min.required'=>'Please insert disbursement minimum amount!',
        'max.required'=>'Please insert disbursement maximum amount!'
    );
	
}