<?php 
	define('ROOT_PATH', './');
	
	//directories
//	define('SURVEY_DIR', ROOT_PATH . '../surveys/');
	define('SURVEY_DIR', '/var/www/surveys/');
	define('TEMPLATE_DIR', ROOT_PATH . 'templates/');

	define('MAIN_SCRIPT', '/browser/index.php');
	
	define('DBHOST','localhost');
	define('DBNAME','emotethis');
	define('DBUSER','root');
	define('DBPASS','');
	
	
	define('USE_HOMIE_DB', true);
	define('HOMIE_DBHOST','127.0.0.1:3310');
	define('HOMIE_DBNAME','emotethis');
	define('HOMIE_DBUSER','root');
	define('HOMIE_DBPASS','');
?>
