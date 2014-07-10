<?php
ob_start();
include('../../../libCommon.php');

class CTM_templates {
	public $files;
	public $tdir=TINYMCE_TEMPLATES;
	public $dir;
	public $xml_array;
	public $rootControl=null;
	public $sxe;
	
	function __construct() {
		$this->dir=dirname(__FILE__).'/'.TINYMCE_TEMPLATES;
		$this->dir=realpath($this->dir);
		$this->files=self::getFiles($this->dir);
	}

	function getFiles($dir) {
		$result=array();
		$it=new RecursiveDirectoryIterator($dir);
		for( ; $it->valid(); $it->next()) {
			if($it->isDir()) {
				continue;
			} elseif($it->isFile()) {
				$fn=$it->getFilename();
				$fi=pathinfo($fn);
				$ext=strtolower($fi['extension']);
				if($ext=='png') {
					$fr=str_replace($ext,"html",$fn);
					if(file_exists($dir.'/'.$fr)) {
						$result['pic'][]=$fn;
						$result['html'][]=$fr;
					}
				}
							
			}
		}
		return $result;
		
	}
	
	/*function parseNode($node) {
		$str=array();
		if($node)
		foreach ($node as $k=>$v) {
			$str[]=(string)$k;
			$str=array_merge($str,parseNode($v));
		}
		return $str;
	}
	
	function parseXml($xml,$rows,$cols) {
		$sxe=simplexml_load_string($xml);
		foreach ($sxe->main as $v) {
			$arr=parseNode($v);
		}
	}*/
	
	function getSimpleGrid() {
		$rel_path=dirname($_SERVER['SCRIPT_NAME']);
		$arr=array();
		$selector=$_GET['selector'];
		$r_arr=array(
		'&'=>'&amp;',
		'<'=>'&lt;',
		'>'=>'&gt;',
		'"'=>'&quot;',
		"\r\n"=>'',
		);
		
		for($i=0;$i<count($this->files['pic']);$i++) {
			$index=$i+1;
			$html=file_get_contents($this->dir.'/'.$this->files['html'][$i]);
			//parseXml($html,0,0);	
			$html=str_replace(array_keys($r_arr),$r_arr,$html);
			//$html=str_replace("\r\n","dfgdfgdfg",$html);
	//		CXmlParser::loadXML($dir.'/'.$files['html'][0]);
			$arr[]=<<<EOD
			<a href='#' onclick="insertH(&quot;{$html}&quot;)"><img src='{$rel_path}/{$this->tdir}/{$this->files['pic'][$i]}' width='120' height='120' /></a>
EOD;
//
//			$arr[]=<<<EOD
//			<a href='#' onclick="document.getElementById('ht_selection').value={$index};getParentFormElement(this).submit()"><img src='{$rel_path}/{$this->tdir}/{$this->files['pic'][$i]}' width='120' height='120' /></a>
//EOD;
		}
		
		return CLib::draw_arrayToTable($arr);
	}
	
	function getGrid() {
		$rel_path=dirname($_SERVER['SCRIPT_NAME']);
		$arr=array();
		$selector=$_GET['selector'];
		$r_arr=array(
		'&'=>'&amp;',
		'<'=>'&lt;',
		'>'=>'&gt;',
		'"'=>'&quot;',
		);
		
		for($i=0;$i<count($this->files['pic']);$i++) {
			$index=$i+1;
	//		$html=file_get_contents($dir.'/'.$files['html'][$i]);
	//		//parseXml($html,0,0);	
	//		$html=str_replace(array_keys($r_arr),$r_arr,$html);
	//		CXmlParser::loadXML($dir.'/'.$files['html'][0]);
	//		$arr[]=<<<EOD
	//		<a href='#' onclick="insertHTML(&quot;{$html}&quot;)"><img src='{$rel_path}/{$tdir}/{$files['pic'][$i]}' width='120' height='120' /></a>
//EOD;
			$arr[]=<<<EOD
			<a href='#' onclick="document.getElementById('ht_selection').value={$index};getParentFormElement(this).submit()"><img src='{$rel_path}/{$this->tdir}/{$this->files['pic'][$i]}' width='120' height='120' /></a>
EOD;
		}
		
		return CLib::draw_arrayToTable($arr);
		
	}
	
	function getRow($arr,$pref,$is_val=false) {
		if(!is_array($arr['attributes'])||!count($arr['attributes']))
			return '';
		if(!$is_val) {
			$str="<tr><td width=100%><table width=100% cellpadding=0 cellspacing=0><tr>";
			$str1="<tr>";
			if(isset($arr['attributes']['label'])) {
				$str="<tr><td align='center' colspan='".(count($arr['attributes'])-1)."'><b>{$arr['attributes']['label']}</b></td></tr>".$str;
				unset($arr['attributes']['label']);
			}
		}
		else {
			$str=array();
		}
		
		foreach ($arr['attributes'] as $k=>$v) {
			$val=htmlspecialchars($_POST[$pref.'_'.$k]);
			if($is_val) {
				$str[$k]=$val;
			}
			else {
				$str.=<<<EOD
				<td><label for="{$pref}_{$k}">{$v}</label></td>
EOD;
				$str1.=<<<EOD
				<td><input type='text' id='{$pref}_{$k}' name='{$pref}_$k' value="{$val}" /></td>
EOD;
			}
		}
		if($is_val)
			return $str;
		$str.="</tr>";
		$str1.="</tr>";
		return $str.$str1."</table></td></tr>";
	}
	
	function getControl($v,&$controls,$is_val=false) {
		$rows=array();
		if(is_array($v['row'])&&count($v['row'])) {
			foreach ($v['row'] as $rk=>$rv) {
				if(!$this->rootControl)
					$this->rootControl=(string)$rk;
				$controls[]=$rk;
				foreach ($rv as $rvk=>$rvv) {
					$r=$this->getRow($rvv,$rk.$rvk,$is_val);
					if(!empty($r))
						$rows[]=$r;
					$r1=$this->getControl($rvv,$controls,$is_val);
					if(!empty($r1))
						$rows=array_merge($rows,$r1);
				}
			//	foreach ($rv )
			}
		}
		return $rows;
	}
	
	function processNode($rvv) {
		if(is_array($rvv['attributes']&&count($rvv['attributes']))) {
			$s=array();
			echo "<pre>";
			print_r($rvv);
			echo "</pre>";
			foreach ($rvv['attributes'] as $name=>$value) {
						
			}
		}
	}
	
	function getControlArray($v,$replace_array) {
		$rows=array();
		if(is_array($v['row'])&&count($v['row'])) {
			foreach ($v['row'] as $rk=>$rv) {
				foreach ($rv as $rvk=>$rvv) {
					$repeater=1;
					if($rk=='repeat') {
						if(isset($replace_array[$rvv['attributes']['count']]))
							$repeater=(int)$replace_array[$rvv['attributes']['count']];
							if(empty($repeater))
								$repeater=1;
					}
					else {
						if(is_array($rvv['attributes']&&count($rvv['attributes']))) {
							$s=array();
							foreach ($rvv['attributes'] as $name=>$value) {
										
							}
						}
					}
					for($i=0;$i<$repeater;$i++) {
						
					}
				}
			//	foreach ($rv )
			}
		}
		return $rows;
	}
	
	function renderTemplate($index,$is_val=false,$root='main') {
		if($this->xml_array) {
			$arr=$this->xml_array;
		}
		else {
			$arr=CXmlParser::loadXML($this->dir.'/'.$this->files['html'][$index]);
			$this->xml_array=$arr;
		}
		$str='';
		$controls=array();
		$rows=array();
		foreach ($arr[$root] as $k=>$v) {
			if(is_array($v['row'])&&count($v['row'])) {
				$rows=$this->getControl($v,$controls,$is_val);
			}
		}
		if($is_val)
			return $rows;
		return empty($rows)?'':"<table width='100%' border='1'>".implode("",$rows)."</table><div align='right'><input type='submit' name='btInsert' value='Ok'/> <input type='button' onclick='window.close();' value='Cancel'/>";
	}
	
	function getHtml($index) {
		$result=$this->renderTemplate($index,true);
		$arr=array();
		if(is_array($result)&&count($result)) {
			$arr=$result[0];
			for($i=1;$i<count($result);$i++) {
				$arr=array_merge($arr,$result[$i]);
			}
		}
	//	echo "<pre>";
	//	print_r($this->xml_array);
	//	echo "</pre>";
		foreach ($this->xml_array[$this->rootControl] as $k=>$v) {
		}
	}

}

$tm=new CTM_templates();

$selector=$_GET['selector'];

//$index=(int)$_POST['ht_selection'];

//if (isset($_POST['btInsert'])) {
//	$tm->getHtml($index-1);
//}


?>
<html>
<head>
<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8">
<link rel="stylesheet" href="/lib.css">
<script src="/lib.js"></script>
<?php
if($selector) {
	echo <<<EOD
<script language='javascript'>
function insertH(html) {

	if (html == null)
		html = "";
	{$selector}(html);
	window.close();
}
</script>
EOD;
}
?>
</head>

<body>
<center>
<br>
<form id='f1' method=POST>
<input type='hidden' name='ht_selection' id='ht_selection' value='<?=$index;?>' />
<?php

//if($index) {
//	echo $tm->renderTemplate($index-1);
//}

echo $tm->getSimpleGrid();

ob_end_flush();
?>