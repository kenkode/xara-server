<?php

class ReportsController extends \BaseController {

	


	public function members(){

		$members = Member::all();

		$organization = Organization::find(1);

		$pdf = PDF::loadView('pdf.memberlist', compact('members', 'organization'))->setPaper('a4')->setOrientation('potrait');
 	
		return $pdf->stream('MemberList.pdf');
		
	}



	public function remittance(){

		//$members = DB::table('members')->where('is_active', '=', '1')->get();

		$members = Member::all();
		$organization = Organization::find(1);

		$savingproducts = Savingproduct::all();

		$loanproducts = Loanproduct::all();

		$pdf = PDF::loadView('pdf.remittance', compact('members', 'organization', 'loanproducts', 'savingproducts'))->setPaper('a4')->setOrientation('landscape');
 	
		return $pdf->stream('Remittance.pdf');
		
	}



	public function template(){

		$members = Member::all();

		$organization = Organization::find(1);

		$pdf = PDF::loadView('pdf.blank', compact('members', 'organization'))->setPaper('a4')->setOrientation('landscape');
 	
		return $pdf->stream('Template.pdf');
		
	}



	public function loanlisting(){

		$loans = Loanaccount::all();

		$organization = Organization::find(1);

		$pdf = PDF::loadView('pdf.loanreports.loanbalances', compact('loans', 'organization'))->setPaper('a4')->setOrientation('potrait');
 	
		return $pdf->stream('Loan Listing.pdf');
		
	}



	public function loanproduct($id){

		$loans = Loanproduct::find($id);

		$organization = Organization::find(1);

		$pdf = PDF::loadView('pdf.loanreports.loanproducts', compact('loans', 'organization'))->setPaper('a4')->setOrientation('potrait');
 	
		return $pdf->stream('Loan Product Listing.pdf');
		
	}



	public function savinglisting(){

		$savings = Savingaccount::all();

		$organization = Organization::find(1);

		$pdf = PDF::loadView('pdf.savingreports.savingbalances', compact('savings', 'organization'))->setPaper('a4')->setOrientation('potrait');
 	
		return $pdf->stream('Savings Listing.pdf');
		
	}



	public function savingproduct($id){

		$saving = Savingproduct::find($id);

		$organization = Organization::find(1);

		$pdf = PDF::loadView('pdf.savingreports.savingproducts', compact('saving', 'organization'))->setPaper('a4')->setOrientation('potrait');
 	
		return $pdf->stream('Saving Product Listing.pdf');
		
	}

	public function monthlyrepayments(){

		$date = Input::get('date');
		$loanid=Input::get('member');	
		if($date!=null && $loanid!=null){
			$scrapdate=Loanrepayment::where('loanaccount_id','=',$loanid)
					  ->get();				
			$organization = Organization::find(1);
			$pdf = PDF::loadView('pdf.monthlyrepayments', compact('freebies','date','scrapdate', 'organization'))->setPaper('a4')->setOrientation('potrait');
			return $pdf->stream('Monthly Repayment Report.pdf');
		}else{
			return Redirect::back()
			->withAlarm('Please select the repayment duration and the respective member');
		}										
	}
	
	public function creditappraisal($id,$loanid){
		$member = Member::where('id','=',$id)->get()->first();
		$loans= Loanaccount::where('member_id','=',$id)
				->where('is_disbursed','=',1)
				->get();		
		$currentloan=Loanaccount::where('id','=',$loanid)
					->get()->first();		
		$savingaccount=DB::table('savingaccounts')
					->where('member_id','=',$id)->pluck('account_number');
		$savings=DB::table('savingtransactions')
				->join('savingaccounts','savingtransactions.savingaccount_id','=','savingaccounts.id')
				->where('savingaccounts.member_id','=',$id)
				->where('savingtransactions.type','=','credit')
				->sum('savingtransactions.amount');
		$shareaccount=DB::table('shareaccounts')
					->where('member_id','=',$id)->pluck('account_number');
		$shares=DB::table('sharetransactions')
				->join('shareaccounts','sharetransactions.shareaccount_id','=','shareaccounts.id')
				->where('shareaccounts.member_id','=',$id)
				->where('sharetransactions.type','=','credit')
				->sum('sharetransactions.amount');		
		$pdf = PDF::loadView('pdf.loanreports.creditappraisal', compact('member', 'loans','savings','savingaccount','shares','shareaccount','currentloan'))->setPaper('a4')->setOrientation('portrait'); 	
		return $pdf->stream('Member Credit Appraisal Report.pdf');
		
	}


	public function financials(){

		
		$report = Input::get('report_type');
		$date = Input::get('date');

		$accounts = Account::all();

		$organization = Organization::find(1);


		if($report == 'balancesheet'){

			

			$pdf = PDF::loadView('pdf.financials.balancesheet', compact('accounts', 'date', 'organization'))->setPaper('a4')->setOrientation('potrait');
 	
			return $pdf->stream('Balance Sheet.pdf');

		}


		if($report == 'income'){

			$pdf = PDF::loadView('pdf.financials.incomestatement', compact('accounts', 'date', 'organization'))->setPaper('a4')->setOrientation('potrait');
 	
			return $pdf->stream('Income Statement.pdf');

		}


		if($report == 'trialbalance'){

			$pdf = PDF::loadView('pdf.financials.trialbalance', compact('accounts', 'date', 'organization'))->setPaper('a4')->setOrientation('potrait');
 	
			return $pdf->stream('Trial Balance.pdf');

		}



	}



	


	

}
