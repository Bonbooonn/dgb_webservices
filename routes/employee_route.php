<?php

require_once DOC_ROOT . "/controller/employee/add_employee_controller.php";
$router->post("/employee/add_employee", function() use ($app) {
	$ctrl = new Employee\AddEmployeeController($app);
	$ctrl->exec();
});

require_once DOC_ROOT . "/controller/employee/search_employee_controller.php";
$router->get("/employee/search_employee", function() use ($app) {
	
	$ctrl = new Employee\SearchEmployeeController($app);
	$ctrl->exec();
});