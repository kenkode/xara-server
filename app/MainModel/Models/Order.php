<?php

class Order extends \Eloquent {

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = [];


	// products relations

	public function product(){

		return $this->belongsTo('Product');
	}



	public static function submitOrder($product, $member){



		

		$order = new Order;

		$order->product()->associate($product);
		$order->order_date = date('Y-m-d');
		$order->customer_name = $member->name;
		$order->customer_number = $member->membership_no;
		$order->save();
	}

}