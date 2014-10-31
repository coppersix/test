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
 	

	
	
	
	
	RecordParams();
 	OutputXML();
	
	PutDataInDatabase();
	
	$vendorData = GetVendorData();
	
	EmailUs($vendorData);
	
	$GLOBALS['dbObject']->disconnect();
	
}

function GetVendorData(){
	if(trim($_REQUEST["CallGuid"])){
		$sql = "select * from phone_calls where call_guid = '" . trim($_REQUEST["CallGuid"]) . "'";
		$vendor = $GLOBALS['dbObject']->ReturnSingleRow($sql);
		return $vendor;
	}
}

function PutDataInDatabase(){

	
	$valuesArray = array();
	
	$fields = array("CallStatus","DialStatus","CallGuid");
	foreach($fields as $field) {
		$value = $_REQUEST[$field];
		array_push($valuesArray, $value);
	}
	

	// create the sql
	$sql = "update phone_calls set duration =  timediff(now(),started), ended = NOW(), call_status = ?,dial_status = ? where call_guid = ?";

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
	MFile("log",$dir . '/CallOver.log',$out,"");
}
 
function OutputXML(){
header("content-type: text/xml");  
echo <<<END
<Response>
	<Say>Thank you for using eye Mortuary.com!</Say> 
</Response>   
END;
}

function EmailUs($vendorData){
	
	$text =  <<<EOD
Call to $vendorData[vendor]
$vendorData[vendor_city], $vendorData[vendor_state] $vendorData[vendor_zip] 
$vendorData[vendor_phone]

from:
$_REQUEST[CallerCity],$_REQUEST[CallerState] $_REQUEST[CallerZip]
$_REQUEST[Caller]

lasted $vendorData[duration] seconds

EOD;
	
	// Instantiate a new HTML Mime Mail object
    $mail = new htmlMimeMail5();

    // Set the sender address
    $mail->setFrom("iMortuary.com - Phone calls<twilio@iMortuary.com>");
	$mail->setReturnPath('twilio@iMortuary.com');

    // Set the mail subject
    $mail->setSubject("iMortuary.com:  Phone call ended - to $vendorData[vendor] in $vendorData[vendor_city],$vendorData[vendor_state]");

	#$mail->setText("Hey Matt -- thanks for doing this...");

    // Set the mail body text
    $mail->setText($text);

    // Send the email!
    $mail->send($GLOBALS['email: phone calls']);

}

?>
