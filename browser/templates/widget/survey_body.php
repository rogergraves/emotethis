<?php
	include(TEMPLATE_DIR . 'widget/header.php');
?>

	<div id="main-content">
	<?php
		include(TEMPLATE_DIR . 'widget/navigation.php');
	?>
		
		<div id="survey-area" class="blue-bg">
				

		
		<?php
			include(TEMPLATE_DIR . 'widget/slider.php');
		?>
		
		</div>
	</div>

<?php
	include(TEMPLATE_DIR . 'widget/jscripts.php');
?>

	<div id="fb-root"></div>
	<script type="text/javascript" src="http://connect.facebook.net/en_US/all.js"></script>
	<script type="text/javascript" src="/js/widget.js"></script>
	
<?php
	include(TEMPLATE_DIR . 'widget/footer.php');
?>