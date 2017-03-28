<?php

class Surplus extends \Eloquent {
	protected $table='surplus';
	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];
	// Don't forget to fill this array
	protected $fillable = [];

	public static function calculateSurplus($memberid){
		$amount=Member::where('id',$memberid)->pluck('monthly_remittance_amount');
		$month=date('m');
		$year=date('Y');
		$expected=$amount*$month;
		$saved=DB::table('savingtransactions')->where('type','=','credit')
				->join('savingaccounts','savingtransactions.savingaccount_id','=','savingaccounts.id')
				->where('savingtransactions.date','like','%'.$year.'%')
				->where('savingtransactions.date','like','%'.$month.'%')
				->where('savingaccounts.member_id','=',$memberid)
				->sum('savingtransactions.amount');
		$extra=$saved - $expected;
		return $extra;
	}

}