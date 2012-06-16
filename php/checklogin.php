<?php
session_start();

$host=$_ENV['OPENSHIFT_DB_HOST']; // Host name 
$username="admin"; // Mysql username 
$password="a4J83Khlc59W"; // Mysql password 
$db_name="skipthebus"; // Database name 
$tbl_name="members"; // Table name 

// Connect to server and select databse.
mysql_connect("$host", "$username", "$password")or die("cannot connect"); 
mysql_select_db("$db_name")or die("cannot select DB");

// username and password sent from form 
$myusername=$_POST['email']; 
$mypassword=md5($_POST['password']); 

// To protect MySQL injection (more detail about MySQL injection)
$myusername = stripslashes($myusername);
$mypassword = stripslashes($mypassword);
$myusername = mysql_real_escape_string($myusername);
$mypassword = mysql_real_escape_string($mypassword);
$sql="SELECT * FROM $tbl_name WHERE email='$myusername' and password='$mypassword'";
$result=mysql_query($sql);

// Mysql_num_row is counting table row
$count=mysql_num_rows($result);

// If result matched $myusername and $mypassword, table row must be 1 row
if($count==1){

// Register $myusername, $mypassword and redirect to file "login_success.php"
$_SESSION["myusername"] = $myusername;
header("location:mySkips.php");
}
else {
	include "header.php";
	echo "Wrong Username or Password. Go <a href='index.php'>back</a>.";
	include "footer.php";
}
?>