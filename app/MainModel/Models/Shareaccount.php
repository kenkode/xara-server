<?php

class Shareaccount extends \Eloquent {

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = [];


	public function member(){

		return $this->belongsTo('Member');
	}


	public function sharetransactions(){

		return $this->hasMany('Sharetransaction');
	}



	public static function createAccount($id){

		$member = Member::find($id);


		$share = new Share;


		$acc = 'SH-'.$member->membership_no;

		$shareaccount = new Shareaccount;


		$shareaccount->member()->associate($member);

		$shareaccount->account_number = $acc;

		$shareaccount->opening_date = date('Y-m-d');

		$shareaccount->save();
	}
}
