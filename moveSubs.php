<?php
//pull dependencies
require_once('AfricasTalkingGateway.php');
require_once('dbCon.php');
require_once('premiumConfig.php');

//1. At Midnight delete all from Sending table
    $delSql = "DELETE * FROM sending";
    $delQuery = $db->query($delSql);

//2. Populate sending table
    $popSql = "INSERT INTO sending ( `phone_number`, `lastrcvd_id`) SELECT phone_number,lastrecvd_id FROM subscribers";
    $popQuery = $db->query($popSql);

    if($popQuery) {
        //select *
        $today = date("Y-m-d");
        $updpopSql = "UPDATE sending SET status='pending', id='".$today."'";
        $updpopQuery = $db->query($updpopSql);

    }	

?>