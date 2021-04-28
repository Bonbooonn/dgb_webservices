<?php

$router->post("/employee/add_employee", function() use ($app) {
	require_once DOC_ROOT . "/controller/employee/add_employee_controller.php";
	$ctrl = new Employee\AddEmployeeController($app);
	$ctrl->exec();
});

$router->get("/employee/search_employee", function() use ($app) {
	require_once DOC_ROOT . "/controller/employee/search_employee_controller.php";
	$ctrl = new Employee\SearchEmployeeController($app);
	$ctrl->exec();
});