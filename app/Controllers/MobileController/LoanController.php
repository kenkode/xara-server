<?php

  namespace App\Controllers\MobileController;

  use \App\Controllers\Controller;
  use \App\Models\Loanaccount;
  use \App\Models\Audit;
  use \App\Models\Journal;
  use \App\Models\Account;
  use \App\Models\LoanRepayment;
  use \App\Models\Member;
  use \App\Models\LoanTransaction;
  use \App\Models\User;
  use \App\Models\LoanGuarantor;
  use \App\Models\DisbursementOption;
  use \App\Models\LoanProduct;
  use \App\Models\Savingsaccount;
  use \App\Models\SavingProduct;
  use \App\Models\Savingposting;
  use \App\Models\SavingTransaction;
  use \App\Models\ShareTransaction;
  use Illuminate\Database\Capsule\Manager as DB;

  /**
   *  Controller for mobile xara application
   */
  class LoanController extends Controller
  {

    function __construct($controller)
    {
        $this->controller = $controller;
    }

    public function authenticateUser($request, $response, $args) {
      $data;
      $username = $request->getParam("username");
      $password = $request->getParam("password");

      if(trim($username) && trim($password)) {
       $data['status'] = true;
       $user = User::where('username',$username)->orWhere('email',$username)->first();
       if(password_verify ( $password , $user->password ) == true){
         $data['user'] = $user;
       }
      }else {
        $data['status'] = false;
        $data['exist'] = 0;
      }

      echo json_encode($data);
    }

  public function getTotalLoansAndSavings($request, $response, $args)
  {
      $data;
      $mno = $request->getParam("memberno");
      $type = $request->getParam("type");

      if($type == "admin"){
      $members = Member::all();
     $savings   = SavingTransaction::join('x_savingaccounts','x_savingtransactions.savingaccount_id','=','x_savingaccounts.id')
                          ->where('x_savingtransactions.type','credit')
                          ->sum('amount');
     
      $shares   = ShareTransaction::join('x_shareaccounts','x_sharetransactions.shareaccount_id','=','x_shareaccounts.id')
                          ->where('x_sharetransactions.type','credit')
                          ->sum('amount');
      
      $remittance   = Member::sum('monthly_remittance_amount');

      $amount_to_date   = $savings + $remittance+$shares;

     
      $principal_paid   = LoanRepayment::join('x_loanaccounts','x_loanrepayments.loanaccount_id','=','x_loanaccounts.id')
                          ->sum('principal_paid');
      

      $amount_disbursed   = Loanaccount::sum('amount_disbursed');

      $top_up = Loanaccount::sum('top_up_amount');

      $balance_to_date = $amount_disbursed + $top_up - $principal_paid;

      // Transactions
      $transactions = Loantransaction::join('x_loanaccounts','x_loantransactions.loanaccount_id','=','x_loanaccounts.id')
            ->join('x_members','x_loanaccounts.member_id','=','x_members.id')
            ->select('x_loantransactions.created_at', 'amount', 'name','membership_no','type', 'description', 'amount', 'trans_no')
            ->latest()->take(10)->get();

      $data['savings'] = $amount_to_date;
      $data['loans'] = $balance_to_date;
      $data['transactions'] = $transactions;
      }else{
      
      $member = Member::where('membership_no',$mno)->first();
      $transaction = Loanaccount::where('member_id',$member->id)->first();
      $saving   = SavingTransaction::join('x_savingaccounts','x_savingtransactions.savingaccount_id','=','x_savingaccounts.id')
                          ->where('member_id',$member->id)
                          ->where('x_savingtransactions.type','credit')
                          ->select(DB::raw("COALESCE(sum(amount),0.00) as amount"))
                          ->first();
      $shares   = ShareTransaction::join('x_shareaccounts','x_sharetransactions.shareaccount_id','=','x_shareaccounts.id')
                          ->where('member_id',$member->id)
                          ->where('x_sharetransactions.type','credit')
                          ->select(DB::raw("COALESCE(sum(amount),0.00) as amount"))
                          ->first();

      $remittance   = Member::where('x_members.id',$member->id)
                          ->sum('monthly_remittance_amount');

      $amount_to_date   = $saving->amount + $remittance + $shares->amount;

      $principal_paid   = LoanRepayment::join('x_loanaccounts','x_loanrepayments.loanaccount_id','=','x_loanaccounts.id')
                          ->where('member_id',$member->id)
                          ->select(DB::raw("COALESCE(sum(principal_paid),0.00) as amount"))
                          ->first();

      $amount_disbursed   = Loanaccount::where('member_id',$member->id)
                          ->select(DB::raw("COALESCE(sum(amount_disbursed),0.00) as amount"))
                          ->first();

      $top_up = Loanaccount::where('member_id',$member->id)
                          ->select(DB::raw("COALESCE(sum(amount_disbursed),0.00) as amount"))
                          ->first();

      $balance_to_date = $amount_disbursed->amount + $top_up->amount - $principal_paid->amount;

      // Transactions
      $transactions = Loantransaction::join('x_loanaccounts','x_loantransactions.loanaccount_id','=','x_loanaccounts.id')
            ->join('x_members','x_loanaccounts.member_id','=','x_members.id')
            ->where('member_id',$member->id)
            ->select('x_loantransactions.created_at', 'amount', 'type','membership_no','name', 'description', 'amount', 'trans_no')
            ->latest()->take(10)->get();

      $data['savings'] = $amount_to_date;
      $data['loans'] = $balance_to_date;
      $data['transactions'] = $transactions;
      }

      
      echo json_encode($data);

  }

  public function getLoans($request, $response) {
    $loanaccounts;
    $user_id = $request->getParam("user_id"); 
    $user = User::where('id', $user_id)->first();
    $mno=$user->username;
    $member=Member::where("membership_no",$mno)->first();
    if(count($user)>0) {
	if($user->user_type=='member'){
        $loanaccounts=Loanaccount::where("x_loanaccounts.member_id", $member->id)
            ->join("x_loanproducts", "x_loanaccounts.loanproduct_id","=", "x_loanproducts.id")
            ->join("x_members", "x_loanaccounts.member_id", "x_members.id")
	    ->where("is_new_application","=",0)
	    ->where("is_disbursed","=",1)
	    ->where("is_approved","=",1)
	    ->select("x_loanaccounts.id", "is_approved", "x_loanproducts.interest_rate", "x_loanproducts.name as loan_name",
                "x_members.name as user_name","amount_applied", "amount_disbursed", "repayment_duration", "top_up_amount", "currency",
                "application_date", "repayment_start_date", "x_members.id as user_id", "phone", "group_id", "email")
             ->get();
	}else if($user->user_type=='admin'){
	  $loanaccounts=Loanaccount::where("x_loanaccounts.member_id",$member->id)
			->join("x_loanproducts", "x_loanaccounts.loanproduct_id","=","x_loanproducts.id")
			->join("x_members","x_loanaccounts.member_id","=","x_members.id")
		        ->where("is_new_application","=",0)
			->select("x_loanaccounts.id", "is_approved", "x_loanproducts.interest_rate", "x_loanproducts.name as loan_name",
          		      "x_members.name as user_name","amount_applied", "amount_disbursed", "repayment_duration", "top_up_amount", "currency",
		                "application_date", "repayment_start_date", "x_members.id as user_id", "phone", "group_id", "email")
          	         ->get();
	
	}
    }else{
      $loanaccounts = "NO LOANS AVAILABLE";
    }

    echo json_encode($loanaccounts);
  }

  public function getLoan($request, $response, $args) {
    $data;
    $user_id = $request->getParam('user_id');
    $amount_applied= $request->getParam('amount_applied');
    $product=$request->getParam('loan_product');
    $disburse=$request->getParam('disburse_option');
	/*Obtain the selected disbursement option and loan product by the user*/
    $option=DisbursementOption::where('name',$disburse)->first();
    $product=LoanProduct::where('name',$product)->first();
    $mno= User::where('id',$user_id)->pluck('username');
    $member= Member::where('membership_no',$mno)->first();
    $count=Loanaccount::where('member_id',$member->id)->count();
    $count= $count + 1;
    /*Make a Loan Application*/
    	$apply=new Loanaccount;
	$apply->member_id=$member->id;
	$apply->is_new_application=1;
	$apply->loanproduct_id=$product->id;
	$apply->account_number=$product->short_name."-".$member->membership_no."-".$count;
	$apply->application_date=date('Y-m-d');
	$apply->amount_applied=$amount_applied;
	$apply->interest_rate=$product->interest_rate;
	$apply->period=$product->period;
	$apply->disbursement_id=$option->id;
	$apply->repayment_duration=$product->period;
	$apply->save();
	$data['success']="Application Received";
    echo json_encode($data);
  }

 public function getSavings($request, $response) {
    $savingsaccount;
    $user_id = $request->getParam("user_id");
    $user = User::where("id", $user_id)->first();
    $member = Member::where('membership_no',$user->username)->first();
    if(count($user) > 0) {
      if($user->user_type=='member'){

      

      $savingsaccount   = SavingTransaction::join('x_savingaccounts','x_savingtransactions.savingaccount_id','=','x_savingaccounts.id')
                          ->join('x_savingproducts','x_savingaccounts.savingproduct_id','=','x_savingproducts.id')
                          ->join('x_members','x_savingaccounts.member_id','=','x_members.id')
                          ->where('member_id',$member->id)
                          ->select("x_savingaccounts.id", "x_savingproducts.type as product", "x_savingtransactions.type as type", "opening_balance", "x_members.name as name", "x_savingtransactions.amount", "currency",
                             "x_members.id as user_id", "phone", "group_id", "email", "x_savingtransactions.date")
        
                          ->get();
      }

      else if($user->user_type=='admin'){
        $savingsaccount   = SavingTransaction::join('x_savingaccounts','x_savingtransactions.savingaccount_id','=','x_savingaccounts.id')
                          ->join('x_savingproducts','x_savingaccounts.savingproduct_id','=','x_savingproducts.id')
                          ->join('x_members','x_savingaccounts.member_id','=','x_members.id')
                          ->where('member_id',$member->id)
                          ->select("x_savingaccounts.id", "x_savingproducts.type as product", "x_savingtransactions.type as type", "opening_balance", "x_members.name as name", "x_savingtransactions.amount", "x_savingtransactions.date", "currency",
                             "x_members.id as user_id", "phone", "group_id", "email")
                          ->get();
      }
    }else {
      $savingsaccount = "No Savings";
    }

    echo json_encode($savingsaccount);
  }

  public function getSavingDetails($request, $response, $args) {
    $data;
    $saving_id = $request->getParam("saving_id");
    $user_id = $request->getParam("user_id");
    $user = User::where("id", $user_id)->first();
    $member = Member::where('membership_no',$user->username)->first();

    $data   = SavingTransaction::join('x_savingaccounts','x_savingtransactions.savingaccount_id','=','x_savingaccounts.id')
                          ->join('x_savingproducts','x_savingaccounts.savingproduct_id','=','x_savingproducts.id')
                          ->join('x_members','x_savingaccounts.member_id','=','x_members.id')
                          ->where('member_id',$member->id)
                          ->where('x_savingaccounts.id',$saving_id)
                          ->select("x_savingaccounts.id", "x_savingproducts.type as product", "x_savingtransactions.type as type", "x_savingaccounts.account_number", "x_members.name as name", "x_savingtransactions.amount", "currency",
                             "x_members.id as user_id", "phone", "group_id", "email", "x_savingtransactions.date")
        
                          ->first();
      

    echo json_encode($data);

  }


  public function getSaving($request, $response){
        $data;
    $loan_id = 66;
    $user_id = 1;
    $user_r = array(
      'loanaccount_id' => $loan_id,
      'member_id' => $user_id
    );

    $user_t = array(
      'loanaccount_id' => $loan_id,
      'id' => $user_id
    );

    $loanaccount = Loanaccount::join("x_loanproducts", "loanproduct_id", "x_loanproducts.id")
                              ->where("x_loanaccounts.id", $loan_id)
                              ->first();

    $amount_paid = LoanRepayment::join('x_loanaccounts','x_loanrepayments.loanaccount_id','=','x_loanaccounts.id')
                                ->where($user_r)->sum('principal_paid');
    $interest_paid = LoanRepayment::join('x_loanaccounts','x_loanrepayments.loanaccount_id','=','x_loanaccounts.id')
                                ->where($user_r)->sum('interest_paid');

    $payments = LoanTransaction::join('x_loanaccounts','x_loantransactions.loanaccount_id','=','x_loanaccounts.id')
                                ->where($user_r)
                                ->where('type', '=', 'credit')->sum('amount');
    $loanamount = Loantransaction::join('x_loanaccounts','x_loantransactions.loanaccount_id','=','x_loanaccounts.id')
                                ->where($user_r)
                                ->where('type', '=', 'debit')->sum('amount');

    $balance = $loanamount - $payments;

    $data['amount_paid'] = $amount_paid;
    $data['interest_paid'] = $interest_paid;
    $data['balance'] = $balance;

    $principal_paid = LoanRepayment::join('x_loanaccounts','x_loanrepayments.loanaccount_id','=','x_loanaccounts.id')
                        ->where($user_r)
                        ->sum('principal_paid');

    $amount_disbursed = Loanaccount::where('member_id',$user_id)
                        ->sum('amount_disbursed');

    $top_up = Loanaccount::where('member_id',$user_id)
                        ->sum('top_up_amount');

    $balance_to_date = $amount_disbursed + $top_up - $principal_paid;

    $data['total_interest'] = $this->getInterestAmount($balance_to_date, $loanaccount);

    echo json_encode($data);
  }

  public function getSavingApplicationDetails($request, $response) {
    $data = [];

    $products = SavingProduct::all();

    $data['products'] = $products;

    echo json_encode($data);
  }

   public function getSavingStore($request, $response)
  {
    $data = [];

    $user_id = $request->getParam("user_id");
    $user = User::where("id", $user_id)->first();
    $member = Member::where('membership_no',$user->username)->first();

    $date = date('Y-m-d');
    $transAmount = $request->getParam("amount");

    $savingaccount = Savingsaccount::where('member_id',$member->id)->first();

    $savingtransaction = new Savingtransaction;

    $savingtransaction->date = $date;
    $savingtransaction->savingaccount_id = $savingaccount->id;
    $savingtransaction->amount = $request->getParam("amount");
    if($request->getParam("type") == 'Withdraw'){
    $savingtransaction->type = "debit";
    }else{
    $savingtransaction->type = "credit";
    }
    $savingtransaction->description = "";
    $savingtransaction->payment_mode = $request->getParam("mode");
    $savingtransaction->transacted_by = $request->getParam("username")." : ".$member->name;


  
    // withdrawal 

    if($savingtransaction->save()){

    if($request->getParam("type") == 'Withdraw'){


      $savingpostings = Savingsaccount::join("x_savingproducts", "x_savingaccounts.savingproduct_id", "x_savingproducts.id")
                      ->join("x_savingpostings", "x_savingproducts.id", "x_savingpostings.savingproduct_id")
                      ->get();


      foreach($savingpostings as $posting){

        if($posting->transaction == 'withdrawal'){

          $debit_account = $posting->debit_account;
          $credit_account = $posting->credit_account;
        }
      }


      $data = array(
        'credit_account' => $credit_account,
        'debit_account' => $debit_account,
        'date' => date('Y-m-d'),
        'amount' => $request->getParam("amount"),
        'initiated_by' => 'system',
        'description' => 'cash withdrawal'
        );

      $date = date('Y-m-d H:m:s');
      $trans_no  = strtotime($date);

    $journal = new Journal;
    $account = Account::findOrFail($data['credit_account']);
    $journal->account_id = $account->id;

    $journal->date = $data['date'];
    $journal->trans_no = $trans_no;
    $journal->initiated_by = $data['initiated_by'];
    $journal->amount = $data['amount'];
    $journal->type = 'credit';
    $journal->description = $data['description'];
    $journal->save();

    $journal = new Journal;
    $debaccount = Account::findOrFail($data['debit_account']);
    $journal->account_id = $debaccount->id;

    $journal->date = $data['date'];
    $journal->trans_no = $trans_no;
    $journal->initiated_by = $data['initiated_by'];
    $journal->amount = $data['amount'];
    $journal->type = 'debit';
    $journal->description = $data['description'];
    $journal->save();

      /*$journal = new Journal;


      $journal->journal_entry($data);*/

    $audit = new Audit;

    $audit->date = date('Y-m-d');
    $audit->user = $request->getParam("username")." : ".$member->name;
    $audit->action = 'savings withdrawal';
    $audit->entity = 'Savings';
    $audit->amount = $request->getParam("amount");
    $audit->save();

    }


    // deposit
    if($request->getParam("type") == 'Deposit'){

      $savingpostings = Savingsaccount::join("x_savingproducts", "x_savingaccounts.savingproduct_id", "x_savingproducts.id")
                      ->join("x_savingpostings", "x_savingproducts.id", "x_savingpostings.savingproduct_id")
                      ->get();


      foreach($savingpostings as $posting){

        if($posting->transaction == 'deposit'){

          $debit_account = $posting->debit_account;
          $credit_account = $posting->credit_account;
        }
      }



      $data = array(
        'credit_account' => $credit_account,
        'debit_account' => $debit_account,
        'date' => date('Y-m-d'),
        'amount' => $request->getParam("amount"),
        'initiated_by' => 'system',
        'description' => 'cash deposit'
        );

      $date = date('Y-m-d H:m:s');
      $trans_no  = strtotime($date);

    $journal = new Journal;
    $account = Account::findOrFail($data['credit_account']);
    $journal->account_id = $account->id;

    $journal->date = $data['date'];
    $journal->trans_no = $trans_no;
    $journal->initiated_by = $data['initiated_by'];
    $journal->amount = $data['amount'];
    $journal->type = 'credit';
    $journal->description = $data['description'];
    $journal->save();

    $journal = new Journal;
    $debaccount = Account::findOrFail($data['debit_account']);
    $journal->account_id = $debaccount->id;

    $journal->date = $data['date'];
    $journal->trans_no = $trans_no;
    $journal->initiated_by = $data['initiated_by'];
    $journal->amount = $data['amount'];
    $journal->type = 'debit';
    $journal->description = $data['description'];
    $journal->save();

      /*$journal = new Journal;


      $journal->journal_entry($data);*/

    $audit = new Audit;

    $audit->date = date('Y-m-d');
    $audit->user = $request->getParam("username")." : ".$member->name;
    $audit->action = 'savings deposit';
    $audit->entity = 'Savings';
    $audit->amount = $request->getParam("amount");
    $audit->save();
      
    }

    $data['result'] = "0";
    echo json_encode($data);

    }else{
    $data['result'] = "1";
    echo json_encode($data);
    }
  }

  public static function getInterestAmount($principal, $loanaccount){
    $interest_amount = 0;
		$rate = $loanaccount->interest_rate/100;
		$onerate = 1 + $rate;
		$time = $loanaccount->repayment_duration;
		$formula = $loanaccount->formula;
		if($formula == 'SL'){
			$interest_amount = $principal * $rate * $time;
		}
		if($formula == 'RB'){
	 		if($loanaccount->repayment_duration > 0){
	 			$timer=$loanaccount->repayment_duration;
	 		}else{
	 			$timer=$loanaccount->period;
	 		}
   			$principal_bal = round(($rate*$principal)/(1-(pow($onerate,-$timer))),2);
    		$interest_amount = 0;
        	for($i=1;$i<=$time;$i++){
        		$interest_amount=($principal_bal*$timer)-($principal);
        	}
		}
		return $interest_amount;
	}

  public function getAppliedLoans($request, $response){ 
	$appliedLoans = Loanaccount::join("x_members", "x_loanaccounts.member_id","=", "x_members.id")
                            ->join("x_loanproducts", "x_loanaccounts.loanproduct_id", "=","x_loanproducts.id")
			    ->where("is_new_application","=",1)
                            ->select("x_loanaccounts.id", "x_loanaccounts.is_approved", "x_loanproducts.interest_rate", "x_loanproducts.name as loan_name",
                            "x_members.name as user_name","x_loanaccounts.amount_applied", "x_loanaccounts.amount_disbursed", "repayment_duration", "top_up_amount", "currency",
                            "application_date", "repayment_start_date", "x_members.id as user_id", "phone", "group_id", "email")
                            ->get();

      echo json_encode($appliedLoans);

  }

  public function getLoanGuarantors($request, $response) {
    $loanaccount = $request->getParam("loanaccount_id");
    $member_id = $request->getParam("member_id");

    $data = array(
      "loanaccount_id" => $loanaccount,
      "member_id" => $member_id
    );

    $guarantors = LoanGuarantor::join("x_members", "x_members.id", "x_loanguarantors.id")
                              ->where($data)->get();

    echo json_encode($guarantors);
  }

  public function getGuarantors($request, $response) {
    $member_id = $request->getParam("member_id");
    $group_id = $request->getParam("group_id");

    $data = array(
      "group_id" => $group_id,
      "member_id" => $member_id
    );
    $guarantors = Member::all();
    echo json_encode($guarantors);
  }

  public function getAccounts($request, $response) {
    $group_id = $request->getParam("group_id");
    $member = $request->getParam("member");
    $accounts = Member::whereNotIn("id", [$member])->get();

    echo json_encode($accounts);

  }

  public function getApplicationDetails($request, $response) {
    $data = [];

    $disburseoptions = DisbursementOption::all();
    $products = LoanProduct::all();

    $data['disburseoptions'] = $disburseoptions;
    $data['products'] = $products;

    echo json_encode($data);
  }

  public function loginUser($request, $response, $args) {
   $username = $request->getParam('username'); 
   $password = $request->getParam('password'); 

   $user = User::where('username',$username)->orWhere('email',$username)->first();
 	  if(password_verify ( $password , $user->password ) == true){
  		 echo json_encode($user);
	   }
   }
   /*Approve and Disburse a Loan Application*/
  public function approveDisburse($request, $response,$args){
	$data;
	$total_loan;
	$loan_id=$request->getParam('loan_id');
	$loan_amount=$request->getParam('loan_amount');
	$toApprove=Loanaccount::where("id","=",$loan_id)->first();
	$toApprove->is_new_application=0;
	$toApprove->secretary_approved=1;
	$toApprove->chairman_approved=1;
	$toApprove->is_approved=1;
	$toApprove->amount_approved=$loan_amount;
	$toApprove->date_approved=date('Y-m-d');
	$toApprove->is_approved=1;
	$toApprove->amount_disbursed=$loan_amount;
	$toApprove->date_disbursed=date('Y-m-d');
	$toApprove->is_disbursed=1;
	$toApprove->save();
	/*Calculate the total loan amount plus interest amount*/
		$toApprove1=Loanaccount::where("id","=",$loan_id)->first();
		$product=LoanProduct::where("id","=",$toApprove1->loanproduct_id)->first();
		$rate = $toApprove1->interest_rate/100;
		$onerate = 1 + $rate;
		$time = $toApprove1->repayment_duration;
		$formula = $product->formula;
		if($formula == 'SL'){
			$interest_amount = $loan_amount * $rate * $time;
			$total_loan=$loan_amount + $interest_amount;
		}else if($formula == 'RB'){
	 		if($toApprove1->repayment_duration > 0){
	 			$timer=$toApprove1->repayment_duration;
	 		}else{
	 			$timer=$toApprove1->period;
	 		}
   			$principal_bal = round(($rate*$loan_amount)/(1-(pow($onerate,-$timer))),2);
        		$interest_amount=($principal_bal*$timer)-($loan_amount);
			$total_loan= $loan_amount + $interest_amount;
		}
	/*A Loan Transaction*/
	$transaction=new Loantransaction;
	$transaction->loanaccount_id=$loan_id;
	$transaction->date=date('Y-m-d');
	$transaction->description="loan disbursement";
	$transaction->amount=$total_loan;
	$transaction->type='debit';
	$transaction->save();
	/*Get posting accounts*/
	$loan=Loanaccount::where("id","=",$loan_id)->first();
	$lid=$loan->loanproduct_id;
	$posting=DB::table("x_loanpostings")->where("loanproduct_id",$lid)->where("transaction","=","disbursal")->get();
	foreach($posting as $posted){
		$credit_account=$posted->credit_account;
		$debit_account=$posted->debit_account;
	}
	/*Specify the accounts*/
	$accounts=array('debit'=>$debit_account,'credit'=>$credit_account);
	$datar=array('credit_account'=>$accounts['credit'],'debit_account'=>$accounts['debit'],'date'=>date('Y-m-d'),'amount'=>$total_loan,'initiated_by'=>'system',
	'description'=>'loan disbursement');
	/*Obtaining the TRansaction number*/
	$tarehe=date('Y-m-d H:m:s');
	$transact_no=strtotime($tarehe);
	/*Debit Journal Entry*/
	$jrn=new Journal;
	$acc=Account::find($datar['debit_account']);
	$jrn->account_id=$acc->id;
	$jrn->date=$datar['date'];
	$tarehe=date('Y-m-d H:m:s');
	$transact_no=strtotime($tarehe);
	$jrn->trans_no=$transact_no;
	$jrn->initiated_by=$datar['initiated_by'];
	$jrn->amount=$datar['amount'];
	$jrn->type='debit';
	$jrn->description=$datar['description'];
	$jrn->save();
	/*Credit Journal Entry*/
	$jrn1=new Journal;
	$acc1=Account::find($datar['credit_account']);
	$jrn1->account_id=$acc1->id;
	$jrn1->date=$datar['date'];
	$tarehe=date('Y-m-d H:m:s');
	$transact_no=strtotime($tarehe);
	$jrn1->trans_no=$transact_no;
	$jrn1->initiated_by=$datar['initiated_by'];
	$jrn1->amount=$datar['amount'];
	$jrn1->type='credit';
	$jrn1->description=$datar['description'];
	$jrn1->save();
	/*Return a response*/
	$data['approved']="Loan Approved";
	echo json_encode($data);
  }
  /*Reject Loan Application*/
  public function rejectLoan($request,$response, $args){
	$data;
	$loan_id=$request->getParam('loan_id');
	$toReject=Loanaccount::where("id",$loan_id)->first();
	$toReject->is_rejected=1;
	$toRejct->rejection_reason="Application Not Viable";
	$toReject->save();
	/*Return a response*/
	$data['rejected']="Application Rejected";
	echo json_encode($data);
  }
  /*Loan Top Up*/
  public function topUp($request,$response,$args){
	$data;
	$loan_id=$request->getParam('loan_id');
	$top_up_amount=$request->getParam('top_up_amount');
	$toTop=Loanaccount::where("id",$loan_id)->first();
	$check=$toTop->is_approved;
	if($check==0){
		$data['failed']="Top Up Failure";
		echo json_encode($data); 
	
	}else if($check==1){
		$toTop->top_up_amount=$top_up_amount;
		$toTop->is_top_up=1;
		$toTop->save();
	/*Calculate the total loan amount plus interest amount*/
		$toApprove1=Loanaccount::where("id","=",$loan_id)->first();
		$product=LoanProduct::where("id","=",$toApprove1->loanproduct_id)->first();
		$rate = $toApprove1->interest_rate/100;
		$onerate = 1 + $rate;
		$time = $toApprove1->repayment_duration;
		$formula = $product->formula;
		if($formula == 'SL'){
			$interest_amount = $loan_amount * $rate * $time;
			$total_loan=$top_up_amount + $interest_amount;
		}else if($formula == 'RB'){
	 		if($toApprove1->repayment_duration > 0){
	 			$timer=$toApprove1->repayment_duration;
	 		}else{
	 			$timer=$toApprove1->period;
	 		}
   			$principal_bal = round(($rate*$top_up_amount)/(1-(pow($onerate,-$timer))),2);
        		$interest_amount=($principal_bal*$timer)-($top_up_amount);
			$total_loan= $top_up_amount + $interest_amount;
		}
			/*A Loan Transaction*/
	$transaction=new Loantransaction;
	$transaction->loanaccount_id=$loan_id;
	$transaction->date=date('Y-m-d');
	$transaction->description="loan top up";
	$transaction->amount=$total_loan;
	$transaction->type='debit';
	$transaction->save();
	/*Get posting accounts*/
	$loan=Loanaccount::where("id","=",$loan_id)->first();
	$lid=$loan->loanproduct_id;
	$posting=DB::table("x_loanpostings")->where("loanproduct_id",$lid)->where("transaction","=","disbursal")->get();
	foreach($posting as $posted){
		$credit_account=$posted->credit_account;
		$debit_account=$posted->debit_account;
	}
	/*Specify the accounts*/
	$accounts=array('debit'=>$debit_account,'credit'=>$credit_account);
	$datar=array('credit_account'=>$accounts['credit'],'debit_account'=>$accounts['debit'],'date'=>date('Y-m-d'),'amount'=>$total_loan,'initiated_by'=>'Mobile App',
	'description'=>'loan disbursement');
	/*Obtaining the TRansaction number*/
	$tarehe=date('Y-m-d H:m:s');
	$transact_no=strtotime($tarehe);
	/*Debit Journal Entry*/
	$jrn=new Journal;
	$acc=Account::find($datar['debit_account']);
	$jrn->account_id=$acc->id;
	$jrn->date=$datar['date'];
	$tarehe=date('Y-m-d H:m:s');
	$transact_no=strtotime($tarehe);
	$jrn->trans_no=$transact_no;
	$jrn->initiated_by=$datar['initiated_by'];
	$jrn->amount=$datar['amount'];
	$jrn->type='debit';
	$jrn->description=$datar['description'];
	$jrn->save();
	/*Credit Journal Entry*/
	$jrn1=new Journal;
	$acc1=Account::find($datar['credit_account']);
	$jrn1->account_id=$acc1->id;
	$jrn1->date=$datar['date'];
	$tarehe=date('Y-m-d H:m:s');
	$transact_no=strtotime($tarehe);
	$jrn1->trans_no=$transact_no;
	$jrn1->initiated_by=$datar['initiated_by'];
	$jrn1->amount=$datar['amount'];
	$jrn1->type='credit';
	$jrn1->description=$datar['description'];
	$jrn1->save();
	
		/*Return a Response*/
		$data['topped']="Top Up Success";
		echo json_encode($data);
	
	}	

 }
  /*Offset Loan Application*/



 /*Loan Repaying*/
 public function repayLoan($request,$response,$args){
 	$loan_id=$request->getParam('loan_id');
	$amount_paid=$request->getParam('amount_paid');
	$transact=new Loantransaction;
	$transact->loanaccount_id=$loan_id;
	$transact->date=date('Y-m-d');
	$transact->description="loan repayment";
	$transact->amount=$amount_paid;
	$transact->type="credit";
	$transact->save();
	/*Return a Response*/
	$data['paid']="Payment Success";
	echo json_encode($data);
 }

}
?>
