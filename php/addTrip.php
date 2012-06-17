<?php

session_start();

if(!isset($_SESSION['myusername'])){
	header("location:index.php");
}

require "Twilio.php";

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

// Connect to server and select databse.
mysql_connect("$host", "$username", "$password")or die("cannot connect"); 
mysql_select_db("$db_name")or die("cannot select DB");

$email=$_SESSION['myusername'];
$dest=$_REQUEST['dest'];
$url = 'http://maps.google.com/maps/geo?q='.urlencode($dest).'&output=json';
// make the HTTP request
$data = curl($url);
// parse the json response
$jsondata = json_decode($data,true);
// if we get a placemark array and the status was good, 
if(is_array($jsondata )&& $jsondata ['Status']['code']==200)
{
      $lat = $jsondata ['Placemark'][0]['Point']['coordinates'][1];
      $lng = $jsondata ['Placemark'][0]['Point']['coordinates'][0];
}

$leave_d=$_REQUEST['leave_d'];
$leave_t=$_REQUEST['leave_t'];
$home_d=$_REQUEST['home_d'];
$home_t=$_REQUEST['home_t'];
$radius=$_REQUEST['radius'];
$car=$_REQUEST['car'];
$note=$_REQUEST['note'];


	  $half_mile_lat = 1/69.172; 
    $half_mile_lng = 1/(cos($lat)*69.172);
    if ($half_mile_lng < 0) $half_mile_lng = $half_mile_lng * -1.0;
    
    $latrange = $radius * $half_mile_lat;
    $lngrange = $radius * $half_mile_lng;

    $lat1 = $lat - $latrange;
    $lat2 = $lat + $latrange;
 
    $lng1 = $lng - $lngrange;  
    $lng2 = $lng + $lngrange;    

// check for other leaves that match
    if ($leave_t == "Flexible") 
      $query = "select * from $tbl_name where email<>'$email' and leavedate = '$leave_d' AND lat BETWEEN '$lat1' AND '$lat2' AND lng BETWEEN '$lng1' AND '$lng2'";
    else
      $query = "select * from $tbl_name where email<>'$email' and leavedate = '$leave_d' and (leavetime ='$leave_t' OR leavetime = 'Flexible') AND lat BETWEEN '$lat1' AND '$lat2' AND lng BETWEEN '$lng1' AND '$lng2'";
	  $result=mysql_query($query);
    
	
    while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
      if (distance($lat, $lng, $row['lat'], $row['lng']) < $row['radius'])
      	{ 
      		$tempEM = $row['email'];
      		$query = "select phone from $tbl_members where email = '$tempEM'";
    			$res=mysql_query($query);
    			$rw = mysql_fetch_array($res, MYSQL_ASSOC);
          echo $rw['phone'];
          sendSMS($rw['phone'], false, $client);
      	}
    
      }




// check for other homes that match
	if ($home_t == "Flexible") 
    $query = "select * from $tbl_name where email<>'$email' and homedate = '$home_d' AND lat BETWEEN '$lat1' AND '$lat2' AND lng BETWEEN '$lng1' AND '$lng2'";
	else
    $query = "select * from $tbl_name where email<>'$email' and homedate = '$home_d' and (hometime ='$home_t' OR hometime = 'Flexible') AND lat BETWEEN '$lat1' AND '$lat2' AND lng BETWEEN '$lng1' AND '$lng2'";
  
  $result2=mysql_query($query);
    
	
    while($row2 = mysql_fetch_array($result2, MYSQL_ASSOC)) {
      if (distance($lat, $lng, $row2['lat'], $row2['lng']) < $row2['radius'])
      	{ 
      		$tempEM = $row2['email'];
      		$query = "select phone from $tbl_members where email = '$tempEM'";
			    $res2=mysql_query($query);
			    $rw2 = mysql_fetch_array($res2, MYSQL_ASSOC);
          sendSMS($rw2['phone'], true, $client);	
      	}
      }
// Mysql_num_row is counting table row



// check for other homes that match

$sql2="INSERT INTO $tbl_name (email, destination, lat, lng, leavedate, leavetime, homedate, hometime, radius, car, note) VALUES ('$email', '$dest', '$lat', '$lng', '$leave_d', '$leave_t', '$home_d', '$home_t', '$radius', '$car', '$note');";
$result2=mysql_query($sql2);


header("location:mySkips.php");


function sendSMS($number, $isHome, $client) {
	if (!$isHome)
		$msg = "You have a new SkipTheBus.com match on your trip to the Hamptons!";
	else
		$msg = "You have a new SkipTheBus.com match on your trip back from the Hamptons!";

	/* Your Twilio Number or Outgoing Caller ID */
	$from = '3477057547';
 	$client->account->sms_messages->create($from, $number, $msg);
 
}

function distance($lt1, $ln1, $lt2, $ln2) {
	$theta = $ln1 - $ln2;
	$dis = sin(deg2rad($lt1)) * sin(deg2rad($lt2)) +  cos(deg2rad($lt1)) * cos(deg2rad($lt2)) * cos(deg2rad($theta));
	$dis = acos($dis);
	$dis = rad2deg($dis);
	$miles = $dis * 60 * 1.1515;
	return $miles;
} 

function curl($url) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
            $data = curl_exec($ch);
            curl_close($ch);
            return $data;
        }


?>

