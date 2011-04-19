<?php 

//ini_set('display_errors',0);
error_reporting(E_ALL);

require_once('config.php');
require_once(ROOT_PATH . 'includes/WebCalls/Survey.php');
require_once(ROOT_PATH . 'includes/WebCalls/Results.php');
require_once(ROOT_PATH . 'includes/Call.php');
require_once(ROOT_PATH . 'includes/Routes.php');

//ob_start();//Start buffering
try{
	

	//set_error_handler("userErrorHandler",E_ALL | E_STRICT);
	
	$call_mapper = new CallMapper();
	
	$method = $call_mapper->getCallName($_SERVER['QUERY_STRING'],$_REQUEST);
	
	//print "Method: $method";
	
	$call_factory = new WebCallFactory($_REQUEST);
	if(!$method || !$call_factory->isCall($method)){
		$out_type = 'html';
		if(array_key_exists("out", $_REQUEST))
			$out_type = $_REQUEST['out'];
		$answer = new Answer('error','Unknow method: "'.$method.'"',$out_type);
	}else{
		$call_obj = $call_factory->getCall($method);
		$answer = $call_obj->call();
	}
	
	$answer_mime = $answer->output_mime();
	if(!$answer_mime) $answer_mime = 'text/html';
	$answer_str = $answer->output();
	header('Content-type: ' . $answer_mime . '; charset=UTF-8');
	
	$answer_file = $answer->getFileName();
	if($answer_file)
		header('Content-disposition: attachment; filename=' . $answer_file );
	
	/*
	if(array_key_exists('out',$_REQUEST) && $_REQUEST['out'] == 'json'){
		$answer_str = $answer->to_json();
	}else{
		$answer_str = $answer->output();
	}
	*/
}catch(Exception $e){
	$answer_str = "Something wrong...";
}

//ob_clean();//clean all output
print $answer_str;
//ob_end_flush();

?>