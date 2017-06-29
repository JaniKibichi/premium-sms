<?php
//pull dependencies
require_once('AfricasTalkingGateway.php');
require_once('dbCon.php');
require_once('premiumConfig.php');

	//1. Select All from Quotes
	$sql = "SELECT * FROM quotes LIMIT 1";

	$quoteQuery = $db->query($sql);

	if($result = $quoteQuery->fetch_assoc()) {
  		$msg = $result['message'];
        $id = $result['id'];
	}	
	
    //2. Instantiate gateway
    $gateway   = new AfricasTalkingGateway($username, $apikey);

    //3. Grab pending users
    $today = date("Y-m-d");

    $userSql = "SELECT * FROM sending WHERE id='".$today."' AND status= 'pending' LIMIT 200";

    $usersQuery = $db->query($userSql);
    $counter = 0;
    if($usersQuery) {
        while($results = $usersQuery->fetch_assoc()){
    //4. Send SMS
            try{
                $ATResults =$gateway->sendMessage($results['phone_number'], $msg, $shortCode, $bulkSMSMode, $options);
                    foreach($ATResults as $ATResult) {
                        //if buffered, change status from pending to sent in sending table
                        $status= $result->status;
                        if($status=="Sent"){
                            //update counter
                            $counter++;

                            //update user
                            $atrSql ="SELECT * FROM sending WHERE phone_number LIKE '%".$results['phone_number']."%' AND status = 'pending' LIMIT 1";
                            $atrQuery=$db->query($atrSql);
                            if($atrAvailable=$atrQuery->fetch_assoc()){
                                //delete
                                $atrDelSql="DELETE FROM sending WHERE phone_number='".$atrAvailable['phone_number']."' ";
                                $atrDelQuery = $db->query($atrDelSql); 
                            }

                        }
                    }
            } catch ( AfricasTalkingGatewayException $e ){
                echo "Encountered an error while sending: ".$e->getMessage();
            }

        }

        //update countsent
        $countSql = "SELECT * FROM countsent WHERE insert_time='".$today."'";
        $countQuery=$db->query($countSql);
        if($countAvailable=$countQuery->fetch_assoc()){
            //update for subsequent records
            $tempCount = $countAvailable['numbers_sent_to'];
            $tempCount+=$counter;
            
            //update sql
            $updSql = "UPDATE `countsent` SET `numbers_sent_to`=$tempCount WHERE `insert_time`='".$today."'";
            $updQuery = $db->query($updSql);

        } else{
            //insert new record
            $sql1A = "INSERT INTO countsent (`numbers_sent_to`,`insert_time`) VALUES($counter,'".$today."')";
            $db->query($sql1A);             

        }

    }

		
?>
	
