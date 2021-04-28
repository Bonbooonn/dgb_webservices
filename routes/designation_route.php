<?php

$router->post("/designation/add_designation", function() use ($app) {
	require_once DOC_ROOT . "/controller/designation/add_designation_controller.php";
	$ctrl = new Designation\AddDesignationController($app);
	$ctrl->exec();
});

$router->get('/designation/search_designation', function() use ($app) {
	require_once DOC_ROOT . "/controller/designation/search_designation_controller.php";
	$ctrl = new Designation\SearchDesignationController($app);
	$ctrl->exec();
});

$router->get('/designation/get_designation_details', function() use ($app) {
	require_once DOC_ROOT . "/controller/designation/get_designation_details_controller.php";
	$ctrl = new Designation\GetDesignationDetailsController($app);
	$ctrl->exec();
});

$router->post("/designation/delete_designation", function() use ($app) {
	require_once DOC_ROOT . "/controller/designation/delete_designation_controller.php";
	$ctrl = new Designation\DeleteDesignationController($app);
	$ctrl->exec();
});

?>