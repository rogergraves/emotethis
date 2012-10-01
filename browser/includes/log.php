<?php

function writeLog($msg = ''){
    $arr = debug_backtrace();
    $where = $arr && count($arr) ? ($arr[0]['file'] . ":" . $arr[0]['line']) : '';
//    var_dump($h);

    $logFile = "scripts.log";
    $fh = fopen($logFile, 'a') or die("can't open file");
    fwrite($fh, $_SERVER['REMOTE_ADDR']. " | " . date(DATE_RFC822) . " | " . $where . " | " . $msg . "\n");
    fclose($fh);
}
/*
$myFile = "testFile.txt";
$fh = fopen($myFile, 'a') or die("can't open file");
$stringData = "New Stuff 1\n";
fwrite($fh, $stringData);
$stringData = "New Stuff 2\n";
fwrite($fh, $stringData);
fclose($fh);
*/

function foo(){

writeLog("test");
}

foo();
?>