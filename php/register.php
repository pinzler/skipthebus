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
$myphone=$_POST['phone']; 

// To protect MySQL injection (more detail about MySQL injection)
$myusername = stripslashes($myusername);
$mypassword = stripslashes($mypassword);
$myphone = stripslashes($myphone);
$myusername = mysql_real_escape_string($myusername);
$mypassword = mysql_real_escape_string($mypassword);
$myphone = mysql_real_escape_string($myphone);
$sql="SELECT * FROM $tbl_name WHERE email='$myusername';";
$result=mysql_query($sql);

// Mysql_num_row is counting table row
$count=mysql_num_rows($result);

// If result matched $myusername and $mypassword, table row must be 1 row
if($count==0){

$sql2="INSERT INTO $tbl_name (email, password, phone) VALUES ('$myusername', '$mypassword', '$myphone');";
$result2=mysql_query($sql2);

$_SESSION["myusername"] = $myusername; 
$_SESSION["myphone"] = $myphone; 
header("location:mySkips.php");

}
else {
	include "header.php";
echo "Username already exists. Go <a href='index.php'>back</a>.";
include "footer.php";
}
?>