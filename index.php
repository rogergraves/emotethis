<html>
<head>
<link rel="Shortcut Icon" href="/favicon.ico">
<script language="javascript">

<?php
$dest = "/browser/index.php";
$code = "";

if(isset($_REQUEST['uid']))
	$code = strtoupper($_REQUEST['uid']);
elseif(isset($_REQUEST['survey']))
	$code = strtoupper($_REQUEST['survey']);


if(strlen($code) > 0)
    $dest .= '?survey='.$code;
    

if(strstr($_SERVER['HTTP_USER_AGENT'],'iPhone') || strstr($_SERVER['HTTP_USER_AGENT'],'iPod') || strstr($_SERVER['HTTP_USER_AGENT'],'Android')) {
	if(strlen($code) > 0)
		$dest .= '&';
	else
		$dest .= '?';
	
	$dest .= "device=phone";
}


print("document.location.replace('$dest');\n");


?>
</script>
</head>
</html>