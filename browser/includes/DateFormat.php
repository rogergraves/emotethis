<?php


function verbatim_date($date){
	$dt = new DateTime($date);
	$dt = new DateTime($dt->format('Y-m-d'));//only date we need

	$dt_now = new DateTime("now");
	$dt_now = new DateTime($dt_now->format('Y-m-d'));//only date we need

	if(!function_exists('date_diff')) {
		require_once(ROOT_PATH . 'includes/date_diff_legacy.php');
		$interval = date_diff($dt,$dt_now);
	}else{
		$interval = date_diff($dt,$dt_now);
	}

	if($interval->y == 0 && $interval->m == 0 && $interval->d == 0){
//		return array("Today","0x649812");
		return array($dt->format('d M'),"0x649812");
	}else if($interval->y == 0 && $interval->m == 0 && $interval->d == 1){
//		return array("Yesterday","0x000000");
		return array($dt->format('d M'),"0x000000");
	}else if($dt->format('Y') == $dt_now->format('Y')){
		return array($dt->format('d M'),"0x000000");
	}else{
		return array($dt->format('d M Y'),"0x000000");
	}
}

?>