<?php 

class ProjectsController extends \BaseController{
	/**
	*Produce current project listing
	*
	*/
	public function index(){
		$projects=Project::all();
		return View::make('projects.index',compact('projects'));
	}
	/**
	*Produce form to create a project
	*
	*/
	public function create(){
		$investment=Investment::all();
		return View::make('projects.create',compact('investment'));
	}
	/**
	*create the project
	*
	*/
	public function store(){
		$data=Input::all();
		$invest=array_get($data,'investment');
		if($invest==null){
			return Redirect::back()->withNone('Please select the investment category!!!');
		}else{
			$project=new Project;
			$project->name=array_get($data,'name');
			$project->investment_id=array_get($data,'investment');
			$project->units=array_get($data,'units');
			$project->unit_price=array_get($data,'unit_price');
			$project->description=array_get($data,'desc');
			$project->save();
			$created="The investment project created successfully!!!";
			$projects=Project::all();
			return View::make('projects.index',compact('projects','created'));
		}		
	}
	/**
	*Produce form to update project details
	*
	*/
	public function update($id){
		$investment=Investment::all();
		$project=Project::where('id','=',$id)->get()->first();
		return View::make('projects.update',compact('investment','project'));
	}
	/**
	*Delete project details
	*
	*/
	public function destroy($id){
		Project::destroy($id);
		return Redirect::back()->withDeleted('The investment project deleted permanently!!!');
	}
	/**
	*Show Project orders Listing
	*
	*/
	public function show(){
		$projects=Projectorder::where('is_approved','=',0)
				->where('is_rejected','=',0)
				->get();
		$usertype=Confide::User()->user_type;		
		if($usertype=='admin'){
			return View::make('projects.orders.index',compact('projects'));
		}else if($usertype=='member'){
			$projectile=Project::where('units','>',0)->get();
			$approvedones=Projectorder::where('is_approved','=',1)->get();
			return View::make('projects.orders.show',compact('projectile','approvedones'));
		}		
	}
	/**
	*Produce A form to create project order
	*
	*/
	public function createOrder($id){
		$project=Project::where('id','=',$id)->get()->first();
		return View::make('projects.orders.create',compact('project'));
	}
	/**
	*create project order
	*
	*/
	public function docreateOrder(){
		$data=Input::all();
		$prid=array_get($data,'project');
		$prunits=array_get($data,'units');
		$units=Project::where('id','=',$prid)->pluck('units');
		if($prunits>$units){
			return Redirect::back()->withNone('Units applied cannot be more than available units');
		}else{
			$project= new Projectorder;
			$project->project_id=$prid;
			$project->member_id=array_get($data,'member');
			$project->units=$prunits;
			$project->date=date('Y-m-d');
			$project->is_approved=0;
			$project->date_approved=date('Y-m-d');
			$project->payment_mode=array_get($data,'payment_mode');
			$project->save();

			$scheduled='The project successfully ordered, awaiting approval!!!';
			$projectile=Project::all();
			return View::make('projects.orders.show',compact('projectile','scheduled'));
		}				
	}
	/**
	*Produce A form to approve project order
	*
	*/
	public function approve($id){
		$project=Projectorder::where('id','=',$id)->get()->first();
		return View::make('projects.orders.approve',compact('project'));
	}
	/**
	*approve project order
	*
	*/
	public function doapprove(){
		$data=Input::all();
		$project=Projectorder::where('id','=',array_get($data,'project'))->get()->first();
		//Vetting the project payment feasibility
		//Obtain member details
		$member=$project->member_id;		
		$members=Member::where('id','=',$member)->get()->first();
		//Get payment mode
		$paymode=$project->payment_mode;
		$id=$project->project_id;
		$units=$project->units;
		//get the project unit price from table projects
		$unit_price=Project::where('id','=',$id)->pluck('unit_price');
		//Calculate to tal amount payable
		$totalamount=$unit_price*$units;
		/*
		*START VETTING HERE
		*/
		switch($paymode){
			case $paymode=='Loan Transfer':
				//Count number of loans member has already
				$loans=Loanaccount::where('member_id','=',$member)->count();
				if($loans<6){
					//Approve the project
					$project->is_approved=1;
					$project->units=array_get($data,'units');
					$project->date_approved=date('Y-m-d');
					$project->save();
					//Reducing the number of units available
					$reduce=Project::where('id','=',$project->project_id)->get()->first();
					$reduceunits=$reduce->units;
					$newunits=$reduceunits-array_get($data, 'units');
					$reduce->units=$newunits;
					$reduce->save();
					//Get the Loan product available::last entered
					$loanproduct=Loanproduct::orderBy('id','DESC')->get()->first();
					//Make a loan application
					$apply=new Loanaccount;
					$apply->member_id=$member;
					$apply->loanproduct_id=$loanproduct->id;
					$apply->is_new_application=0;
					$apply->application_date=date('Y-m-d');
					$apply->amount_applied=$totalamount;
					$apply->interest_rate=$loanproduct->interest_rate;
					$apply->period=$loanproduct->period;
					$apply->is_approved=TRUE;
					$apply->date_approved=date('Y-m-d');
					$apply->amount_approved=$totalamount;
					$apply->is_disbursed=TRUE;
					$apply->date_disbursed=date('Y-m-d');
					$apply->amount_disbursed=$totalamount;
					$apply->repayment_start_date=date('Y-m-d');
					$apply->account_number=Loanaccount::loanAccountNumber($apply);
					$apply->repayment_duration=$loanproduct->period;
					$apply->disbursement_id=1;					
					$apply->save();
					$date=date('Y-m-d');
					$loanamount = $totalamount + Loanaccount::getInterestAmount($apply);
					Loantransaction::disburseLoan($apply, $loanamount, $date);
					//return $memberemail;
					//Send email Notification for approved Project order
					Mail::send( 'emails.project', array('name'=>$members->name), function( $message ) use ($members){
	                        $message->to($members->email )
	                        		->subject( 'Investment Project Order Approval' );
	                });  
					//End Sending Notification
					//Yell with a message
					$approved='The project successfully approved! The amount has been transferred as a new loan for the member. ';
					$projects=Projectorder::where('is_approved','=',0)
							->where('is_rejected','=',0)
							->get();
					return View::make('projects.orders.index',compact('projects','approved'));
				}else if($loans>=6){
					return Redirect::back()->withLoan('Maximum Loan Application Reached. The project cannot be paid through loan application!!!!');
				}
			break;
			case $paymode=='Savings Transfer':
				//Get the total savings the member has already
				$savings=DB::table('savingtransactions')
				        ->join('savingaccounts','savingtransactions.savingaccount_id','=','savingaccounts.id')
				        ->where('savingaccounts.member_id','=',$member)
				        ->where('savingtransactions.type','=','credit')
				        ->sum('savingtransactions.amount');
				//Get the 80% of the savings::we do not want the savings account
				$available=0.8 * $savings;
				if($available>=$totalamount){
					//record transaction::A debit on the user account
					$transact=new Savingtransaction;
					$transact->date=date('Y-m-d');
					$transact->savingaccount_id=$member;
					$transact->amount=$totalamount;
					$transact->type='debit';
					$transact->save();
					//Now approve the investment project
					$project->is_approved=1;
					$project->units=array_get($data,'units');
					$project->date_approved=date('Y-m-d');
					$project->save();
					//Reducing the number of units available
					$reduce=Project::where('id','=',$project->project_id)->get()->first();
					$reduceunits=$reduce->units;
					$newunits=$reduceunits-array_get($data, 'units');
					$reduce->units=$newunits;
					$reduce->save();
					//Send email Notification for approved Project order
					Mail::send( 'emails.project', array('name'=>$members->name), function( $message )
					 use ($members)
					{
                        $message->to($members->email)->subject('Investment Project Order Approval');
                    });  
					//End Sending Notification					
					//Yell with a message
					$approved='The project successfully approved! The payments have been transferred from the member savings.';
					$projects=Projectorder::where('is_approved','=',0)
							->where('is_rejected','=',0)
							->get();
					return View::make('projects.orders.index',compact('projects','approved'));	
				}else{
					return Redirect::back()->withInadequate('Low Member Savings. The project cannot be paid via member savings transfer!!!!');
				}
			break;
			case $paymode=='Mpesa Payment':
				//flash the message that funds has been received
				//Approve the project
				$project->is_approved=1;
				$project->units=array_get($data,'units');
				$project->date_approved=date('Y-m-d');
				$project->save();
				//Reducing the number of units available
				$reduce=Project::where('id','=',$project->project_id)->get()->first();
				$reduceunits=$reduce->units;
				$newunits=$reduceunits-array_get($data, 'units');
				$reduce->units=$newunits;
				$reduce->save();
				//Send email Notification for approved Project order
				Mail::send( 'emails.project', array('name'=>$members->name), function( $message )
				 use($members)
				{
                    $message->to($members->email )
                    		->subject( 'Investment Project Order Approval' );
                });  
					//End Sending Notification					
				//Yell with a message
				$approved='The project successfully approved!';
				$projects=Projectorder::where('is_approved','=',0)
							->where('is_rejected','=',0)
							->get();
				return View::make('projects.orders.index',compact('projects','approved'));	
			break;
			case $paymode=='Cash Payment':
				//flash the message that funds has been received
				//Approve the project
				$project->is_approved=1;
				$project->units=array_get($data,'units');
				$project->date_approved=date('Y-m-d');
				$project->save();
				//Reducing the number of units available
				$reduce=Project::where('id','=',$project->project_id)->get()->first();
				$reduceunits=$reduce->units;
				$newunits=$reduceunits-array_get($data, 'units');
				$reduce->units=$newunits;
				$reduce->save();	
				//Send email Notification for approved Project order
				Mail::send( 'emails.project', array('name'=>$members->name), function( $message )
				 use ($members)
				{
                    $message->to($members->email )
                    		->subject( 'Investment Project Order Approval' );
                });  
				//End Sending Notification				
				//Yell with a message
				$approved='The project successfully approved!';
				$projects=Projectorder::where('is_approved','=',0)
							->where('is_rejected','=',0)
							->get();
				return View::make('projects.orders.index',compact('projects','approved'));	
			break;
		}
		/*
		*END VETTING HERE
		*/			
	}
	/**
	*Produce A form to reject project order
	*
	*/
	public function reject($id){
		$project=Projectorder::where('id','=',$id)->get()->first();
		return View::make('projects.orders.reject',compact('project'));
	}
	/**
	*reject project order
	*
	*/
	public function doreject(){
		$data=Input::all();
		$project=Projectorder::where('id','=',array_get($data,'project'))->get()->first();	
		//Obtain member details
		$member=$project->member_id;		
		$members=Member::where('id','=',$member)->get()->first();		
		$project->is_rejected=1;		
		$project->date_rejected=date('Y-m-d');
		$project->save();

		//Send email Notification for approved Project order
		Mail::send( 'emails.rejection', array('name'=>$members->name), function( $message )
		 use ($members)
		{
            $message->to($members->email)
            		->subject( 'Investment Project Order Rejection' );
        });  
		//End Sending Notification	

		$rejected='Project was succesfully rejected!!!';
		$projects=Projectorder::where('is_approved','=',0)
				->where('is_rejected','=',0)
				->get();
		return View::make('projects.orders.index',compact('projects','rejected'));
	}
	/**
	*Form to update project order details
	*
	*/
	public function updateOrder($id){
		$project=Projectorder::where('id','=',$id)->get()->first();
		return View::make('projects.orders.update',compact('project'));
	}
	/**
	*update project order details
	*
	*/
	public function doupdateOrder(){
		$data=Input::all();
		$prid=array_get($data,'project');
		$pr=Projectorder::where('id','=',$prid)->get()->first();
		$prunits=array_get($data,'units');
		$units=Project::where('id','=',$pr->project_id)->pluck('units');
		if($prunits>$units || $prunits<1){
			return Redirect::back()->withNone('Units applied cannot be more than available units');
		}else{
			$project=Projectorder::where('id','=',$prid)->get()->first();						
			$project->units=$prunits;			
			$project->save();

			$updated='The project successfully updated!!!';
			$projects=Projectorder::where('is_approved','=',0)
					->where('is_rejected','=',0)
					->get();
			return View::make('projects.orders.index',compact('projects','updated'));
		}				
	}
	/**
	*Delete project order details
	*
	*/
	public function diminishOrder($id){
		Projectorder::destroy($id);
		return Redirect::back()->withDeleted('The project deleted permanently!!!');
	}
	/**
	*update project details
	*
	*/
	public function doupdate(){
		$data=Input::all();
		$invest=array_get($data,'investment');
		if($invest==null){
			return Redirect::back()->withNone('Please select the investment category!!!');
		}else{
			$project=Project::where('id','=',array_get($data,'id'))->get()->first();
			$project->name=array_get($data,'name');
			$project->investment_id=array_get($data,'investment');
			$project->units=array_get($data,'units');
			$project->unit_price=array_get($data,'unit_price');
			$project->description=array_get($data,'desc');
			$project->save();

			$updated="The investment project updated successfully!!!";
			$projects=Project::all();
			return View::make('projects.index',compact('projects','updated'));
		}
	}		
}