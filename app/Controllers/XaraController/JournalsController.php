<?php

class JournalsController extends \BaseController {

	/**
	 * Display a listing of journals
	 *
	 * @return Response
	 */
	public function index()
	{
		$journals = Journal::all();

		return View::make('journals.index', compact('journals'));
	}

	/**
	 * Show the form for creating a new journal
	 *
	 * @return Response
	 */
	public function create()
	{

		$accounts = Account::all();
		return View::make('journals.create', compact('accounts'));
	}

	/**
	 * Store a newly created journal in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Journal::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}


		
		$data = array(
			'date' => Input::get('date'), 
			'debit_account' => Input::get('debit_account'),
			'credit_account' => Input::get('credit_account'),
			'description' => Input::get('description'),
			'amount' => Input::get('amount'),
			'initiated_by' => Input::get('user')
			);
		
		$journal = new Journal;

		$journal->journal_entry($data);

		return Redirect::route('journals.index');
	}

	/**
	 * Display the specified journal.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$journal = Journal::findOrFail($id);

		return View::make('journals.show', compact('journal'));
	}

	/**
	 * Show the form for editing the specified journal.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$journal = Journal::find($id);

		return View::make('journals.edit', compact('journal'));
	}

	/**
	 * Update the specified journal in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$journal = Journal::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Journal::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$branch = Branch::findOrFail(Input::get('branch_id'));
		$account = Account::findOrFail(Input::get('account_id'));


		$journal->branch()->associate($branch);
		$journal->account()->associate($account);

		$journal->date = Input::get('date');
		$journal->trans_no = Input::get('trans_no');
		$journal->initiated_by = Input::get('initiated_by');
		$journal->amount = Input::get('amount');
		$journal->type = Input::get('type');
		$journal->description = Input::get('description');
		$journal->update();

		return Redirect::route('journals.index');
	}

	/**
	 * Remove the specified journal from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$journal = Journal::findOrFail($id);

		$journal->void = TRUE;
		$journal->update();

		return Redirect::route('journals.index');
	}

}
