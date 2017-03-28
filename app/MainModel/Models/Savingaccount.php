<?php

class Savingaccount extends \Eloquent {

	// Add your validation rules here
	public static $rules = [
		'account_number' => 'unique:account_number'
	];

	// Don't forget to fill this array
	protected $fillable = [];


	public function member(){

		return $this->belongsTo('Member');
	}


	public function savingproduct(){

		return $this->belongsTo('Savingproduct');
	}


	public function transactions(){

		return $this->hasMany('Savingtransaction');
	}



	public static function getLastAmount($savingaccount){

		$saving = DB::table('savingtransactions')->where('savingaccount_id', '=', $savingaccount->id)->where('type', '=', 'credit')->OrderBy('date',  'desc')->pluck('amount');

			
		if($saving){
			return $saving;
		}
		else {
			return 0;
		}
			

	}



	public static function getAccountBalance($savingaccount){

		$deposits = DB::table('savingtransactions')->where('savingaccount_id', '=', $savingaccount->id)->where('type', '=', 'credit')->sum('amount');
		$withdrawals = DB::table('savingtransactions')->where('savingaccount_id', '=', $savingaccount->id)->where('type', '=', 'debit')->sum('amount');

		$balance = $deposits - $withdrawals;

		return $balance;
	}




	public static function getDeductionAmount($account, $date){

		$transactions = DB::table('savingtransactions')->where('savingaccount_id', '=', $account->id)->get();

		$amount = 0;
		foreach ($transactions as $transaction) {

			$period = date('m-Y', strtotime($transaction->date));

			if($date == $period){

				if($transaction->type == 'credit'){
					$amount = $transaction->amount;
				}
				
			} 
			
		}


		return $amount;
	}



	



}