<?php

class InvestmentcategoriesController extends \BaseController{
	/*
	*
	*Display Investments Categories
	* @return Response
	*/
	public function index(){
		$organization=Organization::find(1);
		$cats=Investmentcategory::all();
		return View::make('investmentcats.index',compact('cats','organization'));
	}
	/*
	*
	*Display Form to create investment category
	*/
	public function create(){
		return View::make('investmentcats.create');
	}
	/*
	*
	*Create  investment category
	*/
	public function store(){
		$data=Input::all();
		$cat=new Investmentcategory;
		$cat->name=array_get($data, 'name');
		$cat->code=array_get($data, 'code');
		$cat->description=array_get($data, 'desc');
		$cat->save();

		$catdone='Investment Category successfully created!!!';
		$cats=Investmentcategory::all();
		$organization=Organization::find(1);
		return View::make('investmentcats.index',compact('cats','organization','catdone'));
	}
	/*
	*
	*Form to update investment category
	*/
	public function update($id){
		$cats=Investmentcategory::where('id','=',$id)->get()->first();
		return View::make('investmentcats.edit',compact('cats'));
	}
	/*
	*
	*update investment category
	*/
	public function doupdate(){
		$data=Input::all();
		$catinv=Investmentcategory::where('id','=',array_get($data,'id'))->get()->first();
		$catinv->name=array_get($data,'name');
		$catinv->code=array_get($data, 'code');
		$catinv->description=array_get($data, 'desc');
		$catinv->save();

		$catupdated="The investment category successfully updated!!!";
		$cats=Investmentcategory::all();
		$organization=Organization::find(1);
		return View::make('investmentcats.index',compact('cats','catupdated','organization'));
	}
	/*
	*
	*deleting an investment category
	*/
	public function destroy($id){
		Investmentcategory::destroy($id);
		return Redirect::back()->withPuff('The investment category deleted permanently!!!');
	}
}