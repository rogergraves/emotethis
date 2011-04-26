<?php 

require_once(ROOT_PATH . 'includes/DB.php');
require_once(ROOT_PATH . 'includes/Emotion.php');

/*
 * XML based now
 * */

class SurveyException extends Exception { }

class SurveyManager{

	function isSurvey($survey_code, $only_enabled = false){
		if( file_exists($this->getSurveyXML($survey_code)) ){
			if(! $only_enabled)
				return true;
			else{
				$survey = $this->getSurvey($survey_code);
				if( ! $survey->isDisabled())
					return true;
			}
		}
		return false;
	}
	
	function getSurvey($survey_code){
		return new Survey($this->getSurveyXML($survey_code));
	}
	

	
	protected function getSurveyXML($survey_code){
		return SURVEY_DIR . $survey_code . ".xml";
	}
}


class Survey{
	
	protected $xml;
	
	function __construct($xml_file) {
		if(!file_exists($xml_file))
			throw new SurveyException("Survey " . $xml_file . " not found");
			
		$this->xml = simplexml_load_file($xml_file,'SimpleXMLElement',LIBXML_NOCDATA);
	}
	
	function isDisabled(){
		$status = (string)$this->getAttribute('/survey', 'status');
		//print "Status $status";
		if($status && strtolower($status) == 'off')
			return true;
			
		return false;
	}
	

	
	function getThanks(){
		$a_thanks = $this->xml->xpath('/survey/thanks');
		if($a_thanks){
			$thanks = array_pop($a_thanks);
			return trim((string)$thanks);
		}
		return '';
	}
	
	function getPassword(){
		$password = (string)$this->getAttribute('/survey', 'password');
		
		if($password) return trim((string)$password);
	}
	
	function hasDemo(){
		
		$demo = (string)$this->getAttribute('/survey', 'demographics');
		
		if($demo && strtolower($demo) == 'true')
			return true;
		return false;
	}
	
	protected function getAttribute($path,$attribute){
		$a_items = $this->xml->xpath($path);
		if($a_items){
			$item = array_pop($a_items);
			$a_attr = $item->attributes();
			$attr = $a_attr[$attribute];
			return $attr;
		}
		return '';
	}
	
	function getStimulus(){
		$a_stimulus = $this->xml->xpath('/survey/stimulus');
		if($a_stimulus){
			$stimulus = array_pop($a_stimulus);
			return trim((string)$stimulus);
		}
		return '';
	}
	
	function getItemImage(){
		$a_stimulus = $this->xml->xpath('/survey/stimulus');
		if($a_stimulus){
			$stimulus = array_pop($a_stimulus);
			$a_attributes = $stimulus->attributes();
			return $a_attributes['image'];
			//var_dump($stimulus->attributes());
		}
	}
	
	function getShortStimulus(){
		$a_stimulus = $this->xml->xpath('/survey/stimulus');
		if($a_stimulus){
			$stimulus = array_pop($a_stimulus);
			$a_attributes = $stimulus->attributes();
			return $a_attributes['short'];
		}
	}
	
	function listQuestionsId(){
		$a_demo = $this->xml->xpath('/survey/demo');
		
		$a_demo = $this->xml->xpath('/survey/demo');
		
		$a_results = array();
		if($a_demo){
			$a_demo_result = array();
			foreach($a_demo as $demo){
				$a_questions = $demo->xpath('question');
				if($a_questions){
					foreach($a_questions as $question){
						array_push($a_results, (string)$question['id']);
					}
				}
			}
		}
		return $a_results;
	}
	
	/*
	 * Return array of demo question:
	 * array('block-text' => 'bla-bla',
	 * 	'questions' => (
	 *   id => '',
	 *   title => ''
	 *   type => '',
	 *   values => ((value => , title => ))
	 * )
	 * )
	 */
	function getDemo(){
		$a_demo = $this->xml->xpath('/survey/demo');
		
		if($a_demo){
			$a_demo_result = array();
			foreach($a_demo as $demo){
				$a_questions = $demo->xpath('question');
				
				$a_question_results = array();
				if($a_questions){
					foreach($a_questions as $question){
						$a_question_attr = $question->attributes();
						$question_result = array('id' => (string)$a_question_attr['id']);
						
						$a_q_values = $question->children(); //question values like checkbox, select, radiobox
						$has_values = false;
						if($a_q_values){
							$a_values_results = array();
							foreach ($a_q_values as $q_value){
								$has_values = true;
								$question_result['type'] = $q_value->getName();
								$a_value_attr = $q_value->attributes();
								array_push($a_values_results,array('value' => (string)$a_value_attr['value'], 
															'title' => (string)$q_value ));
								//var_dump($q_value);
							}
							$question_result['title'] = (string)$a_question_attr['title'];
							$question_result['values'] = $a_values_results;
						}
						if( ! $has_values){
							$question_result['title'] = (string)$question;
							$question_result['type'] = 'text';
						}
						array_push($a_question_results,$question_result);
					}
				}
				
				$a_demo_attr = $demo->attributes();
				
				array_push($a_demo_result,array('block-text' => (string)$a_demo_attr['name'], 'questions' => $a_question_results));
			}
			
			return $a_demo_result;
		}else{
			return;
		}
	}
}


class SurveyResultManager{
	
	function isSurveyFree($survey_code){
		
		$homie_db = defined("USE_HOMIE_DB") && USE_HOMIE_DB ? new HomieDB() : new DB();
		$db = new DB();
		$select_user_id = "SELECT user_id FROM surveys WHERE code = '" . mysql_real_escape_string($survey_code) . "' LIMIT 1";
		$rows = $db->runQuery($select_user_id);
		if(is_array($rows[0]) && $rows[0]['user_id']){
			$user_id = $rows[0]['user_id'];
			$select_stype = "SELECT kind FROM subscriptions where user_id='" .mysql_real_escape_string($user_id) . "' ORDER BY user_id DESC LIMIT 1";
			$kind_rows = $homie_db->runQuery($select_stype);
			if(is_array($kind_rows[0]) && $kind_rows[0]['kind'] == "free"){
				return true;
			}
		}
		
		return false;
	}
	
	public function isResult($survey_code,$result_id){
		$db = new DB();
		$sql = "SELECT COUNT(*) AS is_result FROM survey_result WHERE is_removed = 0 AND code = '" . mysql_real_escape_string($survey_code) . 
				"' AND survey_result_id='" . mysql_real_escape_string($result_id) . "' LIMIT 1";
		$rows = $db->runQuery($sql);
		if(is_array($rows) && isset($rows[0]) && $rows[0]['is_result']){
			return true;
		}
		return false;
	}
	
	public function removeResult($survey_code,$result_id){
		$db = new DB();
		$sql = "UPDATE survey_result set is_removed=1 WHERE is_removed = 0 AND code = '".  mysql_real_escape_string($survey_code) .
				"' AND survey_result_id='" . mysql_real_escape_string($result_id) . "' LIMIT 1";
		$db->runQuery($sql,false);
	}
	
	public function getUserData($user_data_id){
		$db = new DB();
		$sql = "SELECT * FROM survey_user_data WHERE survey_user_data_id = '" . mysql_real_escape_string($user_data_id) . "'";
		$rows = $db->runQuery($sql);
		if(is_array($rows) && isset($rows[0])){
			return new SurveyUserData($rows[0]);
		}else{
			return NULL;
		}
	}
	
	public function getSurveyFetch($survey_code){
		$db = new DB();
		$sql = "SELECT * FROM survey_result WHERE is_removed = 0 AND code = '" . mysql_real_escape_string($survey_code) . "'";
		return $db->runQuery($sql,true,true);
	}
	
	public function getSurveyResult($result_id){
		$db = new DB();
		$sql = "SELECT * FROM survey_result WHERE is_removed = 0 AND survey_result_id = '" . mysql_real_escape_string($result_id) . "'";
		$rows = $db->runQuery($sql);
		if(is_array($rows) && isset($rows[0])){
			return new SurveyResult($rows[0]);
		}else{
			return NULL;
		}
	}
	
	
	protected function like_mapper($w){
		return ' verbatim LIKE "%' . mysql_real_escape_string($w) . '%" ';
	}
	
	public function isFree($survey_code){
		
	}
	
	
	public function filterResults($survey_code, $a_words = NULL, $emote = NULL , $intensity_distr = NULL , $starting = 0, $ending = 0 ){
		$db = new DB();
		$starting = (int) $starting;
		$ending = (int) $ending;
		
		$sql = "SELECT * FROM survey_result WHERE is_removed = 0 AND code = '" . mysql_real_escape_string($survey_code) . "'";
		
		if($a_words){
			if(!is_array($a_words)) $a_words = array($a_words);
			$search_criteria = implode(" OR ", array_map(array($this,'like_mapper'),array_filter($a_words)));
			if($search_criteria)
				$sql .= ' AND (' . $search_criteria . ')';
			
		}
		
		if($emote)
			$sql .= " AND emote= '" . mysql_real_escape_string($emote) . "' ";
		
		if($intensity_distr){
			//pp, mp, pn or mn
			$emote_obj = new Emotion();
			$a_positive = $emote_obj->listPositive();
			$a_negative = $emote_obj->listNegative();
			
			// Changed by Roger on 2011/03/08 to use new segmentation
			if($intensity_distr == 'pp'){ // Enthusiasts
				$sql .= " AND emote IN (" . implode(", ", array_map(array('DB','mysql_escape'),$a_positive)) . ") AND intensity_level >= '66' ";
			}else if($intensity_distr == 'pn'){ // Detractors
				$sql .= " AND emote IN (" . implode(", ", array_map(array('DB','mysql_escape'),$a_negative)) . ") AND intensity_level >= '66' ";
			}else if($intensity_distr == 'mp'){ // Participants
				$sql .= " AND intensity_level >= '34' AND intensity_level <= 65";
			}else if($intensity_distr == 'mn'){ // Indifferent
				$sql .= " AND intensity_level <= '33' ";
			}

			/* OLD CODE HERE
			if($intensity_distr == 'pp'){
				$sql .= " AND emote IN (" . implode(", ", array_map(array('DB','mysql_escape'),$a_positive)) . ") AND intensity_level > '50' ";
			}else if($intensity_distr == 'mp'){
				$sql .= " AND emote IN (" . implode(", ", array_map(array('DB','mysql_escape'),$a_positive)) . ") AND intensity_level <= '50' ";
			}else if($intensity_distr == 'pn'){
				$sql .= " AND emote IN (" . implode(", ", array_map(array('DB','mysql_escape'),$a_negative)) . ") AND intensity_level > '50' ";
			}else if($intensity_distr == 'mn'){
				$sql .= " AND emote IN (" . implode(", ", array_map(array('DB','mysql_escape'),$a_negative)) . ") AND intensity_level <= '50' ";
			}
			*/
		}
		
		
		//Sorting by time by default
		$sql .= ' ORDER BY end_time DESC ';
		
		$limit_str = '';
		if($starting){
			$limit_str = ' LIMIT ' . $starting;
			if($ending) $limit_str .= ', ' . $ending;
			else $limit_str .= ', 100'; 
		}else if($ending){
			$limit_str = ' LIMIT 0,' . $ending;
		}
		
		$sql .= $limit_str;
		
		
		return $db->runQuery($sql);
	}
	
	
	public function getReport($survey_code){
		
		$survey_manager = new SurveyManager();
		$survey_obj = $survey_manager->getSurvey($_REQUEST['survey']);
		
		$sql = "SELECT *,CONVERT_TZ(start_time,'+00:00','-7:00') AS start_time_mst,CONVERT_TZ(end_time,'+00:00','-7:00') AS end_time_mst " . 
				" FROM survey_result LEFT JOIN survey_user_data USING(survey_result_id) where is_removed=0 AND code = '" . 
				mysql_real_escape_string($survey_code) . "'";
		$db = new DB();
		$rows = $db->runQuery($sql);
		
		$db = new DB();
		$a_question_id = $survey_obj->listQuestionsId();
		
		$a_header = array('Start','End', 'IP', 'Emotion','Intensity','Verbatim','Name','Email','Phone');
		$a_header = array_merge($a_header,$a_question_id);
		$a_result = array();
		
		foreach($rows as $row){
			$verbatim = $row['verbatim'];
			$a_verbatim = preg_split('/[\n\r\s]+/',$verbatim);
			$verbatim = implode(" ",$a_verbatim);
			$result = array($row['start_time_mst'],$row['end_time_mst'], $row['ip'], $row['emote'],$row['intensity_level'],$verbatim,$row['name'],$row['email'],$row['phone']);
			
			foreach($a_question_id as $question_id){
				$select_question = "SELECT * FROM survey_demo_result where survey_result_id='" . mysql_real_escape_string($row['survey_result_id']) . "' " .
									" AND question_field = '" . mysql_real_escape_string($question_id) . "' LIMIT 1";
				$q_rows = $db->runQuery($select_question);
				if($q_rows){
					$q_row = $q_rows[0];
					$val = array_key_exists('answer',$q_row) ? $q_row['answer'] : '';
					array_push($result,$val);
				}else{
					array_push($result,'');
				}
			}
			array_push($a_result,$result);
			
		}
		array_unshift($a_result,$a_header);

		return $a_result;
	}
}

class SurveyUserData{
	
	protected $id;
	protected $surver_result_id;
	
	protected $name;
	protected $email;
	protected $phone;
	
	function __construct($user_data) {
		if(is_array($user_data)){
			//constructed by DB Row
			$this->id = $user_data['survey_user_data_id'];
			$this->survey_result_id = $user_data['survey_result_id'];
			$this->name = $user_data['name'];
			$this->email = $user_data['email'];
			$this->phone = $user_data['phone'];
		}else{
			//constructed by survey code
			$this->survey_result_id = $user_data;
		}
	}
	
	public function getId(){
		return $this->id;
	}
	
	public function getSurveyResultId(){
		return $this->survey_result_id;
	}
	
	public function setSurveyResultId($survey_result_id){
		$this->survey_result_id = $survey_result_id;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function setName($name){
		$this->name = $name;
	}
	
	public function getEmail(){
		return $this->email;
	}
	
	public function setEmail($email){
		$this->email = $email;
	}
	
	public function getPhone(){
		return $this->phone;
	}
	
	public function setPhone($phone){
		$this->phone = $phone;
	}
	
	
	public function save(){
		if($this->id){
			$sql="UPDATE survey_user_data set survey_result_id = '" . mysql_real_escape_string($this->survey_result_id) . "', " .
											" name = '" . mysql_real_escape_string($this->name) . "', " .
											" email = '" . mysql_real_escape_string($this->email) . "', " .
											" phone = '" . mysql_real_escape_string($this->phone) . "' " .
											" WHERE survey_user_data_id = '" . mysql_real_escape_string($this->id) . "'";
			$db = new DB();
			$db->runQuery($sql,false);
		}else{
			$sql = "INSERT INTO survey_user_data(survey_result_id,name,email,phone) VALUES(" .
						"'" . mysql_real_escape_string($this->survey_result_id) . "'," .
						"'" . mysql_real_escape_string($this->name) . "'," .
						"'" . mysql_real_escape_string($this->email) . "'," .
						"'" . mysql_real_escape_string($this->phone) . "')";
			
			$db = new DB();
			$db->runQuery($sql,false);
			$this->id = $db->last_insert_id();
		}
	}
}

class SurveyResult{
	
	protected $id;
	
	protected $emote;
	protected $intensity_level;
	protected $verbatim;
	protected $ip;
	protected $start_time;
	protected $end_time;
	
	protected $name;
	protected $email;
	protected $phone;

	protected $survey_code;
	
	function __construct($survey) {
		if(is_array($survey)){
			//constructed by DB Row
			$db = new DB();
			$this->id = $survey['survey_result_id'];
			
			$this->emote = $survey['emote'];
			$this->intensity_level = $survey['intensity_level'];
			$this->verbatim = $survey['verbatim'];
			$this->ip = $survey['ip'];
			$this->start_time = $db->datetime_to_unix($survey['start_time']);
			$this->end_time = $db->datetime_to_unix($survey['end_time']);
			$this->survey_code = $survey['code'];
		}else{
			//constructed by survey code
			$this->survey_code = $survey;
		}
	}
	
	public function getId(){
		return $this->id;
	}
	
	public function getCode(){
		return $this->survey_code;
	}
	
	public function setEmote($emote){
		$this->emote = $emote;
	}

	public function getEmote(){
		return $this->emote;
	}
	
	public function setIntensityLevel($intensity_level){
		$this->intensity_level = $intensity_level;
	}

	public function getIntensityLevel(){
		return $this->intensity_level;
	}
	
	public function getVerbatim(){
		return $this->verbatim;
	}
	
	public function setVerbatim($verbatim){
		$this->verbatim = $verbatim;
	}
	
	public function setIp($ip){
		$this->ip = $ip;
	}
	
	public function getIp(){
		return $this->ip;
	}
	
	public function getStartTime(){
		return $this->start_time;
	}
	
	public function setStartTime($start_time){
		$this->start_time = $start_time;
	}

	public function getEndTime(){
		return $this->end_time;
	}
	
	public function setEndTime($end_time){
		$this->end_time = $end_time;
	}
	

	
	public function save(){
		if($this->id){
			$db = new DB();
			$sql="UPDATE survey_result set emote = '" . mysql_real_escape_string($this->emote) . "'," .
											" intensity_level = '" . mysql_real_escape_string($this->intensity_level) . "', " .
											" verbatim = '" . mysql_real_escape_string($this->verbatim) . "', " .
											" ip = '" . mysql_real_escape_string($this->ip) . "', " .
											" start_time = '" . mysql_real_escape_string($db->mysql_datetime($this->start_time)) . "', " .
											" end_time = '" . mysql_real_escape_string($db->mysql_datetime($this->end_time)) . "' " .
											" WHERE survey_result_id = '" . mysql_real_escape_string($this->id) . "'";
			
			$db->runQuery($sql,false);
		}else{
			$db = new DB();
			//insert new result
			$sql = "INSERT INTO survey_result(emote,intensity_level,verbatim,ip,start_time,end_time,code) VALUES(" .
						"'" . mysql_real_escape_string($this->emote) . "'," .
						"'" . mysql_real_escape_string($this->intensity_level) . "'," .
						"'" . mysql_real_escape_string($this->verbatim) . "'," .
						"'" . mysql_real_escape_string($this->ip) . "'," .
						"'" . mysql_real_escape_string($db->mysql_datetime($this->start_time)) . "'," .
						"'" . mysql_real_escape_string($db->mysql_datetime($this->end_time)) . "'," .
						"'" . mysql_real_escape_string($this->survey_code) . "')";
			
			
			$db->runQuery($sql,false);
			$this->id = $db->last_insert_id();
		}
	}
}

class DemoResult{
	
	protected $id;
	protected $survey_result_id;
	protected $question_field;
	protected $answer;
	
	function __construct($demo) {
		if(is_array($demo)){
			//constructed by DB Row
			$this->id = $demo['survey_demo_result_id'];
			$this->survey_result_id = $demo['survey_result_id'];
			$this->question_field = $demo['question_field'];
			$this->answer = $demo['answer'];
		}else{
			//constructed by survey id
			$this->survey_result_id = $demo;
		}
	}
	
	public function getId(){
		return $this->id;
	}
	
	public function getSurveyResultId(){
		return $this->survey_result_id;
	}
	
	public function setSurveyResultId($survey_result_id){
		$this->survey_result_id = $survey_result_id;
	}
	
	public function getQuestionField(){
		return $this->question_field;
	}
	
	public function setQuestionField($question_field){
		$this->question_field = $question_field;
	}
	
	public function getAnswer(){
		return $this->answer;
	}
	
	public function setAnswer($answer){
		$this->answer = $answer;
	}
	
	public function save(){
		if($this->id){
			$sql="UPDATE survey_demo_result set survey_result_id = '" . mysql_real_escape_string($this->survey_result_id) . "'," .
											" question_field = '" . mysql_real_escape_string($this->question_field) . "', " .
											" answer = '" . mysql_real_escape_string($this->answer) . "' " .
											" WHERE survey_demo_result_id = '" . mysql_real_escape_string($this->id) . "'";
			$db = new DB();
			$db->runQuery($sql,false);
		}else{
			$sql = "INSERT INTO survey_demo_result(survey_result_id,question_field,answer) VALUES(" .
						"'" . mysql_real_escape_string($this->survey_result_id) . "'," .
						"'" . mysql_real_escape_string($this->question_field) . "'," .
						"'" . mysql_real_escape_string($this->answer) . "')";
			
			$db = new DB();
			$db->runQuery($sql,false);
			$this->id = $db->last_insert_id();
		}
	}
}

?>