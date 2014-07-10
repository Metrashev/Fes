<?php
require_once(dirname(__FILE__)."/conf.php");
$tmp_name=".tmp";

$dir=dirname(__FILE__)."/../../../..";
echo file_get_contents($dir.$GLOBALS['FMAN_IMAGES_URL_PATH']."/{$tmp_name}");

?>