<?php

class Autoprocess extends \Eloquent {

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = [];

	public static function record($date, $category, $product){


		$dt = explode('-', $date);

		$year = $dt[0];
		$month = $dt[1];

		$period = $month."-".$year;


		$autoprocess = new Autoprocess;

		$autoprocess->period = $period;
		$autoprocess->category = $category;
		$autoprocess->product_id = $product->id;
		$autoprocess->is_completed = true;

		$autoprocess->save();

	}



	public static function checkProcessed($date, $category, $product){


		$dt = explode('-', $date);

		$year = $dt[0];
		$month = $dt[1];

		$period = $month."-".$year;

		$processed = DB::table('autoprocesses')->where('period', '=', $period)->where('product_id', '=', $product->id)->where('category', '=', $category)->where('is_completed', '=', true)->first();

		if(is_null($processed)){
			return false;
		} else {

			return true;
		}
	}

}
