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
			background: #000000;
			z-index: 100000;
		}
		
		#loading {
			position: absolute;
			color: white;
			font-size: 16px;
			top: 50%;
			left: 40%;
			z-index: 100002;
		}
		#loading #loading-message {
			position: fixed;
			display: block;
			z-index: 100003;
		}
		#main-content{
			display: none;
			height: 100%;
		}
	</style>
	<link rel="stylesheet" href="../css/sencha-touch.css" type="text/css">


</head>
<body>
	<input id="survey_code" type="hidden" value="<?php if( array_key_exists('survey_code',$tpl_vars)){ print $tpl_vars['survey_code'];}?>">

<!--
	<div id="loading-mask"></div>
	<div id="loading">
		<span id="loading-message"> Loading. Please wait...</span>
	</div>
	
	
	<div id="loading-win" style="display: none;">
	</div>
-->

	<div id="loading-mask"></div>
	<div id="loading">
		<span id="loading-message"> Loading...</span>
	</div>



	<div id="landscape-overlay" class="x-hidden-display">
		<div id="landscape-overlay-bg" ></div>
		<div id="landscape-message">
			For best results, please rotate your <br/> phone to Portrait mode.<br/>
			
			<div style="margin-top: 0px;">
			<div style="float: left; margin-left: 65px;">
				<img style="margin-left: 90px; margin-bottom: 0px;" src="../images/red_arrow.png"><br/>
				<img src="../images/h_iphone.png">
			</div>
			<div style="margin-right: 35px;">
				<img style="margin-top: 30px;" src="../images/v_iphone.png">
			</div>
			</div>
			
		</div>
	</div>
	<div id="preloading-win" class="x-hidden-display">
		Loading...
	</div>
	
	

	<link rel="stylesheet" href="../css/phone.css" type="text/css">
	<script type="text/javascript" src="../js/sencha-touch.js"></script>
	<script type="text/javascript" src="../js/iphone.min.js"></script>
</body>
</html>
