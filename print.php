<?php

define('GET_CACHES', 1);
define('ROOT_PATH', '../');
require(ROOT_PATH.'ephod.php');
include '../tet.php';
include '../licence.php';
// include '../header.php';


error_reporting(E_ALL);
ini_set('display_errors','1');

$tid = $_POST['tid'];
$email = $_POST["emailaddress"];
$org = $_POST["org"];
$startdate = $_POST["startdate"];
$enddate = $_POST["enddate"];
$shortdescription = $_POST["shortdescription"];
$location = $_POST["location"];
$members = $_POST["members"];
$programme = $_POST["programme"];
$brandname = $_POST["brandname"];
$templatename = $_POST["templatename"];
$longdescription = $_POST["longdescription"];
$url = $_POST["url"];
$budget = $_POST["budget"];

$to = "aandv@exeterguild.com";
$subject = "$org Flagged Event";
$headers = "From: $email\n";
$message = "
Org Name: $org \n 
Start Date: $startdate \n 
End Date: $enddate \n 
Short Description: $shortdescription \n 
Location: $location \n 
Long Description: $longdescription \n
Risk assessment: $url \n
Budget: $budget \n


Email Address: $email";
$user = "$email";
$usersubject = "Your Event";
$userheaders = "From: aandv@exeterguild.com\n";
$usermessage = "Thank you for submitting your event. It has been flagged by Activities and Volunteering for further action. We will be in touch shortly. Thank you.";
mail($to,$subject,$message,$headers);
mail($user,$usersubject,$usermessage,$userheaders);



 $tagtitle = "No"; 

 $aaron = "UPDATE mslimport set Programme=? WHERE id=$tid";
 
  $levites = $dbh->prepare($aaron);

 $levites->execute(array($tagtitle));

//	echo "done $tid";
 header("Location: message.php?title=Case Created&body=Your case has been sent to the help desk.");

?>