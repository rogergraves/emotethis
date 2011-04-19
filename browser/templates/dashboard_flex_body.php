<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">	
	<title>EmoteThis</title>
	<link rel="Shortcut Icon" href="/favicon.ico">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<script type="text/javascript" src="../js/AC_OETags.js"></script>
	<script src="../js/history/history.js" language="javascript"></script>
	<style>
		body { margin: 0px; overflow:hidden }
	</style>
	<script language="JavaScript" type="text/javascript">
	<!--
// -----------------------------------------------------------------------------
// Globals
// Major version of Flash required
var requiredMajorVersion = 9;
// Minor version of Flash required
var requiredMinorVersion = 0;
// Minor version of Flash required
var requiredRevision = 124;
// -----------------------------------------------------------------------------
// -->
	</script>

</head>

<body scroll="no" >

<script type="text/javascript" src="../js/AC_OETags.js"></script>
<script language="JavaScript" type="text/javascript">
<!--
// Version check for the Flash Player that has the ability to start Player Product Install (6.0r65)
var hasProductInstall = DetectFlashVer(6, 0, 65);

// Version check based upon the values defined in globals
var hasRequestedVersion = DetectFlashVer(requiredMajorVersion, requiredMinorVersion, requiredRevision);

if ( hasProductInstall && !hasRequestedVersion ) {
	// DO NOT MODIFY THE FOLLOWING FOUR LINES
	// Location visited after installation is complete if installation is required
	var MMPlayerType = (isIE == true) ? "ActiveX" : "PlugIn";
	var MMredirectURL = encodeURI(window.location);
    document.title = document.title.slice(0, 47) + " - Flash Player Installation";
    var MMdoctitle = document.title;

	AC_FL_RunContent(
		"src", "playerProductInstall",
		"FlashVars", "MMredirectURL="+MMredirectURL+'&MMplayerType='+MMPlayerType+'&MMdoctitle='+MMdoctitle+"",
		"width", "1140",
		"height", "740",
		"align", "middle",
		"id", "main",
		"quality", "high",
		"bgcolor", "#ffffff",
		"name", "main",
		"allowScriptAccess","sameDomain",
		"type", "application/x-shockwave-flash",
		"pluginspage", "http://www.adobe.com/go/getflashplayer"
	);
} else if (hasRequestedVersion) {
	// if we've detected an acceptable version
	// embed the Flash Content SWF when all tests are passed
	AC_FL_RunContent(
			"src", "main",
			"FlashVars", "survey=<?= $tpl_vars['survey_code'] ?>&session=<?= $tpl_vars['session'] ?>",
			"width", "1140",
			"height", "740",
			"align", "middle",
			"id", "main",
			"quality", "high",
			"bgcolor", "#ffffff",
			"name", "main",
			"allowScriptAccess","sameDomain",
			"type", "application/x-shockwave-flash",
			"pluginspage", "http://www.adobe.com/go/getflashplayer"
	);
  } else {  // flash is too old or we can't detect the plugin
    var alternateContent = 'Alternate HTML content should be placed here. '
  	+ 'This content requires the Adobe Flash Player. '
   	+ '<a href=http://www.adobe.com/go/getflash/>Get Flash</a>';
    document.write(alternateContent);  // insert non-flash content
  }
// -->
</script>
<noscript>
  	<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
			id="main" width="1140" height="740"
			codebase="http://fpdownload.macromedia.com/get/flashplayer/current/swflash.cab">
			<param name="movie" value="main.swf?survey=<?= $tpl_vars['survey_code'] ?>&session=<?= $tpl_vars['session'] ?>" />
			<param name="quality" value="high" />
			<param name="bgcolor" value="#ffffff" />
			<param name="allowScriptAccess" value="sameDomain" />
			<embed src="main.swf?survey=<?= $tpl_vars['survey_code'] ?>&session=<?= $tpl_vars['session'] ?>" quality="high" bgcolor="#000000"
				width="1140" height="740" name="main" align="middle"
				play="true"
				loop="false"
				quality="high"
				allowScriptAccess="sameDomain"
				type="application/x-shockwave-flash"
				pluginspage="http://www.adobe.com/go/getflashplayer">
			</embed>
	</object>
</noscript>

</body>
</html>