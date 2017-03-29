<?php

use App\Models\TestM;

// Remember to alert if account is not activated

$app->post("/", "LoanController:authenticateUser");

$app->get("/totalloansandsavings", "LoanController:getTotalLoansAndSavings");

$app->get("/loans", "LoanController:getLoans");

$app->post("/loan", "LoanController:getLoan");

$app->get("/appliedloans", "LoanController:getAppliedLoans");


 ?>
