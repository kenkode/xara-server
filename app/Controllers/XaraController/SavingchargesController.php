<?php

class SavingchargesController extends \BaseController {

	/**
	 * Display a listing of savingcharges
	 *
	 * @return Response
	 */
	public function index()
	{
		$savingcharges = Savingcharge::all();

		return View::make('savingcharges.index', compact('savingcharges'));
	}

	/**
	 * Show the form for creating a new savingcharge
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('savingcharges.create');
	}

	/**
	 * Store a newly created savingcharge in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Savingcharge::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		Savingcharge::create($data);

		return Redirect::route('savingcharges.index');
	}

	/**
	 * Display the specified savingcharge.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$savingcharge = Savingcharge::findOrFail($id);

		return View::make('savingcharges.show', compact('savingcharge'));
	}

	/**
	 * Show the form for editing the specified savingcharge.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$savingcharge = Savingcharge::find($id);

		return View::make('savingcharges.edit', compact('savingcharge'));
	}

	/**
	 * Update the specified savingcharge in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$savingcharge = Savingcharge::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Savingcharge::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$savingcharge->update($data);

		return Redirect::route('savingcharges.index');
	}

	/**
	 * Remove the specified savingcharge from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Savingcharge::destroy($id);

		return Redirect::route('savingcharges.index');
	}

}
