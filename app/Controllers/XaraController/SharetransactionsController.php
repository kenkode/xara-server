<?php

class SharetransactionsController extends \BaseController {

	/**
	 * Display a listing of sharetransactions
	 *
	 * @return Response
	 */
	public function index()
	{
		$sharetransactions = Sharetransaction::all();

		return View::make('sharetransactions.index', compact('sharetransactions'));
	}

	/**
	 * Show the form for creating a new sharetransaction
	 *
	 * @return Response
	 */
	public function create($id)
	{


		$shareaccount = Shareaccount::findOrFail($id);

		$member = $shareaccount->member;

		return View::make('sharetransactions.create', compact('shareaccount', 'member'));

		
	}

	/**
	 * Store a newly created sharetransaction in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Sharetransaction::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$shareaccount = Shareaccount::findOrFail(Input::get('account_id'));

		$sharetransaction = new Sharetransaction;

		$sharetransaction->date = Input::get('date');
		$sharetransaction->shareaccount()->associate($shareaccount);
		$sharetransaction->amount = Input::get('amount');
		$sharetransaction->type = Input::get('type');
		$sharetransaction->description = Input::get('description');
		$sharetransaction->save();

		return Redirect::to('sharetransactions/show/'.$shareaccount->id);
	
	}

	/**
	 * Display the specified sharetransaction.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		


		$account = Shareaccount::findOrFail($id);

		$credit = DB::table('sharetransactions')->where('shareaccount_id', '=', $account->id)->where('type', '=', 'credit')->sum('amount');
		$debit = DB::table('sharetransactions')->where('shareaccount_id', '=', $account->id)->where('type', '=', 'debit')->sum('amount');

		$balance = $credit - $debit;

		$sh = Share::findOrFail(1);

		$sharevalue = $sh->value;

		if($sharevalue != 0){
			$shares = $balance/$sharevalue;
		} else {

			$shares = 0;
		}
		

		return View::make('sharetransactions.show', compact('account', 'shares'));

		
	}

	/**
	 * Show the form for editing the specified sharetransaction.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$sharetransaction = Sharetransaction::find($id);

		return View::make('sharetransactions.edit', compact('sharetransaction'));
	}

	/**
	 * Update the specified sharetransaction in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$sharetransaction = Sharetransaction::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Sharetransaction::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$sharetransaction->update($data);

		return Redirect::route('sharetransactions.index');
	}

	/**
	 * Remove the specified sharetransaction from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Sharetransaction::destroy($id);

		return Redirect::route('sharetransactions.index');
	}

}
