<?php
	include(TEMPLATE_DIR . 'header_dashboard.php');
?>

<div id="login-win">
	<div class="login-win-header">Enter your password to view e.mote results</div>
	<form id="dashboard-login" method="POST">
		<input name="action" value="dashboardlogin" type="hidden" />
		<div class="fieldBlock">
			<label class="formLabel" for="loginSurveyCode">Survey code</label>
			<input name="uid" class="field" value="" id="loginSurveyCode" tabindex="1" type="text" />
		</div>
		
		<div class="fieldBlock">
			<label class="formLabel" for="loginPassword">Password</label>
			<input name="pw" class="field" value="" id="loginPassword" tabindex="2" type="password" />
		</div>
		<?php 
			if(array_key_exists('login_error',$tpl_vars)){
		?>
		<div class="login-error">Password or code not recognized, please try again.</div>
		<?php 
			}
		?>
		<div class="submitButton">
			<input id="submit-form" type="submit" value="Go">
		</div>
	</form>
</div>
<script type="text/javascript" src="http://code.jquery.com/jquery-1.4.3.js"></script>
<script type="text/javascript" src="../js/curvycorners.js"></script>
<?php
	include(TEMPLATE_DIR . 'footer_dashboard.php');
?>