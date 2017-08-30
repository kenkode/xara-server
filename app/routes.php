<?php

use App\Models\TestM;

// Remember to alert if account is not activated

$app->post("/", "LoanController:authenticateUser");

//$app->get("/totalloansandsavings", "LoanController:getTotalLoansAndSavings");

$app->post("/totalloansandsavings", "LoanController:getTotalLoansAndSavings");

$app->post("/loans", "LoanController:getLoans");

$app->post("/loan", "LoanController:getLoan");

$app->post("/appliedloans", "LoanController:getAppliedLoans");

$app->post("/guarantors", "LoanController:getLoanGuarantors");

$app->post("/accounts", "LoanController:getAccounts");

$app->get("/applicationdetails", "LoanController:getApplicationDetails");

$app->post('/users/login', 'LoanController:loginUser');

$app->post('/approveDisburse','LoanController:approveDisburse');

$app->post('/topUp','LoanController:topUp');

$app->post('/rejectLoan','LoanController:rejectLoan');

$app->post('/repayLoan','LoanController:repayLoan');

$app->post("/savings", "LoanController:getSavings");

$app->get("/applicationsaving", "LoanController:getSavingApplicationDetails");

$app->post("/savingdetails", "LoanController:getSavingDetails");

$app->post("/savingstore", "LoanController:getSavingStore");
 
 ?>
