<?php

class ShareaccountsController extends \BaseController {

	/**
	 * Display a listing of shareaccounts
	 *
	 * @return Response
	 */
	public function index()
	{
		$shareaccounts = Shareaccount::all();

		return View::make('shareaccounts.index', compact('shareaccounts'));
	}

	/**
	 * Show the form for creating a new shareaccount
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('shareaccounts.create');
	}

	/**
	 * Store a newly created shareaccount in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Shareaccount::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$member = Member::find(Input::get('member_id'));


		$acc = 'SH-'.$member_no;

		$shareaccount = new Shareaccount;


		$shareaccount->member()->associate($member);

		$shareaccount->account_number = $acc;

		$shareaccount->opening_date = date();

		$shareaccount->save();

		return Redirect::route('shareaccounts.index');
	}

	/**
	 * Display the specified shareaccount.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$shareaccount = Shareaccount::findOrFail($id);

		return View::make('shareaccounts.show', compact('shareaccount'));
	}

	/**
	 * Show the form for editing the specified shareaccount.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$shareaccount = Shareaccount::find($id);

		return View::make('shareaccounts.edit', compact('shareaccount'));
	}

	/**
	 * Update the specified shareaccount in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$shareaccount = Shareaccount::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Shareaccount::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$shareaccount->update($data);

		return Redirect::route('shareaccounts.index');
	}

	/**
	 * Remove the specified shareaccount from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Shareaccount::destroy($id);

		return Redirect::route('shareaccounts.index');
	}

}
