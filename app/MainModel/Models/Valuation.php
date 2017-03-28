<?php

class Valuation extends \Eloquent {

	protected $table='valuations';
	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = [];
	/*
	*Appreciate/Depreciate Investment Valuation
	*
	*/
	public static function calculateValuation($investment){
		$invest=Investment::find($investment);
		//dd($invest);
		$value=$invest->valuation;
		$rate=$invest->growth_rate/100;
		$type=$invest->growth_type;		
		$curmonth=date('m');
		$curyear=date('Y');
		//$endpoint=30 *60;>>Approx. Five years
		$startdate=date('Y-m-d',strtotime($invest->date));   
        $nextdate=date('Y-m-d',strtotime($startdate. '+30 days'));
        if(date('Y-m-d')==$nextdate){
        	switch($type){
        		//Investment that appreciates
        		case $type=='Appreciation':
        			$grate=1+$rate;
        			$newvalue=round(($grate*$value),2);
        			//Update current valuation
        			$invest->valuation=$newvalue;
        			$invest->date=date('Y-m-d');
        			$invest->update();
        			//Record the valuation
        			$valued=new Valuation;
        			$valued->investment_id=$investment;
        			$valued->valuation=$newvalue;
        			$valued->month=$curmonth;
        			$valued->year=$curyear;
        			$valued->date=date('Y-m-d');
        			$valued->save();
        			//Update the unit_price for anything associated with that investment
        			$project=Project::where('investment_id','=',$investment)->get();
        			$project->unit_price=round(($grate*$project->unit_price),2);
        			$project->update();
        		break;
        		//Investments that depreciates
        		case $type=='Depreciation':
        			$grate=1-$rate;
        			$newvalue=round(($grate*$value),2);
        			//Update current valuation
        			$invest->valuation=$newvalue;
        			$invest->date=date('Y-m-d');
        			$invest->update();
        			//Record the valuation
        			$valued=new Valuation;
        			$valued->investment_id=$investment;
        			$valued->valuation=$newvalue;
        			$valued->month=$curmonth;
        			$valued->year=$curyear;
        			$valued->date=date('Y-m-d');
        			$valued->save();
        			//Update the unit_price for anything associated with that investment
        			$project=Project::where('investment_id','=',$investment)->get();
        			$project->unit_price=round(($grate*$project->unit_price),2);
        			$project->update();
        		break;
        	}
        }        	
                
	}
}