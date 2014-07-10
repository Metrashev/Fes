<?php


function ob_include($file, $data=array()){
  ob_start();
  include($file);
  return ob_get_clean();
}

/**
 * Encapsulated include
 *
 * @param string $file
 */
function enc_include($file){
	include($file);
}

/**
 * Encapsulated require_once
 *
 * @param unknown_type $file
 */
function enc_require_once($file){
	require_once($file);
}


function __autoload($funcName) {
	//require_once(dirname(__FILE__).'/'.$GLOBALS['CONFIG']['AutoLoad'][$funcName]);
	require_once($GLOBALS['CONFIG']['AutoLoad'][$funcName]);
}

function stripSlashesRecurive($data)
{
	return is_array($data)?array_map('stripSlashesRecurive',$data):stripslashes($data);
}

function beginRequest()
{
  static $inputStripped = false;

	if(!$inputStripped && get_magic_quotes_gpc())
	{
		if(isset($_GET))
			$_GET=stripSlashesRecurive($_GET);
		if(isset($_POST))
			$_POST=stripSlashesRecurive($_POST);
		if(isset($_REQUEST))
			$_REQUEST=stripSlashesRecurive($_REQUEST);
		if(isset($_COOKIE))
			$_COOKIE=stripSlashesRecurive($_COOKIE);
		$inputStripped = true;
	}
}


function getFileExt($name) {
	$p=pathinfo($name);
	return empty($p['extension'])?'':'.'.$p['extension'];
}

/*
convert_1d_to_2d_array - this function converts 1 dimensional array to 2 dimensional array

$in_array - Input 1 dimentional array. assoc arrays are accepted.
$DistributionDirection - Horizontal | Vertical
$Cols - Number of cols in output array.
$Rows - Number of rows in output array.
If $Cols is set $Rows must be 0 and vice versa
*/

function convert_1d_to_2d_array($in_array, $DistributionDirection, $Cols, $Rows=0){

	if(!is_array($in_array)) return false;
	if($Cols + $Rows === 0) return false;

	if($Cols>0){
		$Rows = ceil(count($in_array)/$Cols);
	} else if ($Rows>0) {
		$Cols = ceil(count($in_array)/$Rows);
	}

	$out_array=Array();
/*
	for($row=0; $row<$Rows; $row++) {
		for($col=0; $col<$Cols; $col++) {
			$index = $DistributionDirection=='Horizontal' ? $row*$Cols+$col : $row+$Rows*$col;
			if( isset($in_array[$index]) ){
				$out_array[$row][$col] = $in_array[$index];
			} else {
				$out_array[$row][$col] = "";
			}
		}
	}
*/
	$RowColVar = $DistributionDirection=='Horizontal' ? $Cols : $Rows;
	$index = 0;
	foreach($in_array as $val){
		$row = (int)($index/$RowColVar);
		$col = $index % $RowColVar;
		$out_array[$row][$col] = $val;
		$index++;
	}

	for($index; $index<$Rows*$Cols; $index++){
		$row = (int)($index/$RowColVar);
		$col = $index % $RowColVar;
		$out_array[$row][$col] = "";
	}

	return $out_array;
} // End function convert_1d_to_2d_array


function arrayToTable($table, $tr='<tr>', $td='<td>'){
	$res = '';
	foreach ($table as $row)
		$res .= "$tr$td".implode("</td>$td", $row).'</td></tr>';
	return $res;
}

function arrayToTableStyle($table){
	$res = '';
	$r = 0;
	$TRcnt = count($table);
	$TDcnt = count(reset($table));
	foreach ($table as $row){
		$r++;
		$TRclass = $r % 2 ? 'odd' : 'even';
		if($r==1) $TRclass.=' first';
		if($r==$TRcnt) $TRclass.=' last';
		$TRclass .= ' tr'.$r;
		
		$res .= "<tr class='$TRclass'>\n";
		$c = 0;
		foreach ($row as $cell){
			$c++;
			$TDclass = $c % 2 ? 'odd' : 'even';
			if($c==1) $TDclass.=' first';
			if($c==$TDcnt) $TDclass.=' last';
			$TDclass .= ' td'.$c;
			$res .= "<td class='$TDclass'>$cell</td>\n";
		}
		$res .= "</tr>\n";
	}
	return $res;
}

class PageBar {

  const GETVarName='p';

  function __construct($ItemsPerPage, $ItemsCnt){
    $this->pages = ceil($ItemsCnt/$ItemsPerPage);
    $this->CurrentPage = (int)$_GET[self::GETVarName];
  }

  function getData($href){
    $result = array();
    $result['total'] = $this->pages;
    $result['current'] = $this->CurrentPage;
    $result['prev'] = $href.self::GETVarName.'='.($this->CurrentPage-1);
    $result['next'] = $href.self::GETVarName.'='.($this->CurrentPage+1);

    list($fp, $lp) = self::getPagesRange($this->pages, $this->CurrentPage);
    for($i=$fp; $i<$lp; $i++){
      $result['pages'][$i] = $href."&amp;".self::GETVarName.'='.$i;
    }
    return $result;
  }

  static function getPagesRange($pages, $page, $maxpages = 20){

      $dl = ceil( ($maxpages-1) / 2 );
      $dr = floor( ($maxpages-1) / 2 );

      if($page<=$dl){
        $fp = 1;
        $lp = $maxpages;
      } else if ($page>($pages-$dr)) {
        $fp = $pages-$maxpages+1;
        $lp = $pages;
      } else {
        $fp = $page - $dl;
        $lp = $page + $dr;
      }

      return array($fp, $lp);
  }
}

function is_valid_email_address($email){
	return ereg("^[^@]+@([0-9a-zA-Z][0-9a-zA-Z-]*\.)+[a-zA-Z]{2,4}$", $email);
}


function draw_listbox_options($options, $selected, $no_key=FALSE,$render_extra_attributes=false){
		if(!is_array($options)) return;
		//reset($options);
		$str="";
		$keys=array();
		foreach ($options as $k=>$v) {
			if(is_array($v)) {
				if(empty($keys)) {
					$keys=array_keys($v);
				}
				$values=array_values($v);
				$val=$values[0];
			}
			else {
				$val=$v;
			}
			if($no_key) {
				$k=$v;
			}
			$selected_str="";
			if(is_array($selected)&&in_array("$k",$selected)) {
				$selected_str=" selected=\"selected\" class=\"selected\"";
			}
			else {
				if("$k"=="$selected") {
					$selected_str=" selected=\"selected\" class=\"selected\"";
				}
			}
			$str_extra="";
			if($render_extra_attributes&&is_array($values)) {
				foreach ($keys as $kk=>$vv) {
					if(strtolower($vv)=="value") {
						continue;
					}
					$str_extra.=" {$vv}=\"".htmlspecialchars($values[$kk])."\" ";
				}
			}
			if($no_key) {
				$str.="<option{$selected_str}{$str_extra}>{$val}</option>";
			}
			else {
				$str.="<option{$selected_str}{$str_extra} value=\"".htmlspecialchars($k)."\">{$val}</option>";
			}
			
		}
		return $str;
		
		/*while( list($key, $val) = each($options) )
		{
			if($no_key) $key = $val;
			if( is_array($selected) && in_array("$key", $selected) )
			{
				$selectedStr = " SELECTED=\"true\" class=\"selected\"";
			} else if ("$key"=="$selected") {
				$selectedStr = " SELECTED=\"true\" class=\"selected\"";
			} else {
				$selectedStr = "";
			}
			if($no_key)
			{
				$HTML .= "<OPTION$selectedStr>".($val)."</OPTION>";
				//$HTML .= "<OPTION$selectedStr>".$val."\n";
			} else {
				$HTML .= "<OPTION$selectedStr VALUE=\"".htmlspecialchars($key)."\">".($val)."</OPTION>";
				//$HTML .= "<OPTION$selectedStr VALUE=\"".htmlspecialchars($key)."\">".$val."\n";
			}
		}
		return $HTML;*/
	}
	
?>