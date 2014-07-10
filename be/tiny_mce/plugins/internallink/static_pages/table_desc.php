<?php

$ta_xml=array(
'columns'=>array(
	'header'=>'',
	
),
/*'excel_options'=>array(
	'skip_columns'=>array("id","_h_id"),
	'add_index'=>true,
),
'hasExcelExport'=>true,*/
//'OnItemDataBound'=>'chCap',
//'OnOrderChange'=>'fn_order_change',
//'OnBeforeItemDataBound'=>"loadRowData",
'page_size'=>25,
'DataTable'=>array(
	'table'=>'categories left outer join static_pages on categories.id=static_pages.cid',
	'fields'=>"categories.id,'sp' as table_name,categories.level,static_pages.def as def,static_pages.title as st,static_pages.id as spid, categories.value,categories.id as cid",
	'order_fields'=>"categories.l,ifnull(def,0) desc, ifnull(static_pages.id,0)",
	'where'=>"categories.id>1 and categories.level>0",
	),
);
$ta_xml['template']=(dirname(__FILE__). '/table.tpl');


if(isset($__custom_where)) {
	
	$ta_xml['DataTable']['where']=$__custom_where;
}
?>