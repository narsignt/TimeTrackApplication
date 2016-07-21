<?php
include_once "attribute.php";
global $conn;
//echo $ipaddress;
//echo $dbusername;
//echo $dbpassword;
//echo $dbname;
   $conn=mysql_connect ($ipaddress, $dbusername, $dbpassword) or die ('System cannot connect to the database because: ' . mysql_error());
   mysql_select_db ($dbname);
   if($conn<0)
   echo "connection could not be established"; 


?>