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
	<script type="text/javascript" src="survey.js"></script>
<?php
	include(TEMPLATE_DIR . 'footer.php');
?>