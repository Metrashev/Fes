<?php
function getboxesControls($type='edit') {
	$con=array(

'controls'=>array(
'title'=>array(
	'control'=>array("Label"=>"Заглавие","name"=>"in_data[title]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"title","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_VARCHAR,"required"=>false,),
),

'body'=>array(
	'control'=>array("Label"=>"Текст","name"=>"in_data[body]","isHTML"=>false,"tagName"=>"TextArea","bound_field"=>"body","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_TEXT,"required"=>false,),
),

'link'=>array(
	'control'=>array("Label"=>"Url","name"=>"in_data[link]","isHTML"=>false,"tagName"=>"Input","bound_field"=>"link","userFunc"=>"","FormatString"=>""),
	'write_data'=>array("type"=>DATA_VARCHAR,"required"=>false,),
),
)
);

if($type=='search') {
    	$con['template']=array('dir'=>dirname(__FILE__).'/search.tpl');
    }
else {
	$con['template']=array('dir'=>dirname(__FILE__).'/edit.tpl');
}
    return $con;
}

?>