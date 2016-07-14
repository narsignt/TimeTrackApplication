<?php
global $conn;
   $conn=mysql_connect ("68.178.217.5", "italentnewdb", "Q!w2e3r4") or die ('System cannot connect to the database because: ' . mysql_error());
   mysql_select_db ("italentnewdb");
   if($conn<0)
   echo "connection could not be established"; 


?>