<?php

$router->post("/employee/add_employee", function() use ($app) {
	require_once DIR . "/controller/employee/add_employee_controller.php";
	$ctrl = new Employee\AddEmployeeController($app);
	$ctrl->exec();
});

$router->get("/employee/search_employee", function() use ($app) {
	require_once DIR . "/controller/employee/search_employee_controller.php";
	$ctrl = new Employee\SearchEmployeeController($app);
	$ctrl->exec();
});

$router->get("/employee/get_employee_details", function() use ($app) {
	require_once DIR . "/controller/employee/get_employee_details_controller.php";
	$ctrl = new Employee\GetEmployeeDetailsController($app);
	$ctrl->exec();
});

$router->post("/employee/delete_employee", function() use ($app) {
	require_once DIR . "/controller/employee/delete_employee_controller.php";
	$ctrl = new Employee\DeleteEmployeeController($app);
	$ctrl->exec();
});