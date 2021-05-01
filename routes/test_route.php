<?php

$router->get("/test", function() use ($app) {
	// echo "<pre>";
	// print_r($_SERVER);
	// echo DOC_ROOT;
	echo dirname($_SERVER['PHP_SELF']);
});
