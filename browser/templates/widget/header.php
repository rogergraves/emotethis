<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">	
	<title>EmoteThis</title>
	<!-- Preloader -->
	<style type="text/css">
		HTML, BODY { height: 100%; }
		#loading-mask {
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: transparent;
			z-index: 100010;
		}
		#loading {
			position: absolute;
			color: white;
			font-size: 12px;
			top: 40%;
			left: 45%;
			z-index: 100012;
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
	<link rel="stylesheet" href="../css/widget.css" type="text/css">

</head>
<body style="background-color:transparent;">
	<div id="loading-mask"></div>
	<div id="loading">
		<span id="loading-message">Loading. Please wait...</span>
	</div>
	
	<div id="loading-win" style="display: none;">
	</div>
	