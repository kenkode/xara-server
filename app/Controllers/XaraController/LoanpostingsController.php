<?php

class LoanpostingsController extends \BaseController {

	/**
	 * Display a listing of loanpostings
	 *
	 * @return Response
	 */
	public function index()
	{
		$loanpostings = Loanposting::all();

		return View::make('loanpostings.index', compact('loanpostings'));
	}

	/**
	 * Show the form for creating a new loanposting
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('loanpostings.create');
	}

	/**
	 * Store a newly created loanposting in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Loanposting::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		Loanposting::create($data);

		return Redirect::route('loanpostings.index');
	}

	/**
	 * Display the specified loanposting.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$loanposting = Loanposting::findOrFail($id);

		return View::make('loanpostings.show', compact('loanposting'));
	}

	/**
	 * Show the form for editing the specified loanposting.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$loanposting = Loanposting::find($id);

		return View::make('loanpostings.edit', compact('loanposting'));
	}

	/**
	 * Update the specified loanposting in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$loanposting = Loanposting::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Loanposting::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$loanposting->update($data);

		return Redirect::route('loanpostings.index');
	}

	/**
	 * Remove the specified loanposting from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Loanposting::destroy($id);

		return Redirect::route('loanpostings.index');
	}

}
