<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SavingProduct;
use App\Models\SavingTransaction;
use App\Models\Member;

/**
 *
 */
class Savingsaccount extends Model
{

  protected $table = "x_savingaccounts";

  public function member(){

		return $this->belongsTo('Member');
	}


	public function savingproduct(){

		return $this->belongsTo('SavingProduct');
	}


	public function transactions(){

		return $this->hasMany('SavingTransaction');
	}



	public static function getLastAmount($savingaccount){

		$saving = DB::table('x_savingtransactions')->where('savingaccount_id', '=', $savingaccount->id)->where('type', '=', 'credit')->OrderBy('date',  'desc')->pluck('amount');

			
		if($saving){
			return $saving;
		}
		else {
			return 0;
		}
			

	}



	public static function getAccountBalance($savingaccount){

		$deposits = DB::table('x_savingtransactions')->where('savingaccount_id', '=', $savingaccount->id)->where('type', '=', 'credit')->sum('amount');
		$withdrawals = DB::table('x_savingtransactions')->where('savingaccount_id', '=', $savingaccount->id)->where('type', '=', 'debit')->sum('amount');

		$balance = $deposits - $withdrawals;

		return $balance;
	}




	public static function getDeductionAmount($account, $date){

		$transactions = DB::table('x_savingtransactions')->where('savingaccount_id', '=', $account->id)->get();

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



 ?>
