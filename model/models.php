<?php
$models = glob(__DIR__ . "/*.php");

if ( !empty($models) ) {
	
	foreach ($models as $model) {
		require_once($model);
	} 
}

?>