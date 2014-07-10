<?php
ob_start();
require_once('../libCommon.php');
//Users::checkUserRights('Users');


require_once(dirname(__FILE__)."/../common/datagrid_controler.php");
require_once(dirname(__FILE__)."/../common/search_controler.php");
require_once(dirname(__FILE__)."/../common/mvc.php");

$filter_session_name="filter_test";
$__del_var="hdDeletetest";
$__editTable="test";
$fn_Delete="";

$mvc=new MVC($__editTable,$filter_session_name,"",$__del_var);
$mvc->workpath=dirname(__FILE__);

$mvc->is_multiform=isset($__render_grid_only);

$mvc->has_data_grid=true;

$mvc->autoprepare();

$mvc->processDelete();
$mvc->processSelect();

echo $mvc->render(isset($__skip_back_button));

return;

/*
if(isset($_POST['ajax_params'])) {
	require_once(dirname(__FILE__).'/../common/ajax_params.php');
}

include(dirname(__FILE__).'/table_desc.php');

include(dirname(__FILE__).'/controls.php');

if(isset($in_edit_id)&&(int)$in_edit_id) {
	$search=array();
}
else {
	$filter_session_name="filter_test";
	$search=gettestControls('search');
}
$__del_var="hdDeletetest";
$__editTable="test";
$fn_Delete="";
$fn_Delete="del_test";

function del_test($del_id) {
	$db=getdb();
		
	$db->execute("delete from `test` where id='{$del_id}'");
}
include(dirname(__FILE__).'/../common/index.php');

ob_end_flush();
*/
?>