<?php
require_once 'XML/Serializer.php';


class CallException extends Exception { }

abstract class ACallFactory{

	protected static $calls = array();

	public static function registryCall($call_name,$cls){
		if(isset(ACallFactory::$calls[$call_name]))
			throw new CallException('Call : "'.$call_name.'" already registered');

		ACallFactory::$calls[$call_name] = $cls;
	}

	public static function updateCall($call_name,$cls){
		if(!isset(ACallFactory::$calls[$call_name]))
			throw new CallException('Call : "'.$call_name.'" does not register');
		ACallFactory::$calls[$call_name] = $cls;
	}

	public function listCalls(){
		return array_keys(self::$calls);
	}

	public function isCall($call_name){
		if(isset(self::$calls[$call_name]))
			return true;
		return false;
	}

	public function getCallClass($call_name){
		if(isset(self::$calls[$call_name]))
			return self::$calls[$call_name];
	else 
		return '';
	}

	abstract public function getCall($call_name);
}


class CallMapper{
	protected static $mapper = array();
	
	public function getCallName($url,$request){
		
		
		foreach (CallMapper::$mapper as $map){
			if($map['call_check']){
				$func = $map['call_check'];
				if( ! $func($url,$request)) continue;
			}
			
			if($map['url_pattern']){
				if( ! preg_match($map['url_pattern'], $url, $matches)) continue;
			}
			
			if($map['request_map']){
				$success = true;
				foreach ($map['request_map'] as $param){
					if(is_array($param)){
						$a_param_keys = array_keys($param);
						foreach($a_param_keys as $param_key){
							if( ! array_key_exists($param_key,$request)){
								$success = false;
								break;
							}
							else if($request[$param_key] != $param[$param_key]){
								$success = false;
								break;
							}
						}
						if( ! $success ) break;
					}
					else{
						if( ! array_key_exists($param,$request)){
							$success = false;
							break;
						}
					}
				}
				if( ! $success) continue;
			}
			
			return $map['call_name'];
		}
		
		return '';
	}
	
	
	public function dump(){
		var_dump(CallMapper::$mapper);
	}
	
	public static function cmpMap($a,$b){
		if ($a['priority'] == $b['priority'])
			return 0;
		return ($a['priority'] < $b['priority']) ? -1 : 1;
	}
	
	public static function registerMap($call_name, $url_pattern = NULL, $request_map = NULL, $call_check = NULL, $priority = 100){
		$map = array('call_name' => $call_name, 
					'url_pattern' => $url_pattern, 
					'request_map' => $request_map, 
					'call_check' => $call_check, 
					'priority' => $priority);
		
		array_push(CallMapper::$mapper,$map);

		usort(CallMapper::$mapper, array('CallMapper','cmpMap'));
	}
	
	public static function registerMapArray($a_maps){
		foreach ($a_maps as $map_values){
			//list($call_name, $url_pattern, $request_map,$call_check,$priority) = $map_values;
			$map = array('call_name' => (array_key_exists(0,$map_values) ? $map_values[0] : ''), 
					'url_pattern' => (array_key_exists(1,$map_values) ? $map_values[1] : NULL), 
					'request_map' => (array_key_exists(2,$map_values) ? $map_values[2] : NULL), 
					'call_check' => (array_key_exists(3,$map_values) ? $map_values[3] : NULL), 
					'priority' => (array_key_exists(4,$map_values) ? $map_values[4] : 100));
			array_push(CallMapper::$mapper,$map);
		}
		usort(CallMapper::$mapper, array('CallMapper','cmpMap'));
	}
	
}

class WebCallFactory extends ACallFactory{

	protected $request;
	public function __construct($request) {
		$this->request = $request;	
	}

	public function getCall($call_name){
		$call_cls = $this->getCallClass($call_name);

		if(!$call_cls)
			throw new CallException('Class not found for call: "'.$call_name.'"');
	
		$call_obj = new $call_cls($call_name);

		$call_obj->setCallName($call_name);

		$a_arguments = array();
		$a_arguments_name = $call_obj->listArguments();
		//simple map from request
		foreach($a_arguments_name as $arg){
			if(isset($this->request[$arg]))
			$a_arguments[$arg]=$this->request[$arg];
		}
		$call_obj->setArguments($a_arguments);
		return $call_obj;
	}
}

class Validate{
	function __construct($val){
		
	}
}

class Answer{

	protected $status;
	protected $msg;
	protected $output = 'html';
	protected $file_name;
	protected $xml_options = array(
		"indent"          => "    ",
		"linebreak"       => "\n",
		"typeHints"       => false,
		"addDecl"         => true,
		"encoding"        => "UTF-8",
		"rootName"        => "rdf:RDF",
		"defaultTagName"  => "item",
		"cdata"           => true,
		'mode' => 'simplexml',
		"attributesArray" => "_attributes"
	);

	public function __construct($status='ok', $msg='',$output = 'html',$file_name = ''){
		$this->status = $status;
		$this->msg = $msg;
		$this->output = $output;
		$this->file_name = $file_name;
	}

	public function getFileName(){
		return $this->file_name;
	}
	
	public function to_json(){
		$ret = '';
		if(is_array($this->msg)){
			$ret = $this->msg;
		}else{
			$ret = array(
				'status' => $this->status,
				'msg' => $this->msg
			);
		} 
		return json_encode($ret);
	}

	public function to_html(){
		if(is_array($this->msg)){
			return var_export($this->msg,true);
		}else{
			return $this->msg;
		}
	}
	
	public function output_mime(){
		if($this->output == 'json'){
			return 'application/json';
		}else if($this->output == 'xml'){
			return 'text/xml';
		}else if($this->output == 'csv'){
			return 'text/csv';
		}else{
			return 'text/html';
		}
	}
	
	public function output(){
		if($this->output == 'json'){
			return $this->to_json();
		}else if($this->output == 'xml'){
			return $this->to_xml();
		}else if($this->output == 'csv'){
			return $this->to_csv();
		}else{
			return $this->to_html();
		}
		
	}
	
	public function setXMLOpt($name,$value){
		$this->xml_options[$name] = $value;
	}
	
	public function to_xml(){
		$serializer = new XML_Serializer($this->xml_options);
		$result = $serializer->serialize($this->msg);
		if( $result === true ) {
			return $serializer->getSerializedData();
		}
		throw CallException('Cant serialize to the XML data');
	}

	public function to_csv(){
		
		if( ! is_array($this->msg))
			return $this->msg;
		
		$full_csv = '';
		foreach ( $this->msg as $line){
			$fp = fopen('php://temp', 'r+');//$delimiter = ',', $enclosure = '"'
			fputcsv($fp, $line, ',', '"');
			rewind($fp);
			$full_csv .= fgets($fp);
			fclose($fp);
		}
		return $full_csv;
	}
	
	public function is_redirect(){
		if($this->output == 'redirect')
			return true;
		return false;
	}
	
	public function getStatus(){
		return $this->status;
	}

	public function setStatus($status){
		$this->status = $status;
	}

	public function getMsg(){
		return $this->msg;
	}

	public function setMsg($msg){
		$this->msg = $msg;
	}

	public function isOK(){
		if($this->status == 'ok')
			return true;
		return false;
	}
}

class Redirect extends Answer{
	
	protected $type = '';
	protected $url = '';
	
	public function __construct($type,$url = ''){
		$this->type = $type;
		$this->url = $url;
	}
	
	public function output(){
		if($this->type == 'internal')
			header("Location: http://" . $_SERVER['HTTP_HOST'] . $this->url);
		else
			header("Location: " . $this->url);
		die();
	}
}

abstract class ACall{

	protected $arguments = array();
	protected $method_name = NULL;

	public function setArguments($arguments){
		$this->arguments = $arguments;
	}

	public function getArgument($arg_name){
		if(!$arg_name || !$this->isArgument($arg_name))
			return '';
			return $this->arguments[$arg_name];
	}
	
	public function isArgument($arg_name){
		if(isset($this->arguments[$arg_name]))
			return true;
		return false;
	}

	public function getCallName(){
		return $this->method_name;
	}

	public function setCallName($method_name){
		$this->method_name = $method_name;
	}

	abstract public function call();
	abstract public function listArguments();
}

?>