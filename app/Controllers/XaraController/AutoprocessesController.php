<?php

class AutoprocessesController extends \BaseController {

	/**
	 * Display a listing of autoprocesses
	 *
	 * @return Response
	 */
	public function index()
	{
		$autoprocesses = Autoprocess::all();

		return View::make('autoprocesses.index', compact('autoprocesses'));
	}

	/**
	 * Show the form for creating a new autoprocess
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('autoprocesses.create');
	}

	/**
	 * Store a newly created autoprocess in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Autoprocess::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		Autoprocess::create($data);

		return Redirect::route('autoprocesses.index');
	}

	/**
	 * Display the specified autoprocess.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$autoprocess = Autoprocess::findOrFail($id);

		return View::make('autoprocesses.show', compact('autoprocess'));
	}

	/**
	 * Show the form for editing the specified autoprocess.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$autoprocess = Autoprocess::find($id);

		return View::make('autoprocesses.edit', compact('autoprocess'));
	}

	/**
	 * Update the specified autoprocess in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$autoprocess = Autoprocess::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Autoprocess::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$autoprocess->update($data);

		return Redirect::route('autoprocesses.index');
	}

	/**
	 * Remove the specified autoprocess from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Autoprocess::destroy($id);

		return Redirect::route('autoprocesses.index');
	}

}
