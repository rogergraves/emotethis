<?php
 
require_once(ROOT_PATH . 'includes/Call.php');

CallMapper::registerMapArray(array(
	array('getsurvey',NULL,array('survey',array('action'=>'getsurvey')),NULL,50),//priority for execute before startsurvey
	array('freedashboard',NULL,array(array('action'=>'freedashboard')),NULL,50),
	array('saveuserdata',NULL,array(array('action'=>'saveuserdata')),NULL,50),
	array('savesurveyresult',NULL,array(array('action'=>'savesurveyresult')),NULL,50),
	array('savedemoresult',NULL,array(array('action'=>'savedemoresult')),NULL,50),
	array('widgetsurvey',NULL,array(array('action'=>'widgetsurvey')),NULL,50),
	array('setemail',NULL,array(array('action'=>'setemail')),NULL,50),
	array('startsurvey',NULL,array('survey')),
	array('startsurvey',NULL,array('uid')),
	
	//dashboard actions
	array('scorecard',NULL,array(array('action'=>'scorecard')),NULL,80),
	array('dashboardlogin',NULL,array(array('action'=>'dashboardlogin')),NULL,80),
	array('dashboard',NULL,array(array('action'=>'dashboard')),NULL,80),
	array('deleteresult',NULL,array(array('action'=>'deleteresult')),NULL,80),
	
	array('verbatims',NULL,array(array('action'=>'verbatims')),NULL,80),
	array('reportcsv',NULL,array(array('action'=>'reportcsv')),NULL,80),
	
	array('surveycode',NULL,NULL,NULL,1000), //deault action
));
?>