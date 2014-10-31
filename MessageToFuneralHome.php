<?php  

// load custom libs
require('../../imortuary_config.php');
require($GLOBALS['path_to_functions'] . "/LoadFunctions.php");
 
Main();
 
function Main(){
	
	// connect to the database;
	$GLOBALS['dbObject'] = new dbObject($GLOBALS['dbType'],$GLOBALS['host'],$GLOBALS['user'],$GLOBALS['password'],$GLOBALS['database']);
	$GLOBALS['dbObject']->setShowErrors($GLOBALS['display_db_errors']);
	$GLOBALS['dbObject']->connect();
 
	
	
	
	RecordParams();
 	OutputXML();
	
	PutDataInDatabase();
	
}



 
function PutDataInDatabase(){

	
	$valuesArray = array();
	
	$fields = array("CallStatus","DialStatus","Called","CallGuid");
	foreach($fields as $field) {
		$value = $_REQUEST[$field];
		array_push($valuesArray, $value);
	}
	

	// create the sql
	$sql = "update phone_calls call_status = ?,dial_status = ?,called = ? where call_guid = ?";

	$prh = $GLOBALS['dbObject']->prepare($sql);
	$sth = $GLOBALS['dbObject']->execute($prh,$valuesArray);

}

function RecordParams(){
 	$dir = '/home/imortuar/public_html/twilio/logs';
 	ksort($_REQUEST);
	$out = "";
	foreach ($_REQUEST as $key=>$value) {
		if (! preg_match("/__utm/i", $key)) {
			#print "$key: $value<br>";
			$out .= "$key::$value\t";
		}
	}
	MFile("log",$dir . '/FuneralHome.log',$out,"");
}
 
function OutputXML(){
header("content-type: text/xml");  
echo <<<END
<Response>
	<Play>http://api.twilio.com/2008-08-01/Accounts/AC2c1a4803fb8bda5dcaf14135af1ad394/Recordings/RE08f775ec0bb9e3efd7f26e6f90db84aa</Play>
</Response>     
END;
}



function OutputXML_OLD(){
header("content-type: text/xml");  
echo <<<END
<Response>
	<Say>You have an incoming call from individual who found you by using eye Mortuary dot com, the internet's leading funeral home directory.</Say>
</Response>   
END;
}
?>
