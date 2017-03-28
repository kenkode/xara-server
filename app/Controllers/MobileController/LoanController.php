<?php

  namespace App\Controllers\MobileController;

  use \App\Controllers\Controller;
  use \App\Models\Loanaccount;
  use \App\Models\LoanRepayment;
  use \App\Models\Member;
  use \App\Models\LoanTransaction;
  use \App\Models\User;
  use \App\Models\SavingTransaction;
  use \App\Models\ShareTransaction;

  /**
   *  Controller for mobile xara application
   */
  class LoanController extends Controller
  {

    function __construct($controller)
    {
        $this->controller = $controller;
    }

    public function authenticateUser($request, $response) {
      $data;
      $username = $request->getParam("username");
      $password = $request->getParam("password");

      if(trim($username) == "Oirere" && trim($password) == "460888") {
        $data['status'] = true;
        $data['user'] = array(
          "user_id"=>"1",
          "user_name"=>"Oirere",
          "user_email"=>"eddiebranth@gmail.com",
          "user_type"=>"admin",
          "user_phone"=>"0700460888"
        );
      }else {
        $data['status'] = false;
        $data['exist'] = 0;
      }

      echo json_encode($data);
    }

  public function getTotalLoansAndSavings($request, $response)
  {
      $data;
      // $id = $request->getParam("xm_user_key");
      $id = 66;

      $transaction = Loanaccount::findOrFail($id);
  // echo "in loan";die;
      $saving   = SavingTransaction::join('x_savingaccounts','x_savingtransactions.savingaccount_id','=','x_savingaccounts.id')
                          ->where('member_id',$transaction->member_id)
                          ->where('x_savingtransactions.type','credit')
                          ->sum('amount');

      $shares   = ShareTransaction::join('x_shareaccounts','x_sharetransactions.shareaccount_id','=','x_shareaccounts.id')
                          ->where('member_id',$transaction->member_id)
                          ->where('x_sharetransactions.type','credit')
                          ->sum('amount');

      $remittance   = Member::where('x_members.id',$transaction->member_id)
                          ->sum('monthly_remittance_amount');

      $amount_to_date   = $saving + $remittance+$shares;

      $principal_paid   = LoanRepayment::join('x_loanaccounts','x_loanrepayments.loanaccount_id','=','x_loanaccounts.id')
                          ->where('member_id',$transaction->member_id)
                          ->sum('principal_paid');

      $amount_disbursed   = Loanaccount::where('member_id',$transaction->member_id)
                          ->sum('amount_disbursed');

      $top_up = Loanaccount::where('member_id',$transaction->member_id)
                          ->sum('top_up_amount');

      $balance_to_date = $amount_disbursed + $top_up - $principal_paid;

      // Transactions
      $transactions = Loantransaction::join('x_loanaccounts','x_loantransactions.loanaccount_id','=','x_loanaccounts.id')
            ->where('member_id',$transaction->member_id)
            ->select('x_loantransactions.created_at', 'amount', 'type', 'description', 'amount', 'trans_no')
            ->latest()->take(10)->get();

      $data['savings'] = $amount_to_date;
      $data['loans'] = $balance_to_date;
      $data['transactions'] = $transactions;

      echo json_encode($data);

  }

  public function getLoans($request, $response) {
    $loanaccounts;
    // $user_id = $request->getParam("user_id");
    $user_id = 1;
    $user = User::where("id", $user_id)->first();
    if(count($user) > 0) {
      if($user->user_type=='credit'){
        $loanaccounts=Loanaccount::where("x_loanaccounts.member_id", $user_id)
        ->join("x_loanproducts", "loanproduct_id", "x_loanproducts.id")
        ->where('is_rejected',0)
        ->where('secretary_approved',0)
        ->select("x_loanaccounts.id", "is_approved", "x_loanproducts.interest_rate", "name",
         "amount_disbursed", "repayment_duration", "top_up_amount", "currency", "application_date", "repayment_start_date")
        ->get();
      }else if($user->user_type=='admin'){
        $loanaccounts = Loanaccount::where("x_loanaccounts.member_id", $user_id)
        ->join("x_loanproducts", "x_loanproducts.id", "loanproduct_id")
        ->select("x_loanaccounts.id", "is_approved", "x_loanproducts.interest_rate", "name", "amount_disbursed",
        "repayment_duration", "top_up_amount", "currency", "application_date", "repayment_start_date")
        ->get();
      }
    }else {
      $loanaccounts = "No Loans";
    }

    echo json_encode($loanaccounts);
  }

  public function getLoan($request, $response, $args) {
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


}



?>
