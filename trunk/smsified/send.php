<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>AutoBack</title>
<?php

// Include the SMSifed class.
require 'smsified.class.php';

// SMSified Account settings.
$username = "cl17726";
$password = "**********";
$senderAddress = "17177455056";
$js=$_POST["js"];
$nr=$_POST["nr"];
try {	
	
	// Create a new instance of the SMSified object.
	$sms = new SMSified($username, $password);
	
	// Send an SMS message and decode the JSON response from SMSified.
	$response = $sms->sendMessage($senderAddress, $js, $nr);
	$responseJson = json_decode($response);
  echo '<a href="index.php">Success Submit!</a>';	
}

catch (SMSifiedException $ex) {
	echo "<a href="index.php">Failed!</a>";
}



?>
</head>
</html>