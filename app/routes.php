<?php

use App\Models\TestM;

// Remember to alert if account is not activated

$app->post("/", "LoanController:authenticateUser");

$app->get("/totalloansandsavings", "LoanController:getTotalLoansAndSavings");

$app->get("/loans", "LoanController:getLoans");

$app->post("/loan", "LoanController:getLoan");

$app->get("/appliedloans", "LoanController:getAppliedLoans");

$app->post("/guarantors", "LoanController:getLoanGuarantors");

$app->post("/accounts", "LoanController:getAccounts");

$app->get("/applicationdetails", "LoanController:getApplicationDetails");

//$app->post('/users/login', 'LoanController:loginUser');

 $app->post('/users/login', function($request, $response, $args){ $username = $request->getParam('username'); $password = $request->getParam('password'); 

   $user = App\Models\User::where('username',$username)->orWhere('email',$username)->first();
   if(password_verify ( $password , $user->password ) == true){
   echo json_encode($user);
   }
   //return  $user->password ;
  });
 ?>
