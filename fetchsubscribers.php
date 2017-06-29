<?php
//1. Pull dependencies
require_once('AfricasTalkingGateway.php');
require_once('dbCon.php');
require_once('premiumConfig.php');

//2. Create a new instance of our awesome gateway class
$gateway  = new AfricasTalkingGateway($username, $apikey);

//3. Fetch Subs
$sql1 = "SELECT * FROM subscribers ORDER BY lastrcvd_id ASC LIMIT 1";
$subsQuery = $db->query($sql1);
if($result = $subsQuery->fetch_assoc()) {
    $lastReceivedId = $result['lastrcvd_id'];
}	else {
  $lastReceivedId = 0;
}

//4. Any gateway errors will be captured by our custom Exception class below, so wrap the call in a try-catch block

try 
{
//5.  Our gateway will return 100 subscription numbers at a time back to you, starting with
// what you currently believe is the lastReceivedId. Specify 0 for the first
// time you access the gateway, and the ID of the last message we sent you
// on subsequent results

//6. Store to DB
  do {    
    $results = $gateway->fetchPremiumSubscriptions($shortCode, $keyword, $lastReceivedId);
      foreach($results as $result) {
        //insert new record
        $sql2 = "INSERT INTO subscribers (`phone_number`,`lastrecvd_id`) VALUES('".$result->phoneNumber."','".$result->id."')";
        $db->query($sql2);        
      }
  } while ( count($results) > 0 );
  
}
catch ( AfricasTalkingGatewayException $e )
{
  echo "Encountered an error: ".$e->getMessage();
}

?>

