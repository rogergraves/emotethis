<?php 

class Emotion{

	protected $emotions = array(
		'enthusiastic' => array('type' => 'positive'),
		'elated' => array('type' => 'positive'),
		'excited' => array('type' => 'positive'),
		'thrilled' => array('type' => 'positive'),
		'amazed' => array('type' => 'positive'),
		'happy' => array('type' => 'positive'),
		'satisfied' => array('type' => 'positive'),
		'surprised' => array('type' => 'positive'),
		'content' => array('type' => 'positive'),
		'delighted' => array('type' => 'positive'),
		'outraged' => array('type' => 'negative'),
		'angry' => array('type' => 'negative'),
		'unhappy' => array('type' => 'negative'),
		'frustrated' => array('type' => 'negative'),
		'irritated' => array('type' => 'negative'),
		'humiliated' => array('type' => 'negative'),
		'disgusted' => array('type' => 'negative'),
		'miserable' => array('type' => 'negative'),
		'dissatisfied' => array('type' => 'negative'),
		'uneasy' => array('type' => 'negative'),
	
	);

	public function isEmotion($emote){
		if(array_key_exists($emote,$this->emotions))
			return true;
		return false;
	}
	
	public function listPositive(){
		return array_filter($this->listEmotions(),array($this,'isPositive'));
	}
	
	public function listNegative(){
		return array_filter($this->listEmotions(),array($this,'isNegative'));
	}
	
	public function listEmotions(){
		return array_keys($this->emotions);
	}
	
	public function isNegative($emote){
		$type = $this->getType($emote);
		if($type && $type == 'negative')
			return true;
		return false;
	}
	
	public function isPositive($emote){
		$type = $this->getType($emote);
		if($type && $type == 'positive')
			return true;
		return false;
	}
	
	public function getType($emote){
		if( ! array_key_exists($emote,$this->emotions) )
			return NULL;
			
		return $this->emotions[$emote]['type'];
	}
}


?>