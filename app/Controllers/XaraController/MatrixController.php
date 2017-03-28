<?php

 class MatrixController extends \BaseController{

 	/**
	 * Display a listing of guarantor matrices
	 *
	 * @return Response
	 */
	public function index()
	{
		$matrices = Matrix::all();

		return View::make('matrices.index', compact('matrices'));
	}

	/**
	 * Show the form for creating a guarantor matrix
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('matrices.create');
	}
	/**
	 * Create the matrix
	 *
	 * @return Response
	 */
	public function docreate(){
		$data=Input::all();
		$validator = Validator::make($data = Input::all(), Matrix::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$matrix=new Matrix;
		$matrix->name=array_get($data,'name');	
		$matrix->maximum=array_get($data,'maximum');	
		$matrix->description=array_get($data,'desc');
		$matrix->save();

		return Redirect::action('MatrixController@index');
	}
	/**
	 * Get the matrix details
	 *
	 * @return Response
	 */
	public function update($id){
		$matrix=Matrix::where('id','=',$id)->get()->first();
		return View::make('matrices.edit',compact('matrix'));
	}
	/**
	 * Updating the matrix details
	 *
	 * @return Response
	 */
	public function doupdate(){
		$data=Input::all();
		$validator = Validator::make($data = Input::all(), Matrix::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$matrix=Matrix::where('id','=',array_get($data,'id'))->get()->first();
		$matrix->name=array_get($data,'name');	
		$matrix->maximum=array_get($data,'maximum');	
		$matrix->description=array_get($data,'desc');
		$matrix->save();

		return Redirect::action('MatrixController@index');
	}
	/**
	 * Deleting the matrix details
	 *
	 * @return Response
	 */
	public function destroy($id){
		Matrix::destroy($id);
		return Redirect::action('MatrixController@index');
	}
 }