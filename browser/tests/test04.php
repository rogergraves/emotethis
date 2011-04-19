<?php

require_once('../config.php');
require_once('../includes/Call.php');
//require_once('../includes/WebCalls/Survey.php');

CallMapper::registerMapArray(array(
    array('getsurvey',NULL,array('survey',array('action'=>'getsurvey')),NULL,50),//priority for execute before startsurvey
    array('startsurvey',NULL,array('survey')),
    array('surveycode',NULL,NULL,NULL,1000), //deault action
));

$mapper_obj = new CallMapper();

$mapper_obj->dump();

print $mapper_obj->getCallName('index.php',array('survey' => 123)) . "\n";

if(! array_key_exists('survey',array('survey' => 123))){
    print "OK\n";
}
?>