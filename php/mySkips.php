<?php include "header.php"; ?>

<?php
session_start();
if(!isset($_SESSION['myusername'])){
	header("location:index.php");
}

date_default_timezone_set('America/New_York');

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

echo "<h3>Welcome ".$email."!</h3><ul>";

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

   echo "<li class='result'><div class='trip'>Trip to: <span class='destination'>". $rowbig['destination'] . "</span><BR>";
   echo "<span class='date'>" . date('F j, Y', strtotime($rowbig['leavedate'])) . " " . $rowbig['leavetime'] . "</span></div>";
   
// check for other leaves that match
    if ($leave_t == "Flexible") 
      $query = "select * from $tbl_name where email <> '$email' and leavedate = '$leave_d' AND lat BETWEEN '$lat1' AND '$lat2' AND lng BETWEEN '$lng1' AND '$lng2'";
	  else
      $query = "select * from $tbl_name where email <> '$email' and leavedate = '$leave_d' and (leavetime ='$leave_t' OR leavetime = 'Flexible') AND lat BETWEEN '$lat1' AND '$lat2' AND lng BETWEEN '$lng1' AND '$lng2'";
    
    $result=mysql_query($query);
    $count=mysql_num_rows($result);

    if ($count == 0)
        echo "<h1>No matching trips to the Hamptons</h1>";
 else {
    echo "<h1>Matching trips to the Hamptons:</h1>";
 
	
    while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
      if (distance($lat, $lng, $row['lat'], $row['lng']) < $row['radius'])
      	{ 
      		$mess = '"A message has been sent to this user."';
          echo "<div class='req'><a onclick='alert(".$mess.")' href='contact.php?type=leave&id=" . $row['id'] . "'>Request contact</a> : " . $row['destination'] ."<div class='notes'>Notes: ". $row['note'] . "</div>";
      		if ($row['car']) 
				echo " (Has a car)";
      		echo "</div>";
      			
      	}
    
      }
    }

echo "<div class='trip'><span class='date'>" . date('F j, Y', strtotime($rowbig['homedate'])) . " " . $rowbig['hometime'] . "</span></div>";
   
// check for other homes that match
	if ($home_t == "Flexible") 
    $query = "select * from $tbl_name where email<>'$email' and homedate = '$home_d' AND lat BETWEEN '$lat1' AND '$lat2' AND lng BETWEEN '$lng1' AND '$lng2'";
  else
    $query = "select * from $tbl_name where email<>'$email' and homedate = '$home_d' and (hometime ='$home_t' OR hometime = 'Flexible') AND lat BETWEEN '$lat1' AND '$lat2' AND lng BETWEEN '$lng1' AND '$lng2'";
	$result=mysql_query($query);
   
$count=mysql_num_rows($result);

    if ($count == 0)
        echo " <h1>No matching trips from the Hamptons</h1>";
      
else {
	echo " <h1>Matching trips from the Hamptons:</h1>";

    while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
      if (distance($lat, $lng, $row['lat'], $row['lng']) < $row['radius'])
      	{ 
      		$mess = '"A message has been sent to this user."';
          echo "<div class='req'><a onclick='alert(".$mess.")' href='contact.php?type=home&id=" . $row['id'] . "'>Request contact</a> : " . $row['destination'] ."<div class='notes'>Notes: ". $row['note'] ."</div>";  
			if ($row['car']) 
				echo " (Has a car)";
      		echo "</div>";
      	}
      }
    }
      echo "</li>";
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
</ul>
<div id="add-trip">
<h3>Add a trip:</h3>
<form name="form" method="post" action="addTrip.php">
  <table>
    <tr> 
      <td class="label">Full address in the Hamptons<br/><span class="sample">(111 Main Street, East Hampton, NY)</span></td> 
      <td><span class="divider">:</span></td>
      <td><input name="dest" type="text" id="dest"></td>
    </tr>

    <tr> 
      <td class="label">Departure Date</td> 
      <td><span class="divider">:</span></td>
      <td><input name="leave_d" type="text" id="leave_d"></td>
    </tr>

    <tr> 
      <td class="label">Departure Time</td> 
      <td><span class="divider">:</span></td>
      <td><select name="leave_t" id="leave_t">
          <option value="Morning">Morning</option>
          <option value="Afternoon">Afternoon</option>
          <option value="Evening">Evening</option>
          <option value="Late Night">Late Night</option>
          <option value="Flexible">Flexible</option>
        </select>
      </td>
    </tr>

    <tr> 
      <td class="label">Return Date</td> 
      <td><span class="divider">:</span></td>
      <td><input name="home_d" type="text" id="home_d"></td>
    </tr>

    <tr> 
      <td class="label">Departure Time</td> 
      <td><span class="divider">:</span></td>
      <td><select name="home_t" id="home_t">
          <option value="Morning">Morning</option>
          <option value="Afternoon">Afternoon</option>
          <option value="Evening">Evening</option>
          <option value="Late Night">Late Night</option>
          <option value="Flexible">Flexible</option>
        </select>
      </td>
    </tr>

    <tr> 
      <td class="label">Distance from destination you are willing to travel (miles)</td> 
      <td><span class="divider">:</span></td>
      <td><input name="radius" type="text" id="radius"></td>
    </tr>

    <tr> 
      <td class="label">Do you own a car or are you planning on renting one?</td> 
      <td><span class="divider"> : </span></td>
      <td> 
        <select name="car" id="car">
          <option value="No">No</option>
          <option value="Yes">Yes</option>
        </select>
      </td>
    </tr>

    <tr> 
      <td class="label">Notes</td> 
      <td><span class="divider">:</span></td>
      <td><input name="note" type="text" id="note"></td>
    </tr>

</table>
<input type="submit" name="Submit" value="Add trip!">

</form>
</div>

<?php include "footer.php"; ?>
