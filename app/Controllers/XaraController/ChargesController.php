<?php

class ChargesController extends \BaseController {

	/**
	 * Display a listing of charges
	 *
	 * @return Response
	 */
	public function index()
	{
		$charges = Charge::all();

		return View::make('charges.index', compact('charges'));
	}

	/**
	 * Show the form for creating a new charge
	 *
	 * @return Response
	 */
	public function create()
	{
		

		return View::make('charges.create');
	}

	/**
	 * Store a newly created charge in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Charge::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$charge = new Charge;

		$charge->name = Input::get('name');
		$charge->category = Input::get('category');
		$charge->calculation_method = Input::get('calculation_method');
		$charge->payment_method = Input::get('payment_method');
		$charge->percentage_of = Input::get('percentage_of');
		$charge->amount = Input::get('amount');
		if(Input::get('fee') == '1') {
				$charge->fee = TRUE;
		}
		else {
				$charge->fee = FALSE;
		}
		
		$charge->save();

		return Redirect::route('charges.index');
	}

	/**
	 * Display the specified charge.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$charge = Charge::findOrFail($id);

		return View::make('charges.show', compact('charge'));
	}

	/**
	 * Show the form for editing the specified charge.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$charge = Charge::find($id);

		return View::make('charges.edit', compact('charge'));
	}

	/**
	 * Update the specified charge in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$charge = Charge::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Charge::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$charge->name = Input::get('name');
		$charge->category = Input::get('category');
		$charge->calculation_method = Input::get('calculation_method');
		$charge->payment_method = Input::get('payment_method');
		$charge->percentage_of = Input::get('percentage_of');
		$charge->amount = Input::get('amount');
		if(Input::get('fee') == '1') {
				$charge->fee = TRUE;
		}
		else {
				$charge->fee = FALSE;
		}
		$charge->update();


		return Redirect::route('charges.index');
	}

	/**
	 * Remove the specified charge from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Charge::destroy($id);

		return Redirect::route('charges.index');
	}


	public function disable($id)
	{
		$charge = Charge::findOrFail($id);

		$charge->disabled = TRUE;
		$charge->update();

		return Redirect::route('charges.index');
	}


	public function enable($id)
	{
		$charge = Charge::findOrFail($id);

		$charge->disabled = FALSE;
		$charge->update();

		return Redirect::route('charges.index');
	}

}
