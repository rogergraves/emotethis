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
	
//	ssh -L 3310:127.0.0.1:3306 root@portal.rubyriders.com
//	autossh -i /root/.ssh/id_rsa -L 3310:127.0.0.1:3306 root@184.106.92.80
	define('USE_HOMIE_DB', true);
	define('HOMIE_DBHOST','127.0.0.1:3310');
	define('HOMIE_DBNAME','emotethis');
	define('HOMIE_DBUSER','root');
	define('HOMIE_DBPASS','');
?>
