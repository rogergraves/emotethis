<?php 
	require_once('./config.php');
	require_once(ROOT_PATH . 'includes/Model.php');
	
    	$survey_manager = new SurveyManager();

	$survey = $survey_manager->getSurvey('test123');

	var_dump($survey->getDemo());
	print "\n";
?>