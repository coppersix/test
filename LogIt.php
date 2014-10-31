<?php  

// load custom libs
require('../../imortuary_config.php');
require($GLOBALS['path_to_functions'] . "/LoadFunctions.php");
 
Main();
 
function Main(){
	RecordParams();
	echo "Logged";
}
 
 


function RecordParams(){
 	$dir = '/home/imortuar/public_html/twilio/logs';
 	ksort($_REQUEST);
	$date = date("Y-m-d H:i:s");
	$out = "date::$date\t";
	foreach ($_REQUEST as $key=>$value) {
		if (! preg_match("/__utm/i", $key)) {
			#print "$key: $value<br>";
			$out .= "$key::$value\t";
		}
	}
	MFile("log",$dir . '/logit.html',$out,"");
}
 

?>