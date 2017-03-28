<?php

class SavingpostingsController extends \BaseController {

	/**
	 * Display a listing of savingpostings
	 *
	 * @return Response
	 */
	public function index()
	{
		$savingpostings = Savingposting::all();

		return View::make('savingpostings.index', compact('savingpostings'));
	}

	/**
	 * Show the form for creating a new savingposting
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('savingpostings.create');
	}

	/**
	 * Store a newly created savingposting in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Savingposting::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		Savingposting::create($data);

		return Redirect::route('savingpostings.index');
	}

	/**
	 * Display the specified savingposting.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$savingposting = Savingposting::findOrFail($id);

		return View::make('savingpostings.show', compact('savingposting'));
	}

	/**
	 * Show the form for editing the specified savingposting.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$savingposting = Savingposting::find($id);

		return View::make('savingpostings.edit', compact('savingposting'));
	}

	/**
	 * Update the specified savingposting in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$savingposting = Savingposting::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Savingposting::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$savingposting->update($data);

		return Redirect::route('savingpostings.index');
	}

	/**
	 * Remove the specified savingposting from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Savingposting::destroy($id);

		return Redirect::route('savingpostings.index');
	}

}
