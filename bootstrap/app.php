<?php
session_start();

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use Illuminate\Database\Capsule\Manager as Capsule;

require __DIR__ . '/../vendor/autoload.php';

$app = new \Slim\App([
	"settings" => [
		"displayErrorDetails" => true,
		"db" => [
			"driver" => "mysql",
			"host" => "localhost",
			"database" => "xaradb",
			"username" => "root",
			"password" => "",
			"charset" => "utf8",
			"collation" => "utf8_unicode_ci",
			"prefix" => ""
		],
	]
]);

$container = $app->getContainer();

$capsule = new Capsule;

$capsule->addConnection($container['settings']['db']);

$capsule->setAsGlobal();

$capsule->bootEloquent();

$container['db'] = function($container) {
	return $caspule;
};

$container['LoanController'] = function($container) {
	return new \App\Controllers\MobileController\LoanController($container);
};

require __DIR__ . '/../app/routes.php';

?>
