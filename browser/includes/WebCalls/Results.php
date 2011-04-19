<?php
require_once(ROOT_PATH . 'includes/Call.php');
require_once(ROOT_PATH . 'includes/Model.php');
require_once(ROOT_PATH . 'includes/Emotion.php');
require_once(ROOT_PATH . 'includes/Template.php');
require_once(ROOT_PATH . 'includes/DateFormat.php');

class DashboardLogin extends ACall{

	public function call(){

		$tpl = new Template('dashboard_login_body.php');
		$survey_code = strtoupper($this->getArgument('uid'));
		$password = $this->getArgument('pw');

		if($_SERVER['REQUEST_METHOD'] == 'POST' || ($survey_code && $password) ){

			$survey_mng = new SurveyManager();
			if(!$survey_code || ! $survey_mng->isSurvey($survey_code))
				return new Answer('ok',$tpl->process(array('login_error' => 1,'survey' => $survey_code)),'html');

			$survey = $survey_mng->getSurvey($survey_code);

			if($password != $survey->getPassword())
				return new Answer('ok',$tpl->process(array('login_error' => 1,'survey' => $survey_code)),'html');

			//All ok, login user to survey results
			if(!session_id()) session_start();

			$_SESSION['survey_results'][$survey_code] = 1;

			$tpl = new Template('dashboard_flex_body.php');

			return new Answer('ok',$tpl->process(array('session' => session_id(), 'survey_code' => $survey_code)),'html');

		}
		return new Answer('ok',$tpl->process(array()),'html');
	}

	public function listArguments(){
		return array('uid','pw');
	}
}
WebCallFactory::registryCall('dashboardlogin','DashboardLogin');

class Dashboard extends ACall{

	public function call(){
		$survey_code = strtoupper($this->getArgument('uid'));
		//check access
		if(!session_id()) session_start();

		if( ! array_key_exists('survey_results' ,$_SESSION) ||
			! array_key_exists($survey_code,$_SESSION['survey_results']) || ! $_SESSION['survey_results'][$survey_code])
			return new Redirect('internal', MAIN_SCRIPT . '?action=dashboardlogin');

		$tpl = new Template('dashboard_flex_body.php');

		return new Answer('ok',$tpl->process(array('session' => session_id(), 'survey_code' => $survey_code)),'html');
	}

	public function listArguments(){
		return array('uid');
	}
}
WebCallFactory::registryCall('dashboard','Dashboard');

class Scorecard extends ACall{

	protected function emote_cmp($a,$b){
		if($a['value'] == $b['value']) return 0;
		return ($a['value'] > $b['value']) ? -1 : 1;
	}

	public function call(){
		try{
			$survey_code = strtoupper($this->getArgument('survey'));

			$survey_mng = new SurveyManager();

			if(!$survey_code || ! $survey_mng->isSurvey($survey_code)){
				$answer = new Answer('error',array("error" => "Survey not found"),'xml');
				$answer->setXMLOpt("rootName","dashboard");
				return $answer;
			}

			$survey_result_mng = new SurveyResultManager();

			$fetchRow = $survey_result_mng->getSurveyFetch($survey_code);

			$positive_int = 0;
			$negative_int = 0;
			$total_emotions = 0;

			$pp = 0;
			$mp = 0;
			$pn = 0;
			$mn = 0;
			$pp_intensities = 0; // Added by Roger to calculate new e.mote score


			$emotion_obj = new Emotion();
			$emotions = array();
			foreach($emotion_obj->listEmotions() as $emote){
				$emotions[$emote] = array('value' => 0, 'name' => $emote, 'type' => $emotion_obj->getType($emote));
			}


			while($row = $fetchRow->nextRow()){

				if( ! array_key_exists($row['emote'],$emotions) ){
					continue;
				}
				$emote = $emotions[$row['emote']];
				++$total_emotions;

				++$emotions[$row['emote']]['value'];
				++$emote['value'];

				/*  Added by Roger on 2010-01-25 to change calcs according to Jeb's instructions. */
				if($row['intensity_level'] >= 0 && $row['intensity_level'] < 34) { // Is positive or negative, intensity is bottom third
					++$mn;
				} elseif($row['intensity_level'] >= 34 && $row['intensity_level'] < 66) { // Is positive or negative, intensity is middle third
					++$mp;
				} elseif($emote['type'] == 'negative') { // Is negative and intensity is >= 66
					++$pn;
				} else { // Is positive and intensity is >= 66
					++$pp;
					
					$pp_intensities += $row['intensity_level'];
				}

				if($emote['type'] == 'negative'){
					$negative_int += $row['intensity_level'] ? $row['intensity_level'] : 1;
				}else{
					$positive_int += $row['intensity_level'] ? $row['intensity_level'] : 1;
				}


				/* Old/replaced code start
					if($emote['type'] == 'negative'){
						$negative_int += $row['intensity_level'] ? $row['intensity_level'] : 1;
						if($row['intensity_level'] > 50)
							++$pn;
						else
							++$mn;
					}else{
						$positive_int += $row['intensity_level'] ? $row['intensity_level'] : 1;
						if($row['intensity_level'] > 50)
							++$pp;
						else
							++$mp;
					}
				Old code end */
			}

			$emote_score = round($pp_intensities / $pp); // Replaced by Roger 2010-01-25
			// $emote_score = (int)(100 * $pp / $total_emotions);

			$a_bar = array();
			usort($emotions,array($this,'emote_cmp'));
			foreach($emotions as  $emote_name => $emote){
				$color = $emote['type'] == 'negative' ? 'red' : 'green';
				array_push($a_bar,array("_attributes" => array("name" => $emote['name'], "value" => $emote['value'], 'color' => $color)));
			}

			$result = array(
				'graph' => array(
					array(
						'bar' => $a_bar,
						"_attributes" => array( "type" => "emotion distribution"),
					),
					array(
						"slice" =>
							array(
								array("_attributes" => array( "name" => "pp", "value" => $pp)),
								array("_attributes" => array( "name" => "mp", "value" => $mp)),
								array("_attributes" => array( "name" => "pn", "value" => $pn)),
								array("_attributes" => array( "name" => "mn", "value" => $mn)),
							),
						"_attributes" => array( "type" => "intensity distrubition")
					),
				),
			);

			$answer = new Answer('ok',$result,'xml');
			$answer->setXMLOpt("rootName","dashboard");
			$answer->setXMLOpt("rootAttributes",array("emote_score" => $emote_score));
			return $answer;

		}catch(Exception $e){
			$answer = new Answer('error',array("error" => $e->getMessage()),'xml');
			$answer->setXMLOpt("rootName","dashboard");
			return $answer;
		}
	}

	public function listArguments(){
		return array('survey','password');
	}

}
WebCallFactory::registryCall('scorecard','Scorecard');


class DeleteResult extends ACall{
	public function call(){
		try{
			$survey_code = strtoupper($this->getArgument('uid'));
			$session = $this->getArgument('session');
			$result_id = $this->getArgument('id');

			$survey_mng = new SurveyManager();

			if(!$survey_code || ! $survey_mng->isSurvey($survey_code)){
				$answer = new Answer('error',array("error" => "Survey not found"),'xml');
				$answer->setXMLOpt("rootName","result");
				$answer->setXMLOpt("rootAttributes",array("success" => "false"));
				return $answer;
			}

			//check access
			if(!session_id()) session_start();

			if( ! array_key_exists('survey_results' ,$_SESSION) ||
				! array_key_exists($survey_code,$_SESSION['survey_results']) ||
				! $_SESSION['survey_results'][$survey_code]){
				//user not logined
				$answer = new Answer('error',array("error" => 'User not logined'),'xml');
				$answer->setXMLOpt("rootAttributes",array("success" => "false"));
				$answer->setXMLOpt("rootName","result");
				return $answer;
			}

			$survey_result_mng = new SurveyResultManager();

			if( ! $survey_result_mng->isResult($survey_code,$result_id)){
				$answer = new Answer('error',array("error" => 'Result not found'),'xml');
				$answer->setXMLOpt("rootAttributes",array("success" => "false"));
				$answer->setXMLOpt("rootName","result");
				return $answer;
			}

			$survey_result_mng->removeResult($survey_code,$result_id);

			$answer = new Answer('ok',array(),'xml');
			$answer->setXMLOpt("rootAttributes",array("success" => "true"));
			$answer->setXMLOpt("rootName","result");
			return $answer;
		}catch(Exception $e){
			$answer = new Answer('error',array("error" => $e->getMessage()),'xml');
			$answer->setXMLOpt("rootAttributes",array("success" => "false"));
			$answer->setXMLOpt("rootName","result");
			return $answer;
		}
	}
	public function listArguments(){
		return array('id', 'uid','session');
	}
}
WebCallFactory::registryCall('deleteresult','DeleteResult');


class Verbatims extends ACall{
	public function call(){
		try{
			$survey_code = strtoupper($this->getArgument('survey'));
			$survey_mng = new SurveyManager();
			if(!$survey_code || ! $survey_mng->isSurvey($survey_code)){
				$answer = new Answer('error',array("error" => "Survey not found"),'xml');
				$answer->setXMLOpt("rootName","dashboard");
				return $answer;
			}

			$emotion_obj = new Emotion();
			$emote = NULL;
			$intensity_distr = NULL;
			$subset = $this->getArgument('subset');
			if($subset)
				if($emotion_obj->isEmotion($subset))
					$emote = $subset;
				else
					$intensity_distr= $subset;

			$a_split_words = preg_split('/[\n\r\s]+/',$this->getArgument('search'));

			$a_words = array();
			foreach($a_split_words as $word){
				$a_words[$word] = 1;
			}
			$a_words = array_keys($a_words);
			$a_words = array_filter($a_words);

			$survey_result_mng = new SurveyResultManager();
			$a_survey_results = $survey_result_mng->filterResults($survey_code,$a_words,$emote,$intensity_distr,
								$this->getArgument('starting'),$this->getArgument('ending'));

			$a_results = array();
			foreach($a_survey_results as $result){
				$verbatims = $result['verbatim'];
				if($a_words){
					$patterns = array();
					$replacements = array();
					foreach($a_words as $search_word){
						array_push($patterns,"/$search_word/");
						array_push($replacements,"<b>$search_word</b>");
					}
					$verbatims = preg_replace($patterns,$replacements,$verbatims);
				}

				$a_verbatims = preg_split('/[\n\r\s]+/',$verbatims);
				$verbatims = implode(" ",$a_verbatims);

				$intensity_level = 1;
				if($result['intensity_level'] >= 33 && $result['intensity_level'] < 66){
					$intensity_level = 2;
				}else if($result['intensity_level'] >= 66){
					$intensity_level = 3;
				}

				list($timestamp,$ts_color) = verbatim_date($result['end_time']);
				array_push($a_results,array(
					$verbatims,
					"_attributes" => array( "face" => $result['emote'] . "_intensity_$intensity_level" ,
						"id" => $result['survey_result_id'], 'timestamp' => $timestamp, 'ts_color' => $ts_color)));
			}

			//$test_str = var_export($a_survey_results,true);
			$to_xml = array();
			if(count($a_results)) $to_xml = array('verbatim' => $a_results);

			$answer = new Answer('ok', $to_xml ,'xml');
			$answer->setXMLOpt("rootName","verbatims");
			$answer->setXMLOpt("rootAttributes",array("survey" => $survey_code,
							"search" => $this->getArgument('search'), "starting" => $this->getArgument('starting') ,
							"ending" => $this->getArgument('ending'), "subset" => $this->getArgument('subset')));
			return $answer;
		}catch(Exception $e){
			$answer = new Answer('error',array("error" => $e->getMessage()),'xml');
			$answer->setXMLOpt("rootName","verbatims");
			return $answer;
		}
	}

	public function listArguments(){
		return array('survey','search','starting' , 'ending', 'subset','password');
	}

}
WebCallFactory::registryCall('verbatims','Verbatims');


class ReportCSV extends ACall{
	public function call(){
		try{
			$pw = $this->getArgument('pw');

			if( ! $pw || $pw != 'thedoor')
				return new Answer('error',"Access denied");

			$survey_code = $this->getArgument('survey');
			$survey_manager = new SurveyManager();

			if( ! $survey_manager->isSurvey($survey_code))
				return new Answer('error',"Survey not found");

			$survey_result_mng = new SurveyResultManager();
			$a_result = $survey_result_mng->getReport($survey_code);

			$answer = new Answer('ok',$a_result,'csv','results_' . $survey_code . '.csv');

			return $answer;
		}catch(Exception $e){
			return new Answer('error',$e->getMessage());
		}
	}

	public function listArguments(){
		return array('survey','pw');
	}

}
WebCallFactory::registryCall('reportcsv','ReportCSV');

?>