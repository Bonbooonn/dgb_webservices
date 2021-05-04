<?php

$router->post("/holiday/add_holiday", function() use ($app) {
	require_once DIR . "/controller/holiday/add_holiday_controller.php";
	$ctrl = new Holiday\AddHolidayController($app);
	$ctrl->exec();
});

$router->post("/holiday/search_holiday", function() use ($app) {
	require_once DIR . "/controller/holiday/search_holiday_controller.php";
	$ctrl = new Holiday\SearchHolidayController($app);
	$ctrl->exec();
});

