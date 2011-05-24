<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">	
	<title><?php 
//	    if(array_key_exists('survey',$tpl_vars)){
//		print("How do you feel about " . $tpl_vars['survey']->getShortStimulus() . " ?");
//	    }
	?></title>
	<!-- Preloader -->
	<style type="text/css">
		HTML, BODY { height: 100%; }
		#loading-mask {
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: #000000;
			z-index: 1;
		}
		#loading {
			position: absolute;
			color: white;
			font-size: 12px;
			top: 40%;
			left: 45%;
			z-index: 2;
		}
		#loading #loading-message {
			background: url('../images/browser/loader.gif') no-repeat left center;
			padding: 5px 30px;
			display: block;
		}
		#main-content{
			display: none;
			height: 100%;
		}
	</style>
	<link rel="Shortcut Icon" href="/favicon.ico">
	<link rel="stylesheet" href="../css/browser.css" type="text/css">
                            	
</head>
<body>
	<div id="loading-mask"></div>
	<div id="loading">
		<span id="loading-message"><span id="loading-percent">5</span>% Loading. Please wait...</span>
	</div>
	
	<div id="loading-win" style="display: none;">
	</div>
	