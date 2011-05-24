<?php
	include(TEMPLATE_DIR . 'header.php');
?>

	<div id="main-content">
	<?php
		include(TEMPLATE_DIR . 'navigation.php');
	?>
		
		<div id="survey-area" class="blue-bg">
				

		
		<?php
			include(TEMPLATE_DIR . 'slider.php');
		?>
		
		</div>
	</div>

<?php
	include(TEMPLATE_DIR . 'jscripts.php');
?>
	<div id="fb-root"></div>
	<script src="http://connect.facebook.net/en_US/all.js"></script>
	<script type="text/javascript" src="../js/survey.js"></script>
	
<?php
	include(TEMPLATE_DIR . 'footer.php');
?>