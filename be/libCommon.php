<?php

	$dir=dirname(__FILE__);
	require_once($dir.'/../config/config.php');
	require_once($dir.'/config/control_classes.php');
	require_once($dir.'/../lib/be/users.php');
	define("isPostback",$_SERVER['REQUEST_METHOD']=='POST');

	session_start();
//	if(!Users::getUserId()) {
//		header("Location: /be/");
//		exit;
//	}
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Cache-Control: private");
    header('Content-type: text/html; charset=UTF-8');	
    	
//	include($dir.'/../lib/be/phpHeaders.php');	//includes all in __autoload
	require_once($dir.'/../lib/db.php');	//getdb() ->required 
	require_once($dir.'/../lib/be/ConNew.php');	//getdb() ->required 
	require_once($dir.'/../lib/be/ControlsExNew.php');	
	require_once($dir.'/../lib/be/CSearch.php');	
	require_once($dir.'/../lib/search_utils.php');	
	require_once($dir.'/../lib/be/tree.php');	
	require_once($dir.'/../lib/ErrorHandling.php');	
	
	pPrado::beginRequest();
	
	
	//$error_handler=ErrorsManager::getInstance();
	
	
	$REQUEST_METHOD=$_SERVER['REQUEST_METHOD'];
	
	function __autoload($funcName) {
		
		if(!$funcName)
			return;
		$f=array(	
			"IndexTemplate"=>"/be/common/template_index.php",		
			'CRelations'=>'/lib/be/CRelations.php',
			'CLib'=>'/lib/be/lib1.php',
		
			'BE_Utils'=>'/lib/be/fe_utils.php',
			'CTab'=>'/lib/be/CTabControl.php',
			'FE_Utils'=>'/lib/be/fe_utils.php',
			'DB_Utils'=>'/lib/be/fe_utils.php',
			'pPrado'=>'/lib/be/fe_utils.php',
			'Users'=>'/lib/be/users.php',
		
			'COrder'=>"/lib/be/order.php",			
		);
		if(is_file(dirname(__FILE__).'/..'.$f[$funcName])) {		
			require_once(dirname(__FILE__).'/..'.$f[$funcName]);
		}
		else {
			echo "<br />".dirname(__FILE__).'/..'.$f[$funcName]." not found<br />";
		}
		
	}
	
	
	
	function setLastSiteUpdate(){
		getdb()->Execute("UPDATE php_data SET data= DATE_FORMAT(NOW(), '%d-%m-%y') WHERE id='last_updated'");
	}
	
?>