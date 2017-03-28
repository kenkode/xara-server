<?php

class LoanchargesController extends \BaseController {

	/**
	 * Display a listing of loancharges
	 *
	 * @return Response
	 */
	public function index()
	{
		$loancharges = Loancharge::all();

		return View::make('loancharges.index', compact('loancharges'));
	}

	/**
	 * Show the form for creating a new loancharge
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('loancharges.create');
	}

	/**
	 * Store a newly created loancharge in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Loancharge::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		Loancharge::create($data);

		return Redirect::route('loancharges.index');
	}

	/**
	 * Display the specified loancharge.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$loancharge = Loancharge::findOrFail($id);

		return View::make('loancharges.show', compact('loancharge'));
	}

	/**
	 * Show the form for editing the specified loancharge.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$loancharge = Loancharge::find($id);

		return View::make('loancharges.edit', compact('loancharge'));
	}

	/**
	 * Update the specified loancharge in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$loancharge = Loancharge::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Loancharge::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$loancharge->update($data);

		return Redirect::route('loancharges.index');
	}

	/**
	 * Remove the specified loancharge from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Loancharge::destroy($id);

		return Redirect::route('loancharges.index');
	}

}
