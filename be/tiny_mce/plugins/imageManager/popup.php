<?php
include("conf.php");

define("_ALT_COLOR",'#DDDDFF');
define("_NOR_COLOR",'#FFFFFF');

define("IS_IMAGE_MANAGER",isset($_GET['as_img'])?1:0);

if(get_magic_quotes_gpc()||get_magic_quotes_runtime())
{
	if(isset($_GET))
		$_GET=array_map(array('CFileManPermission','_StripSlashes'),$_GET);
	if(isset($_POST))
		$_POST=array_map(array('CFileManPermission','_StripSlashes'),$_POST);
	if(isset($_REQUEST))
		$_REQUEST=array_map(array('CFileManPermission','_StripSlashes'),$_REQUEST);
	if(isset($_COOKIE))
		$_COOKIE=array_map(array('CFileManPermission','_StripSlashes'),$_COOKIE);
}


class CFileManPermission {
	private $rootDir;
	
	static function _StripSlashes(&$data)
	{
		return is_array($data)?array_map(array('CFileManPermission','_StripSlashes'),$data):stripslashes($data);
	}
	
	function __construct($rootDir) {
		$this->rootDir=$rootDir;
	}
	
	function is_in_root($name) {
		$p=strpos($name,$this->rootDir);
		return ($p===0 && $name>$this->rootDir);
	}
	
	function canDelete($name) {
		return $this->is_in_root($name);
	}
	
	function canMove($src,$dst) {
		return $this->is_in_root($src)&&$this->is_in_root($dst);
	}
	
	function canRename($name) {
		return $this->is_in_root($name);
	}
	
	function canCreateFile($name) {
		return $this->is_in_root($name);
	}
	
	function isFileAcceptable($file) {
		return eregi('\.(html?|jpe?g|gif|png|zip|xls|doc|pdf|rar|ctx)$',$file);
	}
	
	function isFileAcceptableImage($file) {
		return eregi('\.(jpe?g|gif|png|bmp)$',$file);
	}
	
	
	
}

class CFileMamager {
	private $rootDir;
	public $currentDir;
	private $filearr;
	private $dirarr;
	private $all_folders;

	public $selected_folder;
	
	public $quota_limit=100000000;
	public $quota_inuse=0;
	
	private $file_permissions;
	/* @var $file_permissions CFileManPermission*/
	
	
	function __construct($rootDir,$dir) {
		$this->rootDir=$rootDir;
		$this->currentDir=$this->check_dir($dir);
		$this->file_permissions=new CFileManPermission($rootDir);
	//	$this->getDir();
	}
	
	function getAllDirs() {
		$this->all_folders=array();
		$this->selected_folder=-1;
		
		$this->_getalldirs(0,$this->rootDir,new RecursiveDirectoryIterator($this->rootDir),"");
		return $this->all_folders;
	}
	
	function _getalldirs($dirarr_ind,$cd,$it,$parent_root) {
		for( ; $it->valid(); $it->next()) {
			if($it->isDir() && !$it->isDot()) {				
				$this->all_folders[]=array('pid'=>$dirarr_ind-1,'fn'=>$it->getFilename(),"pr"=>$parent_root."/".$it->getFilename());
				$c=count($this->all_folders);
				
				$n_cd=substr($cd,-1)=="/"?$cd.$it->getFilename():$cd.'/'.$it->getFilename();
				if($n_cd.'/'==$this->getCurrentDir()) {
					$this->selected_folder=$c;
				}
				$this->_getalldirs($c,$n_cd,new RecursiveDirectoryIterator($n_cd),$parent_root."/".$it->getFilename());
			}
		}
	}
	
	
	function getCurrentDir() {
		if($this->currentDir[0]=='/')
			return $this->rootDir.$this->currentDir;
		return $this->rootDir.'/'.$this->currentDir;
	}
	
	function getOffsetDir() {
		if($this->currentDir[0]=='/')
			return $GLOBALS['FMAN_IMAGES_URL_PATH'].$this->currentDir;
		return $GLOBALS['FMAN_IMAGES_URL_PATH'].'/'.$this->currentDir;
	}
	
	function check_dir($dir) {
		if (!$dir) $dir="/";
		else {
			if ($dir[0]!="/") $dir="/".$dir;
			if ($dir[strlen($dir)-1]!="/") $dir.="/";
		}
	
		$dir = ereg_replace( "//", "/", $dir );
		$dir = ereg_replace( "(^|/)\./", "\\1", $dir );
		$dir = ereg_replace( "[^/]*(^|/)\.\./", "", $dir );
		if (!$dir) $dir="/";
	
		if (strlen($dir)==0 || $dir=="." || $dir==".." ||
				$dir=="./" || $dir=="../" ||
				$dir=="/." || $dir=="/.." || ereg("/\.\./",$dir) ) $dir="/";
	
		return $dir;
	}
	
	function check_file($file) {
		if (strlen($file)==0 || $file=="." || $file==".." || $file[0]=="/" || $file[0]=="\\" || strpos($file,"\\") ||
	   	 strpos($file,"/") ) return FALSE;
		return 1;
	}
	
	function getDir() {
		$this->dirarr=array();
		$this->filearr=array();
		return $this->_getdir(0,0,new RecursiveDirectoryIterator($this->rootDir.$this->currentDir));
	}
	
	private function _getdir($dirarr_ind,$filearr_ind,$it) {
		/* @var $it RecursiveDirectoryIterator*/
		for( ; $it->valid(); $it->next()) {
			if($it->isDir() && !$it->isDot()) {
				if($it->hasChildren()) {
					$this->dirarr[$dirarr_ind][0]=$it->getFilename();
					$this->dirarr[$dirarr_ind][1]=0;
					$this->dirarr[$dirarr_ind++][2]=$it->getMTime();
				}
			} elseif($it->isFile()) {
				$fn=(string)$it->getFilename();
				if($fn[0]=='.') {
					continue;
				}
				if(IS_IMAGE_MANAGER&&!CFileManPermission::isFileAcceptableImage($fn)) {
					continue;
				}
				$this->filearr[$filearr_ind][0]=$it->getFilename();
				$this->filearr[$filearr_ind][1]=$it->getSize();
				$this->filearr[$filearr_ind++][2]=$it->getMTime();
			}
		}
		return array('dir'=>$this->dirarr,'file'=>$this->filearr);
	}
	
	function deltree($dir,&$total_bytes_deleted,&$status_line) {
	//	$total_bytes_deleted=0;
	//	$status_line='';
		return $this->_deltree(new RecursiveDirectoryIterator($this->rootDir.$this->currentDir.$dir),$total_bytes_deleted,$status_line);
	}
	
	private function _deltree($it,&$total_bytes_deleted,&$status_line) {
		for( ; $it->valid(); $it->next()) {
			if($it->isDir() && !$it->isDot()) {
				if($it->hasChildren()) {
					$bleh = $it->getChildren();
					$this->_deltree($bleh,$total_bytes_deleted,$status_line);
				}
				if($this->file_permissions->canDelete($it->getPathname())) {
					@$res=rmdir($it->getPathname());
				}
				else {
					$res=false;
				}
				if(!$res) {
					$status_line.="Error deliting directory <b>".$files."</b><br>";
					return FALSE;
				}
			
			} elseif($it->isFile()) {
				$fs=$it->getSize();
				if($this->file_permissions->canDelete($it->getPathname())) {
					@$res=unlink($it->getPathname());
				}
				else {
					$res=false;
				}
				if(!$res) {
					$status_line.="Error deliting object <b>".$files."</b>, please contact WebAdmin.<br>";
					return FALSE;
				}
				else {
					$total_bytes_deleted+=$fs;
				}
			}
		}
		return true;
	}
	
	function removeDir($name) {
		if($this->file_permissions->canDelete($this->rootDir.$this->currentDir.'/'.$name)) {
			@$res=rmdir($this->rootDir.$this->currentDir.'/'.$name);
			return $res;
		}
		return false;
	}
	
	function MakeDir($name) {
		if($this->file_permissions->canCreateFile($this->rootDir.$this->currentDir.'/'.$name)) {
			@$res=mkdir($this->rootDir.$this->currentDir.'/'.$name,0755);
			return $res;
		}
		return false;
	}
	
	function createFile($name) {
		if($this->file_permissions->canCreateFile($this->rootDir.$this->currentDir.'/'.$name)) {
			@$res=touch($this->rootDir.$this->currentDir.'/'.$name);
			return $res;
		}
		return false;
	}
	
	function isFile($name) {
		return is_file($this->rootDir.$this->currentDir.'/'.$name);
	}
	
	function isDir($name) {
		return is_dir($this->rootDir.$this->currentDir.'/'.$name);
	}
	
	function rename($old_name,$new_name) {
		if($this->file_permissions->canRename($this->rootDir.$this->currentDir.'/'.$new_name)) {
			@$res=rename($this->rootDir.$this->currentDir.'/'.$old_name,$this->rootDir.$this->currentDir.'/'.$new_name);
			return $res;
		}
		return false;
	}
	
	function isAllowableFileExt($name) {
		return true;
	}
	
	function move_uploaded_file($file_struct) {
		if($this->file_permissions->canMove($this->rootDir.$this->currentDir.'/'.$file_struct['name'],$this->rootDir.$this->currentDir.'/'.$file_struct['name'])) {
			@$res=move_uploaded_file($file_struct['tmp_name'],$this->rootDir.$this->currentDir.'/'.$file_struct['name']);
			return $res;
		}
		return false;
	}
	
	function unlinkFile($name) {
		if($this->file_permissions->canDelete($this->rootDir.$this->currentDir.'/'.$name)) {
			@$res=unlink($this->rootDir.$this->currentDir.'/'.$name);
			return $res;
		}
		return false;
	}
	
	function getFileSize($name) {
		return filesize($this->rootDir.$this->currentDir.'/'.$name);
	}
	
	function moveFile($src,$dst) {
		if($this->file_permissions->canMove($this->rootDir.$src,$this->rootDir.$this->currentDir.$dst)) {
			@$res=rename($this->rootDir.$src,$this->rootDir.$this->currentDir.$dst);
			return $res;
		}
		return false;
	}
}

class CFManInterface {
	public $file_manager=null;
	private $files;
	private $data;
	private $cur_dir;
	private $srtfld;
	
	public $status_line;
	public $sortfield;
	public $sortorder;
	public $clipboard='';
	public $resource='';
	
	/* @var $file_manager CFileMamager*/
	
	function __construct($virtual_dir,$currentDir,$data) {
		$this->file_manager=new CFileMamager($virtual_dir,$currentDir);
		$this->cur_dir=$currentDir;
		$this->files=$data['files'];
		$this->data=$data;
		$this->clipboard=$this->data['clipboard'];
	}
	
	function commandButton($newfoldername,$command) {
		
		if(!$this->file_manager->check_file($newfoldername))
		{
			$this->status_line.="Invalid name <b>$newfoldername</b>";
		}
		else {
			switch ($command) {
				case 1: {
					if(!$this->file_manager->MakeDir($newfoldername))
						$this->status_line.="Error creating folder <b>$newfoldername</b>";
						break;
					} 
					case 3: {
						if(!$this->file_manager->createFile($newfoldername.".html"))
						//if (!@touch($basedir.$dir."/".$newfoldername.".html"))
							$this->status_line.="Error creating file <b>$newfoldername</b>";
						break;
					}
				 case 2: {
				 	if($this->file_manager->isFile($newfoldername)) {
						$this->status_line.="Veche ima fail s takova ime<br>";
					} elseif( $this->file_manager->isDir($newfoldername) ) {
						$this->status_line.="Veche ima Folder s takova ime<br>";
					} elseif(!$this->file_manager->check_file($this->files[0])) {
						$this->status_line.="Invalid name <b>".$this->files[0]."</b><br>";
					} elseif (!$this->file_manager->rename($this->files[0],$newfoldername) ) {
						$this->status_line.="Error Renam object <b>".$this->files[0]."</b>.<br>";
					}
				}
			}
		}
		return $this->status_line;
	}
	
	function Upload() {
		for ($i=1;$i<=max(1,(int)$this->data['urlcount']);$i++) {
			$uf=$_FILES["userfile".$i];
		
			if(!$this->file_manager->isAllowableFileExt($uf['name']) || !$this->file_manager->check_file($uf['name']))
			{
				$this->status_line.="Invalid name <b>{$uf['name']}</b><br>";
				continue;
			}
			if($this->file_manager->isFile($uf['name']))
				continue; 
			if(intval($uf['size'])+$this->file_manager->quota_inuse<$this->file_manager->quota_limit) {
				if(is_uploaded_file($uf['tmp_name'])) {
					if(!$this->file_manager->move_uploaded_file($uf)) {
						$this->status_line.="ERROR Uploading files.";
					} else {
//						update_quota($rid, $uf_s);
						$this->file_manager->quota_inuse+=intval($uf['size']);
					}
				}
			} else {
				$this->status_line.="Not enought disk space!";
				break;
			}
		}
	}
	
	function Delete() {
		$total_bytes_deleted=0;
	//	$files=$this->data['files'];
	//	for ($i=0;$i<count($files);$i++) {
			//$uf=$files[$i];
			$uf=$this->data['to_del'];
			if ($this->file_manager->check_file($uf) &&$this->file_manager->isFile($uf) )
			{
				$uf_s=$this->file_manager->getFileSize($uf);
				if($this->file_manager->unlinkFile($uf))
				{
					$total_bytes_deleted+=$uf_s;
				} else {
					$this->status_line.="Error deliting file <b>".$this->data['to_del']."</b><br>";
				}
			} elseif ($this->file_manager->isDir($uf) ) {
				if ($this->file_manager->deltree($uf,$total_bytes_deleted,$this->status_line))
				{
					if ( !$this->file_manager->removeDir($uf) )
					{
						$this->status_line.="Error deliting folder <b>".$this->data['to_del']."</b>, Not empty.<br>";
					}
				}
			} else {
				$this->status_line.="Error deliting object <b>".$this->data['to_del']."</b>, please contact WebAdmin.<br>";
			}
	//	}
		if ($total_bytes_deleted>0)
		{
//			update_quota($rid, -$total_bytes_deleted);
			$this->file_manager->quota_inuse-=$total_bytes_deleted;
		}
		return $total_bytes_deleted;
	}
	
	function Paste() {
		$this->clipboard=$clipboard = explode ("|", $this->data['clipboard']);
		$srcdir=$this->file_manager->check_dir($clipboard[0]);
		if($srcdir[strlen($srcdir)-1]=='/')
			$srcdir=substr($srcdir,0,strlen($srcdir)-1);
		for ($i=1;$i<count($clipboard);$i++) {
		//	$srcfile = $basedir.$srcdir."/".$clipboard[$i];
		//	$dstfile = $basedir.$dir."/".$clipboard[$i];
			if(!$this->file_manager->check_file($clipboard[$i])) {
				$this->status_line.="Invalid name ".$clipboard[$i]."<br>";
			} elseif ($this->file_manager->isFile($clipboard[$i])) {
				$this->status_line.=$clipboard[$i].", Veche ima fail s takova ime v tazi direktoria<br>";
			} elseif ( $this->file_manager->isDir($clipboard[$i]) ) {
				$this->status_line.=$clipboard[$i].", Veche ima Folder s takova ime v tazi direktoria<br>";
			} elseif ( !$this->file_manager->moveFile($srcdir.'/'.$clipboard[$i],$clipboard[$i]) ) {
				$this->status_line.="failed to Paste ".$clipboard[$i]."<br>";
			}
		}
		$this->clipboard=$this->data['clipboard']='';
		
	}
	
	function cmpdesc ($a, $b) {
		if ($a[$GLOBALS['sort_field_name']] == $b[$GLOBALS['sort_field_name']]) return 0;
		return ($a[$GLOBALS['sort_field_name']] > $b[$GLOBALS['sort_field_name']]) ? -1 : 1;
	}

	function cmpasc ($a, $b) {
		if ($a[$GLOBALS['sort_field_name']] == $b[$GLOBALS['sort_field_name']]) return 0;
		return ($a[$GLOBALS['sort_field_name']] < $b[$GLOBALS['sort_field_name']]) ? -1 : 1;
	}
	
	function getAllDirs() {
		return $this->file_manager->getAllDirs();
	}
	
	function getSelectedDirIndex() {
		return (int)$this->file_manager->selected_folder;
	}
	
	function printDir( $sortorder, $sortfield) {
		$dir=$this->cur_dir;
		$webdir=$this->file_manager->getOffsetDir();
	//	$webdir = $base_virtual_disk_URL."/".$this->data['resource'];
	//	if ($webdir[strlen($webdir)-1]=="/")  $webdir=substr($webdir,0,-1);
	//	$webdir.=$dir;
		$this->sortorder=$sortorder;
		$this->sortfield=$sortfield;
		$root=realpath(dirname(__FILE__).'/../../../../');
		//echo $root;
		$array=$this->file_manager->getDir();
		$filearr=$array['file'];
		$dirarr=$array['dir'];
		$this->srtfld=$sortfield;
		
		$arr=array();
		
		if(empty($filearr)) {
			for($i=0;$i<5;$i++) {
				$arr[]=<<<EOD
				<img  src="images/no_image.png" alt="No images in this folder" /><br />
				<div style="padding-top:2px;">				
				No images in this folder
				</div>
EOD;
			}
		}
		
		for ($j=0; $j<count($filearr); $j++) {
			$color=$j%2?_ALT_COLOR:_NOR_COLOR;
			$link=render_edit_link($dir.$filearr[$j][0]);
			$date=date("d F Y H:i",$filearr[$j][2]);
			$is_image=isImageExtention($filearr[$j][0]);
			
			
				$a=<<<EOD
				href="#" onclick="preview('{$webdir}{$filearr[$j][0]}');return false;"
EOD;
				$img=urlencode($webdir.$filearr[$j][0]);
				$bkp=urlencode($_SERVER['REQUEST_URI']);
			if($is_image) {
				
				$edit=<<<EOD
				<div style="padding-top:2px;">				
				<a href="{$webdir}{$filearr[$j][0]}" target="_blank"><img  src="images/view.gif" alt="View" /></a> &nbsp; <a href="edit.php?img={$img}&amp;bkp={$bkp}"><img  src="images/edit.gif" alt="Edit" /></a> &nbsp; <a href="#" onclick="return CheckDelete(&quot;{$filearr[$j][0]}&quot;)"><img  src="images/delete.gif" alt="Delete" /></a> &nbsp; <a href="#" onclick="TransferSelected(&quot;{$filearr[$j][0]}&quot;,&quot;{$filearr[$j][1]}&quot;)">Select</a> 
				</div>
EOD;
			}
			else {
//					$a=<<<EOD
//					href="{$webdir}{$filearr[$j][0]}" onclick="hide_preview();" target="_blank"
//EOD;
				$edit="";
			}
			$arr[]=<<<EOD
			<a href="#"onclick="preview('{$webdir}{$filearr[$j][0]}');return false;"><img src="{$webdir}{$filearr[$j][0]}" width="90" /></a><br />
			{$edit}			
EOD;
		}
		
		return $this->renderArrayToTable($arr);
	
	}
	
	function renderArrayToTable($in_array,$Cols=5,$Rows=0) {
		
		if(!is_array($in_array)) return false;
		if($Cols + $Rows === 0) return false;
	
		if($Cols>0){
			$Rows = ceil(count($in_array)/$Cols);
		} else if ($Rows>0) {
			$Cols = ceil(count($in_array)/$Rows);
		}
	
		$out_array=Array();
		$RowColVar = $Cols ;
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
	
		$res = '';
		$tr="<tr>";
		$td="<td class='imgTd'>";
		foreach ($out_array as $row) {
			$res .= "$tr$td".implode("</td>$td", $row).'</td></tr>';
		}
		return $res;
	}
	
	function render() {
		if($this->data['upload']) {
			$this->Upload();
		}
		if($this->data['to_del']) {
			$this->Delete();
		}
		if($this->data['paste']) {
			$this->Paste();
		}
		if($this->data['commandbtn']) {
			$this->commandButton($this->data['newfoldername'],intval($this->data['command']));
		}
		$sortorder=$this->data['sortorder'];
		$sortfield=$this->data['sortfield'];
		if ($sortorder!="asc" && $sortorder!="desc" ) 
			$sortorder=$this->data['sortorder']= "asc";
		if ($sortfield!=1 && $sortfield!=2 && $sortfield!=3) 
			$sortfield=$this->data['sortfield']= 0;
		return $this->printDir($sortorder,$sortfield);
	}
}

function get_full_virtual_disk_path($path){
	$virtual_disk_basehomedir = $GLOBALS['FMAN_IMAGES_ABS_PATH'];
	$path = $virtual_disk_basehomedir."/".$path;
	return $path;
}

function isImageExtention($file) {
	$images=array("jpg","png","gif","jpeg","bmp");
	$e=pathinfo($file);
	return in_array($e['extension'],$images);
}


$base_virtual_disk_URL = $GLOBALS['FMAN_IMAGES_URL_PATH'];
function isAllowableFileExt($file){
	$AllowableFileExtArray = Array("jpg", "png", "gif");
//	if(($ext = strtolower(substr( strrchr($file, "."), 1))) && in_array($ext, $AllowableFileExtArray) ) return true;
	return true;
}

function myError($code) {
	echo "Error";
	exit();
}

function render_edit_link($file){
	if(eregi('\.(html?)|(ctx)$', $file)){
		return " | <a href=\"page_edit.php?page=$file\" target=_blank>edit</a>";
	}
}

function cmpdesc ($a, $b) {
	global $srtfld;
	if ($a[$srtfld] == $b[$srtfld]) return 0;
	return ($a[$srtfld] > $b[$srtfld]) ? -1 : 1;
}



function getBaseDir() {
	$basedir=get_full_virtual_disk_path("");
	if ($basedir[strlen($basedir)-1]=="/") $basedir=substr($basedir,0,-1);
	return $basedir;
}

function getDir($basedir) {
	$dir=CFileMamager::check_dir($_POST['dir']);
	if (!@is_dir($basedir)) return false;
	if (!@is_dir($basedir.$dir)) $dir="/";
	return $dir;
}

	$basedir=getBaseDir();
	$dir=getDir($basedir);
	if($dir===false) myError(1);
	
	$selector=$_GET['selector'];
	
	$sortorder=$_POST['sortorder'];
	$sortfield=$_POST['sortfield'];
	if ($sortorder!="asc" && $sortorder!="desc" ) 
		$sortorder= "asc";
	if ($sortfield!=1 && $sortfield!=2 && $sortfield!=3) 
		$sortfield= 0;
	
	
	$fm=new CFManInterface($basedir,$dir,$_POST);
	
	
?>


<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="cache-control" content="no-cache">
<title>File Manager</title>

<script src='js/dtree.js'></script>
<link rel="stylesheet" href="js/dtree.css">

<script language="JavaScript">
<!--


var i=1;

history[history.length]='';
history[history.length-1]='';

function br_onclick() {
	if (i<30) {
		j=i;
		i++;
		eval("document.f2.elements.userfile"+j).insertAdjacentHTML("AfterEnd","<br id=\"brid"+i+"\"><input type=\"file\" name=\"userfile"+i+"\" size=\"40\">");
		cf.innerHTML=i;
		document.f2.elements.urlcount.value=i;
	}
}
function bl_onclick() {
	if (i>1) {
		j=i;
		i--;
		eval("document.f2.elements.userfile"+j+".outerHTML=\"\"");
		cf.innerHTML=i;
		document.f2.elements.urlcount.value=i;
		eval("brid"+j+".outerHTML=\"\"");
	}
}

function setsort(sortfield) {
	if (sortfield==document.f2.sortfield.value) {
		document.f2.sortorder.value=='asc'?document.f2.sortorder.value='desc':document.f2.sortorder.value='asc';
	} else {
		document.f2.sortfield.value=sortfield;
	}
	document.f2.submit();
	return false;
}

function GoBack(){
	str=document.f2.dir.value;
	if (str=="/") return false;
	str=str.substring(0,str.lastIndexOf("/"));
	str=str.substring(0,str.lastIndexOf("/")+1);
	document.f2.dir.value=str;
	document.f2.submit();
	return false;
}

function GoToFolder(folder){
	if(folder.substring(0,1)=="/") document.f2.dir.value=folder;
	else document.f2.dir.value+=folder+"/";
	document.f2.submit();
	return false;
}

function CopyToClipboard(){
	tmp="";
	for (i=0; i<document.f2.elements.length; i++) {
		if( (document.f2.elements[i].name=='files[]') && (document.f2.elements[i].checked) )
			tmp+="|"+document.f2.elements[i].value;
	}
	if (tmp.length>1) {
		document.f2.clipboard.value=document.f2.dir.value+tmp;
	} else {
		document.f2.clipboard.value='';
	}
	
	return false;
}

function CheckClipboard(){
	str=document.f2.clipboard.value;
	if(str.length>1) {
		str=str.substring(0,str.lastIndexOf("|"));
		if (str!=document.f2.dir.value) return true;
	}
	alert(" Ther's Nothing to be Paste!");
	return false;
}

function CheckNewDir(){
	str=document.f2.newfoldername.value;
	if(str.length>=1 && str.indexOf("/")==-1) return true;
	alert(str+" is Invalid Name!");
	return false;
}

function CheckDelete(to_del){
	tmp=to_del;
	if (tmp.length>1) {
		if(confirm("are you sure you want to delete:\n"+tmp)) {
			document.getElementById('to_del').value=to_del;
			document.f2.submit();
			return true;
		}
	}
	return false;
}
<? if($selector!="") { ?>
function TransferSelected(file_selected,file_size){
	base_virtual_disk_URL = "<? echo $base_virtual_disk_URL; ?>"+document.f2.dir.value;
	<?echo stripslashes($selector);?>base_virtual_disk_URL+file_selected, file_selected, file_size);
	window.close();
}
<? } ?>
function checkthis(){
	return true;
}

function preview(src) {
	var i=document.getElementById('preview');
	i.src=src+"?r="+Math.random();
	i.style.visibility="visible";
}

function hide_preview() {
	var i=document.getElementById('preview');
	i.style.visibility="hidden";
}

-->


</script>

<style>

.imgTd {
	width:20%;
	text-align:center;
	border:1px solid royalblue;
}

td{
	font-size:12px;
	color:#000000;
}

img {
	border:none;
}

a {
	font-size:12px;
	color:#c00000;
}

</style>

</head>
<body bgcolor="#FFFFFF" text="#000000" topmargin="2" marginheight="2" leftmargin="2" marginwidth="2">
<form name="f2" method="post" enctype="multipart/form-data" action="<?echo basename($PHP_SELF);?>" onSubmit="return checkthis();">
<input name="to_del" type="hidden" id="to_del" value="" />
<table border=0 cellpadding=2 cellspacing=0 bgcolor=#ffffff width="100%">
<tr><td bgcolor=#cccc99 width=1% class="hnm">&nbsp;Location&nbsp;</td><td bgcolor=#cccc99><input name="dir" value="<? echo $dir ?>">
<input type="submit" value="Go">&nbsp;<input type="button" value="Back" onClick="return GoBack();">&nbsp;
<input type="hidden" value="1" name="command" />Create New Folder: <input type="text" name="newfoldername" value="" size="20"><input type="submit" value=" Do It " name="commandbtn" onClick="return CheckNewDir();">
</td></tr>
</table>
<table width="100%" cellpadding="5" cellspacing="0" border="0">
<tr>
	<td style="width:170px;" valign="top">
	<div style="width:160px;height:160px;border:1px solid royalblue" id="preview_div">
		<?/*<img src="" id="preview" style="visibility:hidden" width="160" height="160" />*/?>
		<iframe frameborder="0" marginheight="0" marginwidth="0" src="about:blank" id="preview" style="visibility:hidden;width:160px;height:160px;" >
		<html>
			<body>
			</body>
		</html>
		</iframe>
		</div>
	<div class="dtree">
	<?php
	$f=$fm->getAllDirs();
	
	if(!empty($f)) {
		echo <<<EOD
		<script type="text/javascript">
		<!--

		d = new dTree('d');

		d.add(0,-1,'Images','GoToFolder(&quot;/&quot;)');
EOD;
		$selected=-1;
		
		foreach ($f as $k=>$v) {
			$i=$k+1;
			$p=$v['pid']+1;
			echo <<<EOD
			d.add({$i},{$p},'{$v['fn']}','GoToFolder(&quot;{$v['pr']}&quot;)',null,null,'img/folder.gif','img/folderopen.gif');
EOD;
			
			if($v['pr']=$fm->file_manager->currentDir==$v['pr']||$v['pr']=$fm->file_manager->currentDir==$v['pr'].'/') {
				$selected=$i;
			}
		}
		
		$ss="";
		if($selected>-1) {
			$ss="d.openTo('{$selected}', true);";
		}
		echo <<<EOD
		
		document.write(d);
		{$ss}
		//-->
	</script>
EOD;

	}
	?>
	</div>
		
	</td>
	
	<td valign="top">
		<table width="100%" cellpadding="5" cellspacing="0" style="border-collapse:collapse;">
		<?=$fm->render();?>
		</table>
		<hr width="100%" noshade size="1" align=left>
<table><tr><td>

	<input type="button" value="&lt;&lt;" name="xxl" onClick="bl_onclick(this)">&nbsp;&nbsp;&nbsp;</td>
	<td><div id="cf">1</div></td>
	<td>&nbsp;&nbsp;&nbsp;<input type="button" value="&gt;&gt;" name="xxr" onClick="br_onclick(this)">&nbsp;&nbsp;&nbsp;&nbsp;files
</td></tr></table>
<input type="hidden" name="urlcount" value="1">
<input type="file" name="userfile1" size="40">

	<input type="submit" value="Upload" name="upload">&nbsp;&nbsp;&nbsp;&nbsp;
<input type="hidden" name="sortorder" value="<? echo $fm->sortorder ?>">
<input type="hidden" name="sortfield" value="<? echo $fm->sortfield ?>">
<input type="hidden" name="clipboard" value="<? echo $fm->clipboard ?>">
<input type="hidden" name="resource" value="<? echo $resource ?>">
<input type="hidden" name="selector" value="<? echo htmlspecialchars(stripslashes($_GET['selector'])) ?>">
	</td>
	
</tr>
</table>



</form>
</body>
</html>