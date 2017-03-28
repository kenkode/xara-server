<?php

class DisbursementController extends \BaseController {

	/**
	 * Display a listing of loan disbursementoptions
	 *
	 * @return Response
	 */
	public function index()
	{
		$options = Disbursementoption::all();

		return View::make('disbursements.index', compact('options'));
	}

	/**
	 * Show the form for creating a new loan disbursement option
	 *
	 * @return Response
	 */
	public function create()
	{		
		return View::make('disbursements.create');
	}
	/**
	 * Create the disbursement option
	 *
	 * @return Response
	 */
	public function docreate()
	{	
		$records=Input::all();
		$existence=Disbursementoption::where('name','=',Input::get('name'))->get();	
		if(count($existence)<=0){
			$disburse=new Disbursementoption;
			$disburse->name=array_get($records, 'name');		
			$disburse->min=array_get($records, 'min_amt');
			$disburse->max=array_get($records,'max_amt');
			$disburse->description=array_get($records,'desc');
			$disburse->save();
			$options = Disbursementoption::all();
			$operation="Disbursement option successfully created!";
			return View::make('disbursements.index', compact('options','operation'));
		}else if(count($existence)>0){
			return Redirect::back()->withWrath('The disbursement option already exists.');
		}		
	}
	/**
	*
	*Form to update loan disbursement information
	*
	*/
	public function update($id){
		$disbursed=Disbursementoption::where('id','=',$id)->get()->first();
		return View::make('disbursements.update',compact('disbursed'));
	}
	/**
	*
	*updating loan disbursement information
	*
	*/
	public function doupdate(){
		$data=Input::all();
		$update=Disbursementoption::where('id','=',array_get($data,'id'))->get()->first();
		$update->name=array_get($data, 'name');		
		$update->min=array_get($data, 'min_amt');
		$update->max=array_get($data,'max_amt');
		$update->description=array_get($data,'desc');
		$update->save();
		//Redirect user
		$options = Disbursementoption::all();
		$done="The disbursement option has been successfully updated!";
		return View::make('disbursements.index', compact('options','done'));
	}
	/**
	*
	*Deleting a disbursement option 
	*
	*/
	public function destroy($id){
		Disbursementoption::destroy($id);
		$options = Disbursementoption::all();
		$shot="The disbursement option has been deleted!";
		return View::make('disbursements.index', compact('options','shot'));		
	}
}