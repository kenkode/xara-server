<?php

class InvestmentController extends \BaseController{

	public function update($id){
		$invest=Investment::where('id','=',$id)->get()->first(); 
	    $vendor=Vendor::all();  
	    $cats=Investmentcategory::all();
	    return View::make('css.editinvestment',compact('invest','vendor','cats'));
	}	

	public function doupdate(){
		$data=Input::all();
		$invest=Investment::where('id','=',array_get($data,'investment_id'))->get()->first();
	    $invest->name=array_get($data,'investment');
	    $invest->vendor_id=array_get($data,'vendor');
	    $invest->valuation=array_get($data,'valuation');
	    $invest->growth_type=array_get($data,'growth_type');
	    $invest->growth_rate=array_get($data,'growth_rate');
	    $invest->description=array_get($data,'desc');
	    $invest->category_id=array_get($data,'category');	    
	    $invest->save();

	    $grab="The Investment successfully updated!!!";
	    $investment=Investment::all();
    	$organization=Organization::find(1);
	    return View::make('css.saccoinvestments',compact('grab','investment','organization'));
	}

	public function destroy($id){
		Investment::destroy($id);
		$exit="The investment deleted permanently!!!";
	    $investment=Investment::all();
    	$organization=Organization::find(1);
	    return View::make('css.saccoinvestments',compact('exit','investment','organization'));		
	}

}


?>