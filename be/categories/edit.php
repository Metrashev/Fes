<?php
ob_start();
require_once('../libCommon.php');

$id = (int)$_REQUEST['id'];
if($id<1) die("Invalid ID!");
//echo "ID=".$id;
?>
<!DOCTYPE html
PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8">
<script src='/js/lib.js'></script>
<?=JS_VALIDATION?"<script src='/js/js_validator.js'></script>":'';?>
<link rel="stylesheet" href="/be/lib.css">

<script>
function confDelete() {
    return true;
}
</script>

</head>

<body >
<form id='f1' method=POST enctype="multipart/form-data">
<table class="main_table" align="center">
<tr>
<td>

<?php

if ($_SERVER['REQUEST_METHOD']=='GET') {
    if (isset($_GET['id'])) {
        $db=getdb();
        $array['in_data']=$db->getRow("select * from categories where id='{$_GET['id']}'");
        @$data=unserialize($array['in_data']['php_data']);
        $data=isset($data['parameters'])?$data['parameters']:array();
    }    
}
else {
    $array=$_POST;
    $data=$_POST['data'];
}


$skins= array(0=>'');
foreach ($GLOBALS['CONFIG']['Skins'] as $k=>$v) {
	$skins[$k]=$v['name'];
}



$type_id=(int)$array['in_data']['type_id'];
$class=$GLOBALS['CONFIG']['FEPageTypes'][$type_id]['class'];
if(!empty($class)) {
    $templates=isset($GLOBALS['CONFIG'][$class]['templates'])?$GLOBALS['CONFIG'][$class]['templates']:array();
}

if(is_array($GLOBALS['CONFIG'][$class]['be']['delete'])) {
    if(isset($GLOBALS['CONFIG'][$class]['be']['delete']['file'])) {
        require_once(dirname(__FILE__).'/../../'.$GLOBALS['CONFIG'][$class]['be']['delete']['file']);
    }
    if(!empty($GLOBALS['CONFIG'][$class]['be']['delete']['functions']['getMessage'])) {
        $GLOBALS['delete_message']=call_user_func($GLOBALS['CONFIG'][$class]['be']['delete']['functions']['getMessage'],$id);
        if(empty($GLOBALS['delete_message'])) {
        	$GLOBALS['delete_message']="Are you sure?";
        }
    }
    else {
       $GLOBALS['delete_message']="Are you sure?";
    }
    if(!empty($GLOBALS['delete_message'])) {
        $GLOBALS['delete_message']="onclick='return window.confirm(\"{$GLOBALS['delete_message']}\");' ";
    }
}
else {
    $GLOBALS['delete_message']="onclick='return window.confirm(\"Are you sure?\");' ";
}

include(dirname(__FILE__)."/controls.php");

if( isset($_REQUEST['btDelete']) )
{
	$db=getdb();
	if(((int)$db->getone("select not_deletable from categories where id='{$id}'"))===0) {
	    if(!empty($GLOBALS['CONFIG'][$class]['be']['delete']['functions']['process_delete'])) {
	        $GLOBALS['delete_message']=call_user_func($GLOBALS['CONFIG'][$class]['be']['delete']['functions']['process_delete'],$id);
	    }
	    $Tree = new CURLTree("categories");
	    
		
		$SQL = "SELECT l, weight FROM categories WHERE id='$id'";
		$row = $db->getRow($SQL);
	
		
	    $l = (int)$row["l"];
	    $weight = (int)$row["weight"];
	    $right = $l + $weight;
		$SQL = "SELECT id FROM categories WHERE (l BETWEEN {$l} AND {$right})";
	  	$delete_ids = $db->getCol($SQL);
	  	
	  	foreach ($delete_ids as $del_id) {      
			ControlValues::deleteManagedImages($del_id,$con['controls'],false);			
	  	}
	    $Tree->delete_node($id);
	    header("Location: index.php");
	    exit;
	}
	else {
		echo "Cannot be deleted";
		echo "<br /><br />";
	}
}

function getParentLanguage($id) {
    $db=getdb();
    $row=$db->getrow("select pid,language_id from categories where id='{$id}'");
    while(empty($row['language_id'])&&$row['pid']) {
        $row=$db->getrow("select pid,language_id from categories where id='{$row['pid']}'");
        
    }
    return $row['language_id'];
}

function updateLanguage($id,$language_id) {
    $db=getdb();
    $lr=$db->getrow("select l,weight,language_id from categories where id='{$id}'");
    $old_lng=$lr['language_id'];
    if($old_lng==$language_id)
        return;
    $l=(int)$lr['l'];
    $r=$l+(int)$lr['weight'];
    $db->Execute("update categories set language_id='{$language_id}' where l>'{$l}' AND l<='{$r}'")  ;
}


$errors=array();
if(isset($_POST['btSave'])) {
    if(!isset($_POST['in_data']['language_id'])) {
        $_POST['in_data']['language_id']=getParentLanguage($id);
    }
    else {
        updateLanguage($id,$_POST['in_data']['language_id']) ;
    }
    $wd=ControlValues::getWriteData($con,$_POST);    
    if (empty($wd['errors'])) {
    	if(isset($GLOBALS['CONFIG'][$class]['be']['tree'])) {
     //   if(isset($_POST['data'])) {
            $db=getdb();
			if(!is_array($_POST['data'])) {
				$_POST['data']=array();
			}
            $old_data=$db->getone("select php_data from categories where id='{$id}'");
            @$old_data=unserialize($old_data);
            
            $old_data['parameters']=$_POST['data'];
        
            $wd['data']['php_data']=serialize($old_data);
        }
        
        $id=ControlWriter::Write('categories',$wd['data'],$id);
        $errors+=ControlValues::processManagedImages($id,$_FILES,$con['controls']);
        if(empty($errors)) {
	        header("Location: index.php?node={$id}");
	        exit;
        }
    }
    else {
        $errors=$wd['errors'];
    }
}






if(isset($GLOBALS['CONFIG'][$class]['be']['tree'])) {
    ob_start();
    
    if(is_array($GLOBALS['CONFIG'][$class]['be']['tree'])) {
    	$template_id=(int)$array['in_data']['template_id'];
    	if(isset($GLOBALS['CONFIG'][$class]['be']['tree'][$template_id])) {
    		include(dirname(__FILE__).'/../../'.$GLOBALS['CONFIG'][$class]['be']['tree'][$template_id]);
    	}
    }
    else {
    	include(dirname(__FILE__).'/../../'.$GLOBALS['CONFIG'][$class]['be']['tree']);
    }
    $GLOBALS['tree_include_file']=ob_get_clean();
}

$GLOBALS['show_language_field']=$array['in_data']['level']==1;


echo "<!--   START    -->";

if (!empty($errors)) {
    echo FE_Utils::renderErrors($errors);
    echo "<br />";
}
//$dg->renderEvents();
echo Master::create($con,dirname(__FILE__).'/edit.tpl',$array);

echo "<!--   END    -->";

echo "</form></body></html>";

ob_end_flush();
?>