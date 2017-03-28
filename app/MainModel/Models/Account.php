<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model {

	// Add your validation rules here
	public static $rules = [
		'code' => 'required',
		'name' => 'required',
		'category' => 'required',
	];

	// Don't forget to fill this array
	protected $fillable = [];


	public function journals(){

		return $this->hasMany('Journal');
	}


	public function savingProduct(){

		return $this->belongsTo('Saving');
	}



	// create savings accounts
	public function createsavingaccount($acc_name, $product){

		//create savings control

		$category = 'LIABILITY';

		$account = new Account;

		$account->category = 'LIABILITY';
		$account->code = getaccountcode($category);
		$account->name = $acc_name.' control';
		$account->active = TRUE;
		$account->savingproduct()->associate($product);

		$account->save();



		$category = 'INCOME';

		$account = new Account;

		$account->category = 'INCOME';
		$account->code = getaccountcode($category);
		$account->name = $acc_name.' fee income';
		$account->active = TRUE;
		$account->savingproduct()->associate($product);

		$account->save();
	}



	public function getaccountcode($category){
		$code = DB::table('accounts')->where('category', '=', $category)->orderBy('code', 'ASC')->first();

		$code = $code + 1;

		return $code;
	}


	public static function getAccountBalanceAtDate($account, $date){

		$balance = 0;
		$credit = DB::table('journals')->where('account_id', '=', $account->id)->where('type', '=', 'credit')->where('date', '<=', $date)->sum('amount');
		$debit = DB::table('journals')->where('account_id', '=', $account->id)->where('type', '=', 'debit')->where('date', '<=', $date)->sum('amount');

		if($account->category == 'ASSET'){

			$balance = $debit - $credit;


		}

		if($account->category == 'INCOME'){

			$balance = $credit - $debit;


		}

		if($account->category == 'LIABILITY'){

			$balance = $credit - $debit;


		}

		if($account->category == 'EQUITY'){

			$balance = $credit - $debit;


		}

		if($account->category == 'EXPENSE'){

			$balance = $debit - $credit;


		}


		return $balance;


	}



	public static function balanceSheet($date){

		$accounts = Account::all();

		$organization = Organization::find(1);

		$pdf = PDF::loadView('pdf.financials.balancesheet', compact('accounts', 'date', 'organization'))->setPaper('a4')->setOrientation('potrait');

		return $pdf->stream('Balance Sheet.pdf');
	}

}
