<?php
$routes = glob(__DIR__ . "/*_route.php");
$router = $app['router'];
if ( !empty($routes) ) {
	
	foreach ($routes as $route) {
		require_once($route);
	} 
}

?>