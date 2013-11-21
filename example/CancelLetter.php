<?php

// This file holds API keys for the demos
require_once('Constants.php'); 				

// Include the MailLift API 
require_once('../lib/MailLift.php'); 		


// Initialize the MailLift API with username and API key
MailLift::init(MAILLIFT_USERNAME, MAILLIFT_APIKEY); 

$letter = new MailLiftLetter; 

// Fill in the minimum required fields to send a letter. 
$letter->RecipientName1         = 'Daniel Jurek';
$letter->RecipientAddress1      = '123 Fake St';
$letter->RecipientCity          = 'San Marcos';
$letter->RecipientStateCode     = 'TX';
$letter->RecipientPostCode      = '78666';
$letter->MessageBody			= 'Test new letter message :)'; 


// Send the letter via MailLift
$letter->Send(); 

// Cancel the letter
$letter->Cancel(); 