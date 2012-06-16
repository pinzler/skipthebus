<?php

require "Twilio.php";

date_default_timezone_set('America/New_York');

// Set our AccountSid and AuthToken from twilio.com/user/account
$AccountSid = "AC73fd0f9909eb9aabc41af3c279c99a4e";
$AuthToken = "3991617f97278e11876af817f21f0a33";

// Instantiate a new Twilio Rest Client
$client = new Services_Twilio($AccountSid, $AuthToken);

$host=$_ENV['OPENSHIFT_DB_HOST']; // Host name 
$username="admin"; // Mysql username 
$password="a4J83Khlc59W"; // Mysql password 
$db_name="skipthebus"; // Database name 
$tbl_name="trips"; // Table name 
$tbl_members="members"; // Table name 

$body=$_REQUEST['Body'];

// Connect to server and select databse.
mysql_connect("$host", "$username", "$password")or die("cannot connect"); 
mysql_select_db("$db_name")or die("cannot select DB");
$leave_d = date("Y-m-d");

if (date("H") > 17) $leave_t = "Evening";
else if (date("H") > 11) $leave_t = "Afternoon";
else if (date("H") > 5) $leave_t = "Morning";
else $leave_t = "Fringe";

if (strtolower($body) == "now")
	$query = "select * from $tbl_name where leavedate = '$leave_d' and (leavetime ='$leave_t' or leavetime = 'Flexible')";
else if (strtolower($body) == "today")
	$query = "select * from $tbl_name where leavedate = '$leave_d'";
else
	$query = "select * from $tbl_name";
$result = mysql_query($query);
$count = mysql_num_rows($result);

$to=$_REQUEST['From'];

if (strtolower($body) == "now")
	$msg = "There are " . $count. " SkipTheBus.com trips leaving soon!";
else if (strtolower($body) == "today")
	$msg = "There are " . $count. " SkipTheBus.com trips leaving today!";
else
	$msg = "There are " . $count. " SkipTheBus.com trips in the system!";

/* Your Twilio Number or Outgoing Caller ID */
$from = '3477057547';

$client->account->sms_messages->create($from, $to, $msg);

?> 
