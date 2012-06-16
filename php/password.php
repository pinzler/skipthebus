<?php
$db_host = $_ENV['OPENSHIFT_DB_HOST'];
$db_user = $_ENV['OPENSHIFT_DB_USERNAME'];
$db_pass = $_ENV['OPENSHIFT_DB_PASSWORD'];
$db_name = $_ENV['OPENSHIFT_APP_NAME'];
$db_port = $_ENV['OPENSHIFT_DB_PORT'];


echo $_ENV['OPENSHIFT_DB_USERNAME'];
echo " ";
echo $_ENV['OPENSHIFT_DB_PASSWORD'];
echo " ";
echo $db_host;
echo " ";
echo $db_port;
echo " ";
echo $db_name;


?>



