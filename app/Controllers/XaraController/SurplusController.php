<?php

class SurplusController extends \BaseController {

	public function index(){
		$cats=Surplus::where('id','=',1)->get();
		return View::make('surplus.index',compact('cats'));
	}

	public function create(){
		return View::make('surplus.create');
	}

	public function store(){
		$data=Input::all();
		$extra=new Surplus;
		$extra->rate=$data['rate'];
		$extra->frequency=$data['frequency'];
		$extra->action=$data['intention'];
		$extra->save();
		return Redirect::action('SurplusController@index');
	}

	public function editSetting($id){
		$setting=Surplus::where('id',$id)->get()->first();		
		return View::make('surplus.edit',compact('setting'));
	}

	public function update(){
		$data=Input::all();
		$extra=Surplus::where('id',$data['surplus_id'])->get()->first();
		$extra->rate=$data['rate'];
		$extra->frequency=$data['frequency'];
		$extra->action=$data['intention'];
		$extra->save();
		return Redirect::action('SurplusController@index');
	}

	public function show(){
		$members=Member::all();
		$settings=Surplus::where('id',1)->get()->first();
		return View::make('surplus.distribution',compact('members','settings'));
	}


























}

