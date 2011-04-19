<?php
require_once('config.php');


$db = mysql_connect(DBHOST, DBUSER, DBPASS);
mysql_select_db(DBNAME);

if (!$db || !is_resource($db)) {
    echo "Cant connect to the database: " . mysql_errno($db) . ": " . mysql_error($db);
}else{
    echo "OK";
    $result = mysql_query("SHOW TABLES");
    $returnResult = array();

    if( ! $result ){
        echo("Db error " . mysql_errno($db) . ": " . mysql_error($db) );
    }else{
	while($row = mysql_fetch_assoc($result)){
	    $returnResult[] = $row;
	}
	var_dump($returnResult);
    }
}

?>