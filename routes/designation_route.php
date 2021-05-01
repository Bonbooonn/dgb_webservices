<?php

$router->post("/designation/add_designation", function() use ($app) {
	require_once DIR . "/controller/designation/add_designation_controller.php";
	$ctrl = new Designation\AddDesignationController($app);
	$ctrl->exec();
});

$router->get('/designation/search_designation', function() use ($app) {
	require_once DIR . "/controller/designation/search_designation_controller.php";
	$ctrl = new Designation\SearchDesignationController($app);
	$ctrl->exec();
});

$router->get('/designation/get_designation_details', function() use ($app) {
	require_once DIR . "/controller/designation/get_designation_details_controller.php";
	$ctrl = new Designation\GetDesignationDetailsController($app);
	$ctrl->exec();
});

$router->post("/designation/delete_designation", function() use ($app) {
	require_once DIR . "/controller/designation/delete_designation_controller.php";
	$ctrl = new Designation\DeleteDesignationController($app);
	$ctrl->exec();
});

$router->get('/designation/select2_designations', function() use ($app) {
	require_once DIR . "/controller/designation/select2_designations_controller.php";
	$ctrl = new Designation\Select2DesignationsController($app);
	$ctrl->exec();
});

?>