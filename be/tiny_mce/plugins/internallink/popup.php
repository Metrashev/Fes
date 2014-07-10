<?php

if(get_magic_quotes_gpc()||get_magic_quotes_runtime())
{
	if(isset($_GET))
		$_GET=array_map(array('CRemSlashes','_StripSlashes'),$_GET);
	if(isset($_POST))
		$_POST=array_map(array('CRemSlashes','_StripSlashes'),$_POST);
	if(isset($_REQUEST))
		$_REQUEST=array_map(array('CRemSlashes','_StripSlashes'),$_REQUEST);
	if(isset($_COOKIE))
		$_COOKIE=array_map(array('CRemSlashes','_StripSlashes'),$_COOKIE);
}

class CRemSlashes {
	static function _StripSlashes(&$data)
	{
		return is_array($data)?array_map(array('CRemSlashes','_StripSlashes'),$data):stripslashes($data);
	}
}

$pages=array(
	1=>"static_pages",
	2=>"news",
);
if(isset($_POST['type'])) {
	$type=(int)$_POST['type'];
}

if(!$type) {
	$type=1;
}



$GLOBALS['__selector']=$selector=$_GET['selector'];
$force_search=true;

require_once(dirname(__FILE__).'/../../../common/template_index.php');
$__template_index=new IndexTemplate(0);
$__template_index->hidden['type']="<input type='hidden' name='type' value='{$_POST['type']}'>";

include(dirname(__FILE__)."/{$pages[$type]}/index.php");
return;

$i_dir=realpath(dirname(__FILE__).'/../../../../lib/');
ob_start();
include(dirname(__FILE__).'/../../../libCommon.php');

$selected_table=(int)$_POST['s_table'];
if(!$selected_table) {
	$selected_table=1;
}
$str_order='';
switch ($selected_table) {
	case 1:	{	//static pages
		$ta_xml=array(
		'columns'=>array(
			'header'=>'',
			
		),
		//'OnItemDataBound'=>'chCap',
		//'OnOrderChange'=>'fn_order_change',
		'page_size'=>25,
		/*'hasExcelExport'=>true,*/
		'DataTable'=>array(
			'table'=>'categories left outer join static_pages on categories.id=static_pages.cid',
			'fields'=>"categories.id,'sp' as table_name,categories.level,static_pages.def as def,static_pages.title as st,static_pages.id as spid, categories.value,categories.id as cid",
			//'order_fields'=>"categories.l,ifnull(def,0) desc, ifnull(static_pages.id,0)",
			'order_fields'=>"",
			'where'=>'categories.id>1 and categories.level>0',
			),
		);
		$str_order="categories.l,ifnull(def,0) desc, ifnull(static_pages.id,0)";
		break;
	}
	case 2: {	// news
		$ta_xml=array(
		'columns'=>array(
			'header'=>'',
			
		),
		//'OnItemDataBound'=>'chCap',
		//'OnOrderChange'=>'fn_order_change',
		'page_size'=>25,
		/*'hasExcelExport'=>true,*/
		'DataTable'=>array(
			'table'=>'categories inner join news_pages on categories.id=news_pages.cid',
			'fields'=>"categories.id,'news' as table_name,categories.level,0 as def,news_pages.title as st,news_pages.id as spid, categories.value,categories.id as cid",
			//'order_fields'=>"categories.l,ifnull(def,0) desc, ifnull(static_pages.id,0)",
			'order_fields'=>"",
			'where'=>'categories.id>1 and categories.level>0',
			),
		);
		$str_order="categories.l,due_date desc, ifnull(news_pages.id,0)";
		break;	
	}
}

$ta_xml['columns']=xmlPatcher::createArray((dirname(__FILE__). '/table.tpl'));

function popUp_getSelectField($value,$index,$row,$dataItem) {
	$cid=$row['cid'];
	$spid=$row['spid'];
	if((int)$spid>0&&(int)($row['def'])==0) {
		$title = $row['st'];
	} else {
		 $title = $row['value'];
	}
	$table_id=$row['table_name']=='sp'?'spid':'id';
	$spid=empty($spid)?'':'&'.$table_id.'='.$spid;
	$title = str_replace('"', "&amp;quot;", $title);
	$onClick = "{$GLOBALS['__selector']} \"/?cid={$cid}{$spid}\", \"$title\"); window.close();";
		
		//$onClick = str_replace("&", "&amp;", $onClick);
	$onClick = str_replace('"', "&quot;", $onClick);
	return "<a href=\"#\" onclick=\"$onClick\">".CLanguage::translate(DEFAULT_LANGUAGE,'Select')."</a>";
}

function popUp_getTitleField($value,$index,$row,$dataItem) {
	if(intval($row['level'])==0) {
	    return $row['value'];
	}
	$value=str_repeat('&nbsp;', 2*($row['level']-1)).$row['value'];
	if(intval($row['spid'])>0&&intval($row['def'])==0) {
		$value=str_repeat('&nbsp;', 2*$row['level']).$row['st'];
	}
	return $value;
}

$search=array(

'controls'=>array(
	'cid'=>
		array(
			'control'=>array('Label'=>CLanguage::translate(DEFAULT_LANGUAGE,"Categories"),'name'=>'in_data[cid]','tagName'=>'Select','bound_field'=>'cid','userFunc'=>'','FormatString'=>'','autoload'=>array('type'=>'userfunc','value'=>array('DataSource'=>array('UT_userfunctions','getMenuDropDown'),'func_params'=>'&nbsp;'),)),
			'search_data'=>array('search_name'=>'categories.id','cond'=>'=','matchAllValue'=>'0')
		),
	'keywords'=>
		array(
			'control'=>array('Label'=>CLanguage::translate(DEFAULT_LANGUAGE,"Keywords"),'name'=>'in_data[keywords]','tagName'=>'Input','bound_field'=>'keywords','userFunc'=>'','FormatString'=>'',),
			'search_data'=>array('search_name'=>'static_pages.title,static_pages.body','cond'=>'keywords','matchAllValue'=>'')
		),
),
'template_title'=>'',	//zadava header na tablicata
'template_colums'=>2,	//broi koloni v koito stroi tablicata
'template_type'=>'search',		//posible values:
'clear_fields'=>array('in_data'),	
);

if(isset($_POST['btClear'])) {
	unset($_POST['in_data']);
}

if(!isset($_POST['n_cid'])) {
	$_POST['n_cid']=$_GET['n_cid'];
}

//$builder=createSearch();	/* @var $builder SearchBuilder*/
if(isset($_POST['is_common']))
	$_SESSION['use_cid']=$_POST['n_cid'];// $_SESSION['static_page_filter']['id'];
else 
	unset($_SESSION['use_cid']);

if($_SERVER['REQUEST_METHOD']=='GET'&&isset($_SESSION['static_page_filter'][$_POST['n_cid']])&&$_POST['ch_b']!=1) {
	$_POST['__c_menu_page']=$_SESSION['static_page_filter'][$_POST['n_cid']]['page'];
}
else {
	$_SESSION['static_page_filter'][$_POST['n_cid']]['page']=$_POST['__c_menu_page'];
}
if($_POST['__c_menu_eventtarget']=='goto_page') {
	$_POST['__c_menu_page']=$_POST['__c_menu_eventargument'];
	$_SESSION['static_page_filter'][$_POST['n_cid']]['page']=$_POST['__c_menu_page'];
}
//$dataSource=BE_Utils::fillSQLStruct('listview_menu');
//$tableView=new SingleSelectTableView('__c_menu','listview_menu',$dataSource,$_POST,'select','categories');

if($_POST['btSearch']||intval($_POST['use_search'])==1) {
//	$tableView->setWhere($builder->getFilter($_POST));
	$dg=FE_Utils::prepareDataGrid($ta_xml,$search,$_POST,true);
	$pb=$dg['pb'];
	$dg=$dg['dg'];
	$_SESSION['static_page_filter'][$_POST['n_cid']]['id']=$_POST['id'];
	$_SESSION['static_page_filter'][$_POST['n_cid']]['keywords']=$_POST['keywords'];
	$_SESSION['static_page_filter'][$_POST['n_cid']]['page']=$_POST['__c_menu_page'];
	$_POST['use_search']=1;
}
else {
	if($_SERVER['REQUEST_METHOD']=='GET'&&isset($_SESSION['static_page_filter'][$_POST['n_cid']])&&$_POST['ch_b']!=1) {
		$_POST['id']=$_SESSION['static_page_filter'][$_POST['n_cid']]['id'];
		$_POST['keywords']=$_SESSION['static_page_filter'][$_POST['n_cid']]['keywords'];
		$_POST['__c_menu_page']=$_SESSION['static_page_filter'][$_POST['n_cid']]['page'];
		$_POST['use_search']=1;
		//$tableView->setWhere($builder->getFilter($_POST));
		$dg=FE_Utils::prepareDataGrid($ta_xml,$search,$_POST,true);
		$pb=$dg['pb'];
		$dg=$dg['dg'];
	}
}
$dg->DataSource->OrderFields=array(''=>$str_order);
//$dg->DataSource->OrderFields=array('categories.l,ifnull(def,0) desc, ifnull(static_pages.id,0)'=>'');

//$tableView->setExtraWhere('categories.id>1 and categories.level>0');

$GLOBALS['__selector']=$selector=$_GET['selector'];

$tables=array(1=>CLanguage::translate(DEFAULT_LANGUAGE,'Static pages'),2=>CLanguage::translate(DEFAULT_LANGUAGE,'News'));

?>

<html>
<head>
<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8">
<script src='/UT.js'></script>
<script src='/lib.js'></script>


<link rel="stylesheet" href="/be/lib.css">


</head>

<body>
<center>
<br>
<form id='f1' method=POST>
<input type="hidden" name="id" value="<?= $id;?>">
<input type="hidden" name="n_cid" value="<?= $_POST['n_cid'];?>">
<input type="hidden" name="ch_b" id="ch_b" value="">
<input type="hidden" name="use_search" id="use_search" value="<?=$_POST['use_search'];?>"/>
<table width="700px">
<?php
if(intval($_GET['n_cid'])>0) {
	$chk=isset($_POST['is_common'])?'checked':'';
	echo <<<EOD
<tr><td align="center"><input type="checkbox" name="is_common" id="is_common" {$chk} onclick="document.getElementById('ch_b').value=1;getParentFormElement(this).submit();"/><label for="is_common">Use as common document</label></td></tr>
EOD;
}
?>
	<tr>
		<td><select name="s_table" onchange="getParentFormElement(this).submit();"><?=CLib::draw_listbox_options($tables,$_POST['s_table']);?></select> </td>
	</tr>
<tr>
<td>
<? 
$p=new Page();
MasterForm::create($search,$_POST,$p,array());
echo $p->render();
//$builder->Build($_POST);echo $builder->getHTML();?>
</td>
</tr>

<tr>
<td>
<?=$dg->render();?>
</td>
</tr>
</table>

<?php
ob_end_flush();
?>