<?php
ob_start();
require_once('../libCommon.php');

$in_edit_table="test";
$in_edit_id=(int)$_GET['id'];

$in_skip_relations=array($in_edit_table);

?>
<!DOCTYPE html
PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8">
<script src='/js/UT.js'></script>
<script src='/js/lib.js'></script>
<link rel="stylesheet" href="/be/lib.css">

<script src='/js/Calendar/calendar.js'></script>
<script src='/js/Calendar/calendar-setup.js'></script>
<script src='/js/Calendar/lang/calendar-en.js'></script>
<link rel="stylesheet" href="/js/Calendar/calendar-win2k-cold-1.css">
</head>
<body >
<form id='f1' method=POST>
<table class="main_table" align="center">
<tr>
<td>

<?php

include(dirname(__FILE__).'/controls.php');
$con=gettestControls();

$errors=array();

$errors=array();
if(isset($_POST['btSave'])) {

	$wd=ControlValues::getWriteData($con,$_POST);	
	$db=getdb();
	if (empty($wd['errors'])) {
/*
	$order=$wd['data']['order_field'];
	unset($wd['data']['order_field']);
	if(!$in_edit_id) {
		$order=(int)$order;
	}
*/
/*
			if(!$in_edit_id) {
				$wd['data']['created_date']=date('Y-m-d H:i:s');
				$wd['data']['created_by']=Users::getUserId();
			}
			$wd['data']['updated_date']=date('Y-m-d H:i:s');
			$wd['data']['updated_by']=Users::getUserId();
*/
			$n_id=ControlWriter::Write($in_edit_table,$wd['data'],(int)$_GET['id']);
//			$new_relation=CRelations::processRelationSave($_GET,$n_id);
//			if($new_relation===false) {
/*
				if((string)$order!="") {
	$co=new COrder($in_edit_table,"order_field");
	$co->set_item_order($n_id,$order);
}

*/

//$errors+=ControlValues::processManagedImages($n_id,$_FILES,$con['controls']);
				header("Location: ?id={$n_id}&msg=1&bkp=".urlencode($_GET["bkp"]));
				//header("Location: ".($_GET['bkp']));
				exit;		
//			}
//			if(is_array($new_relation)) {
//				$errors=$new_relation;
//			}
//			else {
//				header("Location: ".($_GET['bkp']));
//				exit;
//			}	
	}
	else {
		$errors=$wd['errors'];
	}
}

//$p=new Page();



if ($_SERVER['REQUEST_METHOD']=='GET') {
	if (isset($_GET['id'])) {
		$db=getdb();
		$array['in_data']=$db->getRow("select * from {$in_edit_table} where id='{$_GET['id']}'");
	}	
}
else {
	$array=$_POST;
}
FE_Utils::getGetMessage($errors);
if (!empty($errors)) {
	echo FE_Utils::renderErrors($errors);
	echo "<br />";
}

echo "<!--   START    -->";
//MasterForm::create($con,$_POST,$p,$array);
echo Master::create($con,$con['template']['dir'],$array);

//echo $hhh= $p->render();

//if($in_edit_id) {	
//	include(dirname(__FILE__).'/../_relations/list.php');		
//	include(dirname(__FILE__).'/../_relations/index.php');
//}

$__render_grid_only=true;

$tab=new CTab("test");
ob_start();
include(dirname(__FILE__)."/index.php");
$tab->add_tab("Test",ob_get_clean());
$tab->add_tab("Test1",<<<EOD
<p>dfgdfgdfg</p>
<p>dfgdfgdfg</p>
<p>dfgdfgdfg</p>
<p>dfgdfgdfg</p>
EOD
);

echo $tab->render();

echo "<!--   END    -->";
echo "</td></tr></table>";
echo "</form></body></html>";

ob_end_flush();
?>