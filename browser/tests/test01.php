<?php 
	require_once('../config.php');
	require_once('../includes/Model.php');
	
    	$survey_manager = new SurveyManager();

	$survey = $survey_manager->getSurvey('test123');

	print "Stimulus: " . $survey->getStimulus() . "\n";
	print "Item Image: " . $survey->getItemImage() . "\n";
?>