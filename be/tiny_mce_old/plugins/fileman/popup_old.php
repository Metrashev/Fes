<?php
include("conf.php");
function get_full_virtual_disk_path($path){
//	$virtual_disk_basehomedir = "/z/pol/www/tu/www/vd";
//	$virtual_disk_basehomedir = "/z/pol/www/projects/ms/photos";
	$virtual_disk_basehomedir = $GLOBALS['FMAN_IMAGES_ABS_PATH'];

	$path = $virtual_disk_basehomedir."/".$path;
	return $path;
}


$base_virtual_disk_URL = $GLOBALS['FMAN_IMAGES_URL_PATH'];
function isAllowableFileExt($file){
	$AllowableFileExtArray = Array("jpg", "png", "gif");
//	if(($ext = strtolower(substr( strrchr($file, "."), 1))) && in_array($ext, $AllowableFileExtArray) ) return true;
	return true;
}

function myError($code) {
	echo "BLia";
	exit();
}

function render_edit_link($file){
	if(eregi('\.(html?)|(ctx)$', $file)){
		return " | <a href=\"page_edit.php?page=$file\" target=_blank>edit</a>";
	}
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
	    strpos($file,"/") || ereg("^\.in\..*\.$",$file) ) return FALSE;
	return 1;
}

function cmpdesc ($a, $b) {
	global $srtfld;
	if ($a[$srtfld] == $b[$srtfld]) return 0;
	return ($a[$srtfld] > $b[$srtfld]) ? -1 : 1;
}

function cmpasc ($a, $b) {
	global $srtfld;
	if ($a[$srtfld] == $b[$srtfld]) return 0;
	return ($a[$srtfld] < $b[$srtfld]) ? -1 : 1;
}

function print_dir($dir, $sortfield, $sortorder) {
	global $srtfld,$resource, $basedir, $base_virtual_disk_URL;

	$dirarr_ind=0;
	$filearr_ind=0;

	$dirarr=array();

	$handle=opendir($basedir.$dir);
	while ($file = readdir($handle))
	{
		if (check_file($file))
		{
			$q=stat($basedir.$dir."/".$file);
			if ($q[2]&040000)
			{
				$dirarr[$dirarr_ind][0]=$file;
				$dirarr[$dirarr_ind][1]=0;
				$dirarr[$dirarr_ind++][2]=$q[9];
			} else if(eregi('\.(html?|jpe?g|gif|png|zip|xls|doc|pdf|rar|ctx)$',$file)) {
				$filearr[$filearr_ind][0]=$file;
				$filearr[$filearr_ind][1]=$q[7];
				$filearr[$filearr_ind++][2]=$q[9];
			}
		}

	}
	closedir($handle);

	$webdir = $base_virtual_disk_URL."/".$resource;
	if ($webdir[strlen($webdir)-1]=="/")  $webdir=substr($webdir,0,-1);
	$webdir.=$dir;
//	$webdir .=  $dir[0]=="/" ? substr($dir,1)."/" : "";

	$root=realpath(dirname(__FILE__).'/../../../../');
	//echo $root;

	$srtfld=$sortfield;
	if ($sortorder=="asc")
	{
		if ($filearr) usort($filearr,cmpasc);
		if ($srtfld==1) $srtfld=0;
		if ($dirarr) usort($dirarr,cmpasc);
		for ($j=0; $j<count($dirarr); $j++)
			echo "<tr bgcolor=#eeeecc><td><input type=checkbox name=\"files[]\" value=\"".$dirarr[$j][0]."\"></td><td class=\"hnm\">&nbsp;<a href=\"#\" onClick=\"return GoToFolder('".$dirarr[$j][0]."');\"><img src=\"folder.gif\" border=0> ".$dirarr[$j][0]."</a></td><td align=right>&nbsp;</td><td class=\"hnm\">&nbsp;".date("d F Y H:i",$dirarr[$j][2])."</td></tr>\n";
		for ($j=0; $j<count($filearr); $j++) {
			
			if(@!getimagesize($root.$webdir.$filearr[$j][0])) {
				$is_Pic='0';
			}
			else {
				$is_Pic='1';
			}
			echo "<tr bgcolor=#eeeecc><td><input ispic=\"{$is_Pic}\" type=checkbox name=\"files[]\" value=\"".$filearr[$j][0]."\" filesize=\"".$filearr[$j][1]."\"></td><td class=\"hnm\">&nbsp;<a href=\"$webdir".$filearr[$j][0]."\" target=\"_blank\"><img src=\"f.gif\" border=0> ".$filearr[$j][0]."</a>".render_edit_link($dir.$filearr[$j][0])."</td><td class=\"hnm\" align=right>".$filearr[$j][1]."&nbsp;</td><td class=\"hnm\">&nbsp;".date("d F Y H:i",$filearr[$j][2])."</td></tr>\n";
		}
	} else {
		if ($filearr) usort($filearr,cmpdesc);
		if ($srtfld==1) $srtfld=0;
		if ($dirarr) usort($dirarr,cmpdesc);
		for ($j=0; $j<count($filearr); $j++) {
			
			if(@!getimagesize($root.$webdir.$filearr[$j][0])) {
				$is_Pic='0';
			}
			else {
				$is_Pic='1';
			}
			echo "<tr bgcolor=#eeeecc><td><input ispic=\"{$is_Pic}\" type=checkbox name=\"files[]\" value=\"".$filearr[$j][0]."\"></td><td class=\"hnm\">&nbsp;<a href=\"$webdir".$filearr[$j][0]."\" target=\"_blank\"><img src=\"f.gif\" border=0> ".$filearr[$j][0]."</a>".render_edit_link($dir.$filearr[$j][0])."</td><td class=\"hnm\" align=right>".$filearr[$j][1]."&nbsp;</td><td class=\"hnm\">&nbsp;".date("d F Y H:i",$filearr[$j][2])."</td></tr>\n";
		}
		for ($j=0; $j<count($dirarr); $j++)
			echo "<tr bgcolor=#eeeecc><td><input type=checkbox name=\"files[]\" value=\"".$dirarr[$j][0]."\" filesize=\"".$filearr[$j][1]."\"></td><td class=\"hnm\">&nbsp;<a href=\"#\" onClick=\"return GoToFolder('".$dirarr[$j][0]."');\"><img src=\"folder.gif\" border=0> ".$dirarr[$j][0]."</a></td><td class=\"hnm\" align=right>&nbsp;</td><td class=\"hnm\">&nbsp;".date("d F Y H:i",$dirarr[$j][2])."</td></tr>\n";
	}
}


function deltree($dir) {
	global $total_bytes_deleted, $status_line;

	$handle=opendir($dir);
	while ($file = readdir($handle))
	{
		if ($file=="." || $file=="..") continue;

		if (is_file($dir."/".$file))
		{
			$uf_s=filesize($dir."/".$file);
			if(@unlink($dir."/".$file))
			{
				$total_bytes_deleted+=$uf_s;
			} else {
				$status_line.="Error deliting file <b>".$files."</b><br>";
				return FALSE;
			}
		} elseif (is_dir($dir."/".$file)) {
			if (deltree($dir."/".$file))
			{
				rmdir($dir."/".$file);
			} else {
				$status_line.="Error deliting directory <b>".$files."</b><br>";
				return FALSE;
			}
		} else {
			$status_line.="Error deliting object <b>".$files."</b>, please contact WebAdmin.<br>";
			return FALSE;
		}
	}
	closedir($handle);
	return TRUE;
}

// echo auth_rid($rid);

/*
	auth("disk_".$resource);

	if( !($rid=get_virtual_disk_rid($resource)) )
	{
		MyError(1);
	}

	$quota = get_quota($rid);
	$quota_limit=$quota[quota_limit];
	$quota_inuse=$quota[quota_inuse];
*/

$quota_limit=100000000;
$quota_inuse=0;

	$basedir=get_full_virtual_disk_path("");
	if ($basedir[strlen($basedir)-1]=="/") $basedir=substr($basedir,0,-1);

	$dir=check_dir($_POST['dir']);
	if (!@is_dir($basedir)) MyError(1);
	if (!@is_dir($basedir.$dir)) $dir="/";

	$sortorder = $_POST['sortorder'];
	$sortfield = $_POST['sortfield'];

	$commandbtn = $_POST['commandbtn'];
	$newfoldername = $_POST['newfoldername'];

	if ($sortorder!="asc" && $sortorder!="desc" ) $sortorder="asc";
	if ($sortfield!=1 && $sortfield!=2 && $sortfield!=3) $sortfield=0;


	if ($commandbtn) { // create new folder
		if(!check_file($newfoldername))
		{
			$status_line.="Invalid name <b>$newfoldername</b>";
		} elseif ($command==1) {
			if (!@mkdir($basedir.$dir."/".$newfoldername, 0755))
				$status_line.="Error creating folder <b>$newfoldername</b>";
		} elseif ($command==3) {
			if (!@touch($basedir.$dir."/".$newfoldername.".html"))
				$status_line.="Error creating file <b>$newfoldername</b>";
		} elseif ($command==2) {
			$uf = $basedir.$dir."/".$files[0];
			$ufn = $basedir.$dir."/".$newfoldername;

			if ( is_file($ufn) ) {
				$status_line.="Veche ima fail s takova ime<br>";
			} elseif( is_dir($ufn) ) {
				$status_line.="Veche ima Folder s takova ime<br>";
			} elseif(!check_file($files[0])) {
				$status_line.="Invalid name <b>".$files[0]."</b><br>";
			} elseif (!@rename($uf, $ufn) ) {
				$status_line.="Error Renam object <b>".$files[0]."</b>.<br>";
			}
		}
	}

	if ($upload == "Upload") { // process file uploads
		for ($i=1;$i<=max(1,(int)$urlcount);$i++) {
			$uf="userfile".$i;
			$uf_n="userfile".$i."_name";
			$uf_t="userfile".$i."_type";
			$uf_s="userfile".$i."_size";
			$uf=$$uf;
			$uf_n=$$uf_n;
			$uf_t=$$uf_t;
			$uf_s=$$uf_s;

			if(!isAllowableFileExt($uf_n) || !check_file($uf_n))
			{
				$status_line.="Invalid name <b>$uf_n</b><br>";
				continue;
			}


			$uf_n=$basedir.$dir."/".$uf_n;
			if (is_file($uf_n)) $uf_s-=filesize($uf_n); clearstatcache();
			if ($uf_s+$quota_inuse < $quota_limit) {
				if (is_file($uf)) {
					if( !@copy($uf,$uf_n) ) {
						$status_line.="ERROR Uploading files.";
					} else {
//						update_quota($rid, $uf_s);
						$quota_inuse+=$uf_s;
					}
				}
			} else {
				$status_line.="Not enought disk space!";
				break;
			}
		}
	}

	if ($delete=="Delete") { // delete files and folders
		$total_bytes_deleted=0;
		for ($i=0;$i<count($files);$i++) {
			$uf = $basedir.$dir."/".$files[$i];
			if ( check_file($files[$i]) && is_file($uf) )
			{
				$uf_s=filesize($uf);
				if(@unlink($uf))
				{
					$total_bytes_deleted+=$uf_s;
				} else {
					$status_line.="Error deliting file <b>".$files[$i]."</b><br>";
				}
			} elseif ( is_dir($uf) ) {
				if (deltree($uf))
				{
					if ( !@rmdir($uf) )
					{
						$status_line.="Error deliting folder <b>".$files[$i]."</b>, Not empty.<br>";
					}
				}
			} else {
				$status_line.="Error deliting object <b>".$files[$i]."</b>, please contact WebAdmin.<br>";
			}
		}
		if ($total_bytes_deleted>0)
		{
//			update_quota($rid, -$total_bytes_deleted);
			$quota_inuse-=$total_bytes_deleted;
		}
	}

	if ($paste=="Paste") { // Paste files
		$clipboard = explode ("|", $clipboard);
		$srcdir=check_dir($clipboard[0]);
		for ($i=1;$i<count($clipboard);$i++) {
			$srcfile = $basedir.$srcdir."/".$clipboard[$i];
			$dstfile = $basedir.$dir."/".$clipboard[$i];
			if (!check_file($clipboard[$i])) {
				$status_line.="Invalid name ".$clipboard[$i]."<br>";
			} elseif ( is_file($dstfile) ) {
				$status_line.=$clipboard[$i].", Veche ima fail s takova ime v tazi direktoria<br>";
			} elseif ( is_dir($dstfile) ) {
				$status_line.=$clipboard[$i].", Veche ima Folder s takova ime v tazi direktoria<br>";
			} elseif ( !@rename($srcfile, $dstfile) ) {
				$status_line.="failed to Paste ".$clipboard[$i]."<br>";
			}
		}

		$clipboard="";
	}
?>


<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="cache-control" content="no-cache">
<title>File Manager</title>
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

function CheckDelete(){
	tmp="";
	for (i=0; i<document.f2.elements.length; i++) {
		if( (document.f2.elements[i].name=='files[]') && (document.f2.elements[i].checked) )
			tmp+=document.f2.elements[i].value+"\n";
	}
	if (tmp.length>1) {
		return confirm("are you sure you want to delete:\n"+tmp);
	} else {
		alert("nothing to delete");
	}
	return false;
}
<? if($selector!="") { ?>
function TransferSelected(){
	base_virtual_disk_URL = "<? echo $base_virtual_disk_URL; ?>"+document.f2.dir.value;
	for (i=0; i<document.f2.elements.length; i++) {
		if( (document.f2.elements[i].name=='files[]') && (document.f2.elements[i].checked) ) {
		//	if(document.f2.elements[i].ispic==1)
				<?echo stripslashes($selector);?>base_virtual_disk_URL+document.f2.elements[i].value, document.f2.elements[i].value, document.f2.elements[i].filesize);
		//	else
		//		<?/*echo $selector.'.insertLink'*/;?>(base_virtual_disk_URL+document.f2.elements[i].value, document.f2.elements[i].value);
		}
	}
	window.close();
}
<? } ?>
function checkthis(){
	return true;
}


-->


</script>

</head>
<body bgcolor="#999999" text="#000000" topmargin="2" marginheight="2" leftmargin="2" marginwidth="2">




<table width="100%" border="0" cellspacing="0" cellpadding="4" align="center" bgcolor="#CCCCCC">
  <tr>
    <td valign="top" colspan="2" class="hnm">
<form name="f2" method="post" enctype="multipart/form-data" action="<?echo basename($PHP_SELF);?>" onSubmit="return checkthis();">
<table border=0 cellpadding=2 cellspacing=0 bgcolor=#ffffff width="100%">
<tr><td bgcolor=#cccc99 width=1% class="hnm">&nbsp;Location&nbsp;</td><td bgcolor=#cccc99><input name="dir" value="<? echo $dir ?>">
<input type="submit" value="Go">
<input type="button" value="Back" onClick="return GoBack();">
<input type="submit" value="Delete" name="delete" onClick="return CheckDelete();">
<input type="button" value="Cut" name="Cut" onClick="return CopyToClipboard();">
<input type="submit" value="Paste" name="paste" onClick="return CheckClipboard();">
<? if($selector!="") {?> <input type="button" value="Select" name="fselect" onClick="TransferSelected();"> <? } ?>
</td></tr>
</table>
<table border=0 cellpadding=0 cellspacing=1 bgcolor=#ffffff width="100%">
<tr bgcolor=#cccc99>
	<td width=20>&nbsp;</td>
	<td class="hnm">&nbsp;<a href="#" onClick="return setsort('0');">Name <? if ($sortfield=='0') if($sortorder=="asc") echo "<img src='asc.gif' border=0>"; else echo "<img src='desc.gif' border=0>"; ?></a></td>
	<td  class="hnm" width=100 align=right><a href="#" onClick="return setsort('1');"><? if ($sortfield=='1') if($sortorder=="asc") echo "<img src='asc.gif' border=0>"; else echo "<img src='desc.gif' border=0>"; ?> Size</a>&nbsp;</td>
	<td  class="hnm" width=100>&nbsp;<a href="#" onClick="return setsort('2');">Modified <? if ($sortfield=='2') if($sortorder=="asc") echo "<img src='asc.gif' border=0>"; else echo "<img src='desc.gif' border=0>"; ?></a></td>
</tr>

<?
	print_dir($dir,$sortfield,$sortorder);
	printf("<tr bgcolor=\"#D5D5D5\"><td class=\"hnm\" colspan=4>&nbsp;%s</td></tr>", $status_line);
?>
</table>

<hr width="100%" noshade size="1" align=left>

<select name="command">
<option value="1">New Dir</option>
<option value="3">New HTML Document</option>
<option value="2">Rename first checked to</option>
</select>
<input type="text" name="newfoldername" value="" size="20"><input type="submit" value=" Do It " name="commandbtn" onClick="return CheckNewDir();">

<hr width="100%" noshade size="1" align=left>
<table><tr><td>

	<input type="button" value="&lt;&lt;" name="xxl" onClick="bl_onclick(this)">&nbsp;&nbsp;&nbsp;</td>
	<td><div id="cf">1</div></td>
	<td>&nbsp;&nbsp;&nbsp;<input type="button" value="&gt;&gt;" name="xxr" onClick="br_onclick(this)">&nbsp;&nbsp;&nbsp;&nbsp;files
</td></tr></table>
<input type="hidden" name="urlcount" value="1">
<input type="file" name="userfile1" size="40">

	<input type="submit" value="Upload" name="upload">&nbsp;&nbsp;&nbsp;&nbsp;
<input type="hidden" name="sortorder" value="<? echo $sortorder ?>">
<input type="hidden" name="sortfield" value="<? echo $sortfield ?>">
<input type="hidden" name="clipboard" value="<? echo $clipboard ?>">
<input type="hidden" name="resource" value="<? echo $resource ?>">
<input type="hidden" name="selector" value="<? echo htmlspecialchars(stripslashes($selector)) ?>">
</form>
    </td>
  </tr>
</table>
</body>
</html>