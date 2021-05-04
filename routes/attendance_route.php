<?php

$router->post("/attendance/check_attendance", function() use ($app) {
	require_once DIR . "/controller/attendance/check_attendance_controller.php";
	$ctrl = new Attendance\CheckAttendanceController($app);
	$ctrl->exec();
});

$router->post("/attendance/get_attendance", function() use ($app) {
	require_once DIR . "/controller/attendance/get_attendance_controller.php";
	$ctrl = new Attendance\GetAttendanceController($app);
	$ctrl->exec();
});

