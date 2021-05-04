<?php
$models = glob(__DIR__ . "/*.php");

if ( !empty($models) ) {
	
	foreach ($models as $model) {

		if ( $model != "base_model.php" ) {
			require_once($model);
		}

		
	} 
}

?>