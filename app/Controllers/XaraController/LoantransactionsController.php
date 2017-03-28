<?php

class LoantransactionsController extends \BaseController {

	/**
	 * Display a listing of loantransactions
	 *
	 * @return Response
	 */
	public function index()
	{
		$loantransactions = Loantransaction::all();

		return View::make('loantransactions.index', compact('loantransactions'));
	}

	/**
	 * Show the form for creating a new loantransaction
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('loantransactions.create');
	}

	/**
	 * Store a newly created loantransaction in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Loantransaction::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		Loantransaction::create($data);

		return Redirect::route('loantransactions.index');
	}

	/**
	 * Display the specified loantransaction.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$loantransaction = Loantransaction::findOrFail($id);

		return View::make('loantransactions.show', compact('loantransaction'));
	}

	/**
	 * Show the form for editing the specified loantransaction.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$loantransaction = Loantransaction::find($id);

		return View::make('loantransactions.edit', compact('loantransaction'));
	}

	/**
	 * Update the specified loantransaction in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$loantransaction = Loantransaction::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Loantransaction::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$loantransaction->update($data);

		return Redirect::route('loantransactions.index');
	}

	/**
	 * Remove the specified loantransaction from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Loantransaction::destroy($id);

		return Redirect::route('loantransactions.index');
	}


	public function statement($id){

		$account = Loanaccount::findOrFail($id);

		$transactions = $account->loantransactions;
		/*
		print_r($transactions);
		$credit = DB::table('savingtransactions')->where('savingaccount_id', '=', $account->id)->where('type', '=', 'credit')->sum('amount');
		$debit = DB::table('savingtransactions')->where('savingaccount_id', '=', $account->id)->where('type', '=', 'debit')->sum('amount');

		$balance = $credit - $debit;
		*/
		$organization = Organization::findOrFail(1);

		$pdf = PDF::loadView('pdf.loanstatement', compact('transactions', 'organization', 'account'))->setPaper('a4')->setOrientation('potrait');;
 	
		return $pdf->stream('loanstatement.pdf');
	}

	public function certificate($id){

		$account = Loanaccount::where('id','=',$id)->get()->first();

		$member=Member::where('id','=',$account->member_id)->pluck('name');

		$organization = Organization::where('id','=',1)
		->get()->first();

		$pdf = PDF::loadView('pdf.loancertificate', compact('member', 'organization', 'account'))->setPaper('a5')->setOrientation('landscape');;
 	
		return $pdf->stream('Loan Clearance Certificate.pdf');				
	}

	public function overpayments($id){

			$account = Loanaccount::where('id','=',$id)->get()->first();

			$member=Member::where('id','=',$account->member_id)->pluck('name');

			$organization = Organization::findOrFail(1);

			$pdf = PDF::loadView('pdf.overpayments', compact('member', 'organization', 'account'))->setPaper('a5')->setOrientation('landscape');;
	 	
			return $pdf->stream('Loan Overpayment Claim.pdf');				
		}


	public function receipt($id){

		$transaction = Loantransaction::findOrFail($id);

		$organization = Organization::findOrFail(1);

		$pdf = PDF::loadView('pdf.loanreports.receipt', compact('transaction', 'organization'))->setPaper('a5')->setOrientation('potrait');;
 	
		return $pdf->stream('receipt.pdf');


	}


}
