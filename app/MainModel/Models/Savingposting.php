<?php

class Savingposting extends \Eloquent {

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = [];



	public function savingproduct(){

		return $this->belongsTo('Savingproduct');
	}

	public function create_post_rules($product, $fee_income_acc, $saving_control_acc, $cash_account){


			//create posting rule for deposit transaction

			$posting = new Savingposting;

			$posting->transaction = 'deposit';
			$posting->debit_account = $cash_account;
			$posting->credit_account = $saving_control_acc;
			$posting->savingproduct()->associate($product);
			$posting->save();



			//create posting rule for withdrawal transaction

			$posting = new Savingposting;

			$posting->transaction = 'withdrawal';
			$posting->debit_account = $saving_control_acc;
			$posting->credit_account = $cash_account;
			$posting->savingproduct()->associate($product);
			$posting->save();



			//create posting rule for charge transaction

			$posting = new Savingposting;

			$posting->transaction = 'charge';
			$posting->debit_account = $cash_account;
			$posting->credit_account = $fee_income_acc;
			$posting->savingproduct()->associate($product);
			$posting->save();





	}

}