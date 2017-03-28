<?php

class Audit extends \Eloquent {

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = [];

	public static function logAudit($date, $user, $action, $entity, $amount){

		$audit = new Audit;

		$audit->date = $date;
		$audit->user = $user;
		$audit->action = $action;
		$audit->entity = $entity;
		$audit->amount = $amount;
		$audit->save();
	}
}
