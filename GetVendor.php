<?php  

// load custom libs
require('../../imortuary_config.php');
require($GLOBALS['path_to_functions'] . "/LoadFunctions.php");

require_once("htmlMimeMail5/htmlMimeMail5.php");
 
Main();
 
function Main(){
	
	// connect to the database;
	$GLOBALS['dbObject'] = new dbObject($GLOBALS['dbType'],$GLOBALS['host'],$GLOBALS['user'],$GLOBALS['password'],$GLOBALS['database']);
	$GLOBALS['dbObject']->setShowErrors($GLOBALS['display_db_errors']);
	$GLOBALS['dbObject']->connect();
	
	$vendorData = GetVendorData();
	
	
	
	OutputXML($vendorData);
	
	RecordParams();
	
	PutDataInDatabase($vendorData);
	
	EmailUs($vendorData);
 	
	$GLOBALS['dbObject']->disconnect();
	
}



function GetVendorData(){
	if(trim($_REQUEST["Digits"])){
		$sql = "select * from vendors where id = " . trim($_REQUEST["Digits"]);
		$vendor = $GLOBALS['dbObject']->ReturnSingleRow($sql);
		return $vendor;
	}
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
	MFile("log",$dir . '/Vendor.log',$out,"");
}
 
 
 
 
function PutDataInDatabase($vendorData){

	
	
	$valuesArray = array();

	
	$fields = array("id","name","phone","city","state","zip");
	foreach($fields as $field) {
		$value = $vendorData[$field];
		array_push($valuesArray, $value);
	}
	
	$fields = array("CallStatus","Called","Caller","CallerCity","CallerState","CallerZip","CallerCountry","CallGuid");
	foreach($fields as $field) {
		$value = $_REQUEST[$field];
		array_push($valuesArray, $value);
	}
	

	

	$fields = implode(",",$fieldsArray);
	$questionMarks = implode(",",$questionMarksArray);
	// create the sql
	$sql = "INSERT INTO phone_calls (started,vendor_id,vendor,vendor_phone,vendor_city,vendor_state,vendor_zip,call_status,called,caller,caller_city,caller_state,caller_zip,caller_country,call_guid) VALUES (NOW(),?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

	$prh = $GLOBALS['dbObject']->prepare($sql);
	$sth = $GLOBALS['dbObject']->execute($prh,$valuesArray);
	

}
 
function OutputXML($vendorData){
	
	if($vendorData[id] == "103928"){
		$vendorData[phone] = "206-604-5266";
	}


	header("content-type: text/xml");  

	if(isset($vendorData['name'])){
echo <<<END
<Response>  
	<Say>  
		Please wait while I connect you to: $vendorData[name] in $vendorData[city] $vendorData[state]
	</Say> 
	<Dial action="http://www.imortuary.com/twilio/CallOver.php" timeLimit="1800">
		<Number action="http://www.imortuary.com/twilio/MessageToFuneralHome.php">$vendorData[phone]</Number> 
	</Dial>
</Response>     
END;
	} else {
echo <<<END
<Response>  
	<Gather action="http://www.imortuary.com/twilio/GetVendor.php" method="GET">  
         <Say>  
             We're sorry but we can't find that extension our database.  Please try again.
         </Say>  
     </Gather>  
</Response>     
END;
	}
}


function EmailUs($vendorData){
	
	$text =  <<<EOD
Call to $vendorData[name]
$vendorData[city], $vendorData[state] $vendorData[zip] 
$vendorData[phone]

from:
$_REQUEST[CallerCity],$_REQUEST[CallerState] $_REQUEST[CallerZip]
$_REQUEST[Caller]
EOD;
	
	// Instantiate a new HTML Mime Mail object
    $mail = new htmlMimeMail5();

    // Set the sender address
    $mail->setFrom("iMortuary.com - Phone calls<twilio@iMortuary.com>");
	$mail->setReturnPath('twilio@iMortuary.com');

    // Set the mail subject
    $mail->setSubject("iMortuary.com:  Phone call started - to $vendorData[name] in $vendorData[city],$vendorData[state]");

	#$mail->setText("Hey Matt -- thanks for doing this...");

    // Set the mail body text
    $mail->setText($text);

    // Send the email!
    $mail->send($GLOBALS['email: phone calls']);

}
?>
