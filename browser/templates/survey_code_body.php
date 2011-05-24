<?php
	include(TEMPLATE_DIR . 'header.php');
?>

	<div id="main-content">
	<?php
		include(TEMPLATE_DIR . 'navigation.php');
	?>
		
		<div id="survey-area" class="blue-bg">
		
		<?php
			include(TEMPLATE_DIR . 'survey_code.php');
		?>
		
		</div>
	</div>

	<script>
	var preloadImages = [
		'../images/browser/emote_logo.png',
		'../images/browser/back_button.png',
		'../images/browser/next_button.png',
		'../images/browser/bg.jpg',
		'../images/browser/left_window_background.png',
		'../images/browser/first_faces_transparent_bg.png',
		'../images/browser/emotion_picker_window.png',
		'../images/browser/submit_button.png',
		'../images/browser/ajax-loader.gif'
	];
	</script>
<?php
	include(TEMPLATE_DIR . 'jscripts.php');
?>
	<script>
		var surveyCodeRequire = true;
	</script>
	<div id="fb-root"></div>
	<script type="text/javascript" src="http://connect.facebook.net/en_US/all.js"></script>
	<script type="text/javascript" src="../js/survey.js"></script>

<?php
	include(TEMPLATE_DIR . 'footer.php');
?>