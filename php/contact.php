<?php
session_start();
if(!isset($_SESSION['myusername'])){
	header("location:index.php");
}


$host=$_ENV['OPENSHIFT_DB_HOST']; // Host name 
$username = "admin"; // Mysql username 
$password="a4J83Khlc59W"; // Mysql password 
$db_name="skipthebus"; // Database name 
$tbl_name="trips"; // Table name 
$tbl_members="members"; // Table name 


// Connect to server and select databse.
mysql_connect("$host", "$username", "$password")or die("cannot connect"); 
mysql_select_db("$db_name")or die("cannot select DB");

$email=$_SESSION['myusername'];
$type=$_REQUEST['type'];
$id=$_REQUEST['id'];

$query = "select * from $tbl_name where id = '$id'";
$result=mysql_query($query);

while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
    $to=$row['email'];
    $dest=$row['destination'];
}

if ($type=="home") $tofrom = "from";
else $tofrom = "to";

$subject = 'Skip The Bus Request for Contact';
$message = 'Skip the Bus user, '.$email.', has an overlapping trip '. $tofrom . ' ' . $dest .' and has requested contact with you.  Please feel free to e-mail the user back. - SkipTheBus.com';
$header  = "Reply-To: ".$email."\n";

mail($to, $subject, $message, $headers);

header("location:mySkips.php");


?>



