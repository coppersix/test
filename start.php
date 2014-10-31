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
	
	OutputXML();
	
	RecordParams();
 	
	$GLOBALS['dbObject']->disconnect();
	
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
	MFile("log",$dir . '/start.log',$out,"");
}
 
function OutputXML(){
header("content-type: text/xml");  
echo <<<END
<Response>  
	<Gather action="http://www.imortuary.com/twilio/GetVendor.php" method="GET">  
         <Play>
		 	http://api.twilio.com/2008-08-01/Accounts/AC2c1a4803fb8bda5dcaf14135af1ad394/Recordings/REf4a9e814804469f97c8846b3d04ee877
		</Play>  
     </Gather>  
</Response>    
END;
}


function OutputXML_OLD(){
header("content-type: text/xml");  
echo <<<END
<Response>  
	<Gather action="http://www.imortuary.com/twilio/GetVendor.php" method="GET">  
         <Say>  
             Thank you for using eye Mortuary dot com.  Please enter the extension of the funeral home you are interested in and then hit the pound button.
         </Say>  
     </Gather>  
</Response>    
END;
}
?>