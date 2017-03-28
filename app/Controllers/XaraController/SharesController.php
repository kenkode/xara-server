<?php

class SharesController extends \BaseController {

	/**
	 * Display a listing of shares
	 *
	 * @return Response
	 */
	public function index()
	{
		$shares = Share::all();

		return View::make('shares.index', compact('shares'));
	}

	/**
	 * Show the form for creating a new share
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('shares.create');
	}

	/**
	 * Store a newly created share in storage.
	 *
	 * @return Response
	 */
	public function store(){

		$validator = Validator::make($data = Input::all(), Share::$rules);
		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}
		$share = new Share;

		$share->value = Input::get('value');
		$share->transfer_charge = Input::get('transfer_charge');
		$share->charged_on = Input::get('charged_on');
		$share->save();

		return Redirect::route('shares.index');
	}

	/**
	 * Display the specified share.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$share = Share::findOrFail($id);

		return View::make('shares.show', compact('share'));
	}

	/**
	 * Show the form for editing the specified share.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$share = Share::find($id);

		return View::make('shares.edit', compact('share'));
	}

	/**
	 * Update the specified share in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$share = Share::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Share::$rules);

		if ($validator->fails()){
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$share->value = Input::get('value');
		$share->transfer_charge = Input::get('transfer_charge');
		$share->charged_on = Input::get('charged_on');
		$share->update();

		return Redirect::to('shares/show/'.$share->id);
	}

	/**
	 * Share capital Contribution.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function contribution(){
		$members=Member::all();
		return View::make('sharecapital.contribution',compact('members'));
	}
	/**
	 * Share Capital Dividends.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function dividend(){
		$members=Member::all();
		return View::make('sharecapital.dividend',compact('members'));
	}
	/**
	 * Share capital report.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function parameters(){
		return View::make('sharecapital.parameters');
	}


	public function parameterize(){
		$divi=new Dividend;
		$divi->total=Input::get('sum_dividends');
		$divi->special=Input::get('special_dividends');
		$divi->outstanding=Input::get('outstanding_shares');
		$divi->save();

		return Redirect::action('SharesController@dividend');
	}
	/**
	 * Share capital report.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function editparameters(){
		$div=Dividend::where('id','=',1)->get()->first();
		return View::make('sharecapital.editparameters',compact('div'));
	}

	public function doparameterize(){
		$divi=Dividend::where('id','=',1)->get()->first();;
		$divi->total=Input::get('sum_dividends');
		$divi->special=Input::get('special_dividends');
		$divi->outstanding=Input::get('outstanding_shares');
		$divi->update();

		return Redirect::action('SharesController@dividend');
	}	
	/**
	 * Remove the specified share from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Share::destroy($id);
		return Redirect::route('shares.index');
	}

}
