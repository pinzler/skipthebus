<?php include "header.php"; ?>

<?php
session_start();
if(!isset($_SESSION['myusername'])){
	header("location:index.php");
}


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

echo "<h3>Welcome ".$email."!</h3><BR>";

$query = "select * from $tbl_name where email = '$email'";
$resultbig=mysql_query($query);
while($rowbig = mysql_fetch_array($resultbig, MYSQL_ASSOC)) {
    $dest= $rowbig['destination'];
    $lat = $rowbig['lat'];
    $lng = $rowbig['lng'];
	$leave_d=$rowbig['leavedate'];
	$leave_t=$rowbig['leavetime'];
	$home_d=$rowbig['homedate'];
	$home_t=$rowbig['hometime'];
	$radius=$rowbig['radius'];
	$car=$rowbig['car'];
	$note=$rowbig['note'];

	$half_mile_lat = 1/69.172; 
    $half_mile_lng = 1/(cos($lat)*69.172);
    if ($half_mile_lng < 0) $half_mile_lng = $half_mile_lng * -1.0;
    
    $latrange = $radius * $half_mile_lat;
    $lngrange = $radius * $half_mile_lng;

    $lat1 = $lat - $latrange;
    $lat2 = $lat + $latrange;
 
    $lng1 = $lng - $lngrange;  
    $lng2 = $lng + $lngrange;    

   echo "Trip to: ". $rowbig['destination'] . "<BR>";
   echo $rowbig['leavedate'] . " " . $rowbig['leavetime'] . "<BR>";
   echo "Leave matches:<BR>";

// check for other leaves that match
    if ($leave_t == "Flexible") 
      $query = "select * from $tbl_name where email <> '$email' and leavedate = '$leave_d' AND lat BETWEEN '$lat1' AND '$lat2' AND lng BETWEEN '$lng1' AND '$lng2'";
	  else
      $query = "select * from $tbl_name where email <> '$email' and leavedate = '$leave_d' and leavetime ='$leave_t' AND lat BETWEEN '$lat1' AND '$lat2' AND lng BETWEEN '$lng1' AND '$lng2'";
    
    $result=mysql_query($query);
    
	
    while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
      if (distance($lat, $lng, $row['lat'], $row['lng']) < $row['radius'])
      	{ 
      		echo "<a href='contact.php?type=leave&id=" . $row['id'] . "'>Request contact</a> : " . $row['destination'] ." ". $row['note'];
      		if ($row['car']) 
				echo " (Has a car)";
      		echo "<BR>";
      			
      	}
    
      }


echo $rowbig['homedate'] . " " . $rowbig['hometime'] . "<BR>";
   
echo " Home matches:<BR>";
// check for other homes that match
	if ($leave_t == "Flexible") 
    $query = "select * from $tbl_name where email<>'$email' and homedate = '$home_d' AND lat BETWEEN '$lat1' AND '$lat2' AND lng BETWEEN '$lng1' AND '$lng2'";
  else
    $query = "select * from $tbl_name where email<>'$email' and homedate = '$home_d' and hometime ='$home_t' OR hometime = 'Flexible' AND lat BETWEEN '$lat1' AND '$lat2' AND lng BETWEEN '$lng1' AND '$lng2'";
	$result=mysql_query($query);
    
	
    while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
      if (distance($lat, $lng, $row['lat'], $row['lng']) < $row['radius'])
      	{ 
      		echo "<a href='contact.php?type=home&id=" . $row['id'] . "'>Request contact</a> : " . $row['destination'] ." ". $row['note'];  
			if ($row['car']) 
				echo " (Has a car)";
      		echo "<BR>";
      	}
      }

}

function distance($lt1, $ln1, $lt2, $ln2) {
	$theta = $ln1 - $ln2;
	$dis = sin(deg2rad($lt1)) * sin(deg2rad($lt2)) +  cos(deg2rad($lt1)) * cos(deg2rad($lt2)) * cos(deg2rad($theta));
	$dis = acos($dis);
	$dis = rad2deg($dis);
	$miles = $dis * 60 * 1.1515;
	return $miles;
} 


?>
<BR>
Add a trip:
<form name="form" method="post" action="addTrip.php">
Address in the Hamptons: <input name="dest" type="text" id="dest"> <BR>
Departure Date: <input name="leave_d" type="text" id="leave_d"> <BR> 
Departure Time: <select name="leave_t" id="leave_t"> 
<option value="Morning">Morning</option>
<option value="Afternoon">Afternoon</option>
<option value="Evening">Evening</option>
<option value="Fringe">Fringe</option>
<option value="Flexible">Flexible</option>
</select><BR>
Return Date: <input name="home_d" type="text" id="home_d"><BR>
Return Time: <select name="home_t" id="home_t">
<option value="Morning">Morning</option>
<option value="Afternoon">Afternoon</option>
<option value="Evening">Evening</option>
<option value="Fringe">Late Night</option>
<option value="Flexible">Flexible</option>
</select><BR>
Distance from address you are willing to travel (miles): <input name="radius" type="text" id="radius"><BR>
Do you own a car or are you planning on renting one? <select name="car" id="car">
<option value="No">No</option>
<option value="Yes">Yes</option>
</select><BR>
Notes: <input name="note" type="text" id="note"> <BR> 
<input type="submit" name="Submit" value="Add trip!">

</form>


<?php include "footer.php"; ?>
