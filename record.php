<?php  

// load custom libs
require('../../imortuary_config.php');
require($GLOBALS['path_to_functions'] . "/LoadFunctions.php");
 
Main();
 
function Main(){
	

	OutputXML();
	
	
}
 
 


 
function OutputXML(){
header("content-type: text/xml");  
echo <<<END
<Response>  
    <Say>  
        Please leave a message at the beep.   
        Press the star key when finished.   
    </Say>  
    <Record   
        action="http://www.imortuary.com/twilio/LogIt.php"  
        method="GET"   
        maxLength="100"  
        finishOnKey="*"  
        />  
    <Say>I did not receive a recording</Say>  
</Response>    
END;
}
?>



   
