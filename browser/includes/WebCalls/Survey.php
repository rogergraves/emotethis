<?php 
require_once(ROOT_PATH . 'includes/Call.php');
require_once(ROOT_PATH . 'includes/Template.php');
require_once(ROOT_PATH . 'includes/Model.php');

//registerMap($call_name, $url_pattern = NULL, $request_map = NULL, $call_check = NULL, $priority = 100){
class GetSurvey extends ACall{

	public function call(){
		try{
			$survey_code = strtoupper($this->getArgument('survey'));
			if( ! $survey_code)
				$survey_code = strtoupper($this->getArgument('uid'));
			$device = $this->getArgument('device');
			$survey_manager = new SurveyManager();
			if($survey_manager->isSurvey($survey_code)){
			
				$survey = $survey_manager->getSurvey($survey_code);
				
				if($survey->isDisabled()){
					
					if($device == 'phone'){
						$short_stimulus = (string)$survey->getShortStimulus();
						
						$store_contacts = $survey->storeRespondentContacts() ? 1 : 0;
						return new Answer('ok',array('short_stimulus' => $short_stimulus,'status' => 'disabled','store_contacts' => $store_contacts),'json');
					}else{
						$survey_tpl = new Template('survey_not_found_body.php');
						return new Answer('ok',$survey_tpl->process(array()),'json');
					}
				}
				
				if(!session_id()) {
					session_start();
				}

				$_SESSION['survey_code'] = strtolower($survey_code);
				$_SESSION['start_time'] = time();
				
				unset($_SESSION['result_id']);
				unset($_SESSION['user_data_id']);
				if($device == 'phone'){
					$short_stimulus = (string)$survey->getShortStimulus();
					$store_contacts = $survey->storeRespondentContacts() ? 1 : 0;
					return new Answer('ok',array('short_stimulus' => $short_stimulus,'status' => 'ok', 'store_contacts' => $store_contacts),'json');
				}
				$survey_tpl = new Template('survey_ajax.php');
				return new Answer('ok',$survey_tpl->process(array('survey' => $survey, 'not_welcome' => true)),'json');
			}else{
				return new Answer('error','Survey not found','json');
			}	
			
		}catch(Exception $e){
			return new Answer('error',$e->getMessage(),'json');
		}
	}

	public function listArguments(){
		return array('survey','device');
	}
}
WebCallFactory::registryCall('getsurvey','GetSurvey');

class StartSurvey extends ACall{
	public function call(){
		try{
			$survey_code = strtoupper($this->getArgument('survey'));
			if( ! $survey_code){
				$survey_code = strtoupper($this->getArgument('uid'));
			}
			
			$survey_manager = new SurveyManager();
			$device = $this->getArgument('device');
			
			if($survey_manager->isSurvey($survey_code)){
				$survey = $survey_manager->getSurvey($survey_code);
				if($survey->isDisabled()){
					if($device == 'phone'){
						$survey_tpl = new Template('phone/main.php');
						return new Answer('ok',$survey_tpl->process(array('device' => $device,'survey_code' => $survey_code)),'html');
					}else{
						$survey_tpl = new Template('survey_not_found.php');
						return new Answer('ok',$survey_tpl->process(array()),'html');
					}
				}
				if(!session_id()) {
					session_start();
				}
				$_SESSION['survey_code'] = strtolower($survey_code);
				$_SESSION['start_time'] = time();
				
				unset($_SESSION['result_id']);
				unset($_SESSION['user_data_id']);
				
				if($device == 'phone'){
					$survey_tpl = new Template('phone/main.php');
					return new Answer('ok',$survey_tpl->process(array('device' => $device,'survey_code' => $survey_code)),'html');
				}
				$survey_tpl = new Template('survey_body.php');
				return new Answer('ok',$survey_tpl->process(array('survey' => $survey, 'not_welcome' => true)),'html');
			}else{
				//survey not found output page for input survey code
				if($device == 'phone'){
					$survey_tpl = new Template('phone/main.php');
					return new Answer('ok',$survey_tpl->process(array('device' => $device)),'html');
				}
				//$survey_tpl = new Template('survey_code_body.php');
				$survey_tpl = new Template('survey_not_found.php');
				return new Answer('ok',$survey_tpl->process(array()),'html');
			}
		}catch(Exception $e){
			return new Answer('error',$e->getMessage(),'html');
		}
	}

	public function listArguments(){
		return array('survey','device','uid');
	}
}
WebCallFactory::registryCall('startsurvey','StartSurvey');


class WidgetSurvey extends ACall{
	public function call(){
		try{
			$survey_code = strtoupper($this->getArgument('survey'));
			$survey_manager = new SurveyManager();
			
			if($survey_manager->isSurvey($survey_code,true)){
				$survey = $survey_manager->getSurvey($survey_code);
				if(!session_id()) {
					session_start();
				}
				$_SESSION['survey_code'] = strtolower($survey_code);
				$_SESSION['start_time'] = time();
				
				unset($_SESSION['result_id']);
				unset($_SESSION['user_data_id']);
				
				$survey_tpl = new Template('widget/survey_body.php');
				return new Answer('ok',$survey_tpl->process(array('survey' => $survey, 'not_welcome' => true)),'html');
			}else{
				$survey_tpl = new Template('widget/not_found.php');
				return new Answer('ok',$survey_tpl->process(array()),'html');
			}
		}catch(Exception $e){
			return new Answer('error',$e->getMessage(),'html');
		}
	}

	public function listArguments(){
		return array('survey','device');
	}
}
WebCallFactory::registryCall('widgetsurvey','WidgetSurvey');


class SurveyCode extends ACall{
	public function call(){
		try{
			$device = $this->getArgument('device');
			if($device == 'phone'){
				$survey_tpl = new Template('phone/main.php');
				return new Answer('ok',$survey_tpl->process(array('device' => $device)),'html');
			}else{
				$survey_tpl = new Template('survey_code_body.php');
				return new Answer('ok',$survey_tpl->process(array()),'html');
			}
		}catch(Exception $e){
			return new Answer('error',$e->getMessage(),'html');
		}
	}

	public function listArguments(){
		return array('device');
	}

}
WebCallFactory::registryCall('surveycode','SurveyCode');

/*
class GetSurveyData extends ACall{

}
*/

class SaveSurveyResult extends ACall{
	public function call(){
		try{
			//sleep(10);
			if(!session_id()) {
				session_start();
			}
			
			$survey_code =& $_SESSION['survey_code'];
			$start_time =& $_SESSION['start_time'];
			$result_id =& $_SESSION['result_id'];
			
			if( ! $survey_code) return new Answer('error','Survey code not defined','json');
			
			$sr_obj = NULL;
			if($result_id){
				$survey_result_manager = new SurveyResultManager();
				$sr_obj = $survey_result_manager->getSurveyResult($result_id);
			}
			
			if(! $sr_obj)
				$sr_obj = new SurveyResult($survey_code);
			
			$sr_obj->setEmote($this->getArgument('emote'));
			$sr_obj->setIntensityLevel($this->getArgument('intensity_level'));
			$sr_obj->setVerbatim(trim($this->getArgument('verbatim')));
			$sr_obj->setIp($_SERVER['REMOTE_ADDR']);
			$sr_obj->setStartTime($start_time);
			$sr_obj->setEndTime(time());
			$sr_obj->save();
			
			$_SESSION['result_id'] = $sr_obj->getId();
			
			return new Answer('ok','save','json');
		}catch(Exception $e){
			return new Answer('error',$e->getMessage(),'json');
		}
	}

	public function listArguments(){
		return array('emote','intensity_level','verbatim');
	}
}
WebCallFactory::registryCall('savesurveyresult','SaveSurveyResult');


class SaveUserData extends ACall{
	public function call(){
		try{
			if(!session_id()) {
				session_start();
			}
			
			$survey_code =& $_SESSION['survey_code'];
			$result_id =& $_SESSION['result_id'];
			$user_data_id =& $_SESSION['user_data_id'];
			
			
			if(!$result_id) return new Answer('error','Unknow result','json');
			
			$a_results = array();
			
			$s_user_obj = NULL;
			if($user_data_id){
				$survey_result_manager = new SurveyResultManager();
				$s_user_obj = $survey_result_manager->getUserData($user_data_id);
			}
			
			if( ! $s_user_obj)
				$s_user_obj = new SurveyUserData($result_id);
			
			$s_user_obj->setName($this->getArgument('name'));
			$s_user_obj->setEmail($this->getArgument('email'));
			$s_user_obj->setPhone($this->getArgument('phone'));
			$s_user_obj->save();
			
			$_SESSION['user_data_id'] = $s_user_obj->getId();
			
			return new Answer('ok',"User data save",'json');
		}catch(Exception $e){
			return new Answer('error',$e->getMessage(),'json');
		}
	}

	public function listArguments(){
		return array('name','email','phone');
	}

}
WebCallFactory::registryCall('saveuserdata','SaveUserData');


class SaveDemoResult extends ACall{
	public function call(){
		try{
			if(!session_id()) {
				session_start();
			}
			
			$survey_code =& $_SESSION['survey_code'];
			$result_id =& $_SESSION['result_id'];
			
			if(!$result_id) return new Answer('error','Unknow result','json');
			
			$a_results = array();
			
			$demo_data = $this->getArgument('demo');
			$fields = explode("&",$demo_data);
			foreach($fields as $field){
				$field_key_value = explode("=",$field);
				$key = urldecode($field_key_value[0]);
				$value =array_key_exists(1,$field_key_value) ? $field_key_value[1] : '' ;
				
				if(array_key_exists($key,$a_results)){
					$a_results[$key] .= "," . trim($value);
				}else{
					$a_results[$key] = trim($value);
				}
			}
			
			foreach($a_results as $key => $value){
				$dr_obj = new DemoResult($result_id);
				$dr_obj->setQuestionField($key);
				$dr_obj->setAnswer($value);
				$dr_obj->save();
			}
			
			return new Answer('ok',"Demo save",'json');
		}catch(Exception $e){
			return new Answer('error',$e->getMessage(),'json');
		}
	}

	public function listArguments(){
		return array('demo');
	}

}
WebCallFactory::registryCall('savedemoresult','SaveDemoResult');


class SetEmail extends ACall{
	public function call(){
		try{
			if(!session_id()) {
				session_start();
			}
			
			$survey_code =& $_SESSION['survey_code'];
			$result_id =& $_SESSION['result_id'];
			$user_data_id =& $_SESSION['user_data_id'];
			
			if(!$result_id) return new Answer('error','Unknow result id','json');
			
			$email = $this->getArgument('email');
			$s_user_obj = NULL;
			
			if($user_data_id){
				$survey_result_manager = new SurveyResultManager();
				$s_user_obj = $survey_result_manager->getUserData($user_data_id);
			}
			
			if( ! $s_user_obj){
				$s_user_obj = new SurveyUserData($result_id);
			}
			
			$s_user_obj->setEmail($email);
			$s_user_obj->save();
			
			$_SESSION['user_data_id'] = $s_user_obj->getId();
			
			return new Answer('ok',"User data save",'json');
		}catch(Exception $e){
			return new Answer('error',$e->getMessage(),'json');
		}
	}

	public function listArguments(){
		return array('email');
	}

}
WebCallFactory::registryCall('setemail','SetEmail');



?>