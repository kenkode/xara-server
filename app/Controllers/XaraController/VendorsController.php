<?php

class VendorsController extends \BaseController {

	/**
	 * Display a listing of vendors
	 *
	 * @return Response
	 */
	public function index()
	{
		$vendors = Vendor::all();

		return View::make('vendors.index', compact('vendors'));
	}

	/**
	 * Show the form for creating a new vendor
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('vendors.create');
	}

	/**
	 * Store a newly created vendor in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Vendor::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$vendor = new Vendor;

		$vendor->name = Input::get('name');
		$vendor->email = Input::get('email');
		$vendor->phone = Input::get('phone');
		$vendor->description = Input::get('description');
		$vendor->status = Input::get('status');
		$vendor->save();


		return Redirect::route('vendors.index');
	}

	/**
	 * Display the specified vendor.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$vendor = Vendor::findOrFail($id);

		return View::make('vendors.show', compact('vendor'));
	}

	/**
	 * Show the form for editing the specified vendor.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$vendor = Vendor::find($id);

		return View::make('vendors.edit', compact('vendor'));
	}

	/**
	 * Update the specified vendor in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$vendor = Vendor::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Vendor::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$vendor->name = Input::get('name');
		$vendor->email = Input::get('email');
		$vendor->phone = Input::get('phone');
		$vendor->description = Input::get('description');
		$vendor->status = Input::get('status');

		$vendor->update();

		return Redirect::route('vendors.index');
	}

	/**
	 * Remove the specified vendor from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Vendor::destroy($id);

		return Redirect::route('vendors.index');
	}

}
