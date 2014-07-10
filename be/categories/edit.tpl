<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=<?=isset($GLOBALS['CONFIG']['SITE_CHARSET'])?$GLOBALS['CONFIG']['SITE_CHARSET']:'UTF-8';?>">
<table class="viewHeader" cellpadding="0" cellspacing="0">
	<tr>
		<td width="1" ><img src="/be/i/design/header_l.png" /></td>
		<td width="100%" class="viewHeaderTitle">Edit menu node</td>
		<td width="1"><img src="/be/i/design/header_r.png" /></td>
	</tr>	
</table>
<table cellpadding="5" cellspacing="0" class="table" align="center" border='0'>
<col width="100">
<col width="*" >
<tbody>
<tr>
    <th align="right"><label>ID =</label></th>
    <th align='left'><?=$_REQUEST['id'];?></th>
</tr>

<tr>
    <td align="right"><label for="value">Value</label></td>
    <td><ITTI field_name="value" style='width:95%'></ITTI></td>
</tr>
   
<tr>
    <td align="right"><label for="visible">Visible</label></td>
    <td><ITTI field_name="visible" ></ITTI></td>
</tr>
<tr>
    <td align="right"><label for="is_crumb_visible">Crumb Visible</label></td>
    <td><ITTI field_name="is_crumb_visible" ></ITTI></td>
</tr>

<tr>
    <td align="right"><label for="use_in_search">Use in search</label></td>
    <td><ITTI field_name="use_in_search" ></ITTI></td>
</tr>
  <? if($GLOBALS['show_language_field']) {?>
   
<tr>
    <td align="right"><label>Language</label></td>
    <td><ITTI field_name="language_id" ></ITTI></td>
</tr>
  <? } ?>
  
<tr>
    <td align="right"><label>Skin</label></td>
    <td><ITTI field_name="skin_id" ></ITTI></td>
</tr>
  
<tr>
    <td align="right"><label>Type</label></td>
    <td><ITTI field_name="type_id" ></ITTI></td>
</tr>




<tr>
    <td align="right"><label>Template</label></td>
    <td><ITTI field_name="template_id" ></ITTI></td>
</tr>

<tr>
    <td align="right"><label>Картинка</label></td>
    <td><ITTI field_name="img" ></ITTI></td>
</tr>

<?=isset($GLOBALS['tree_include_file'])?"<tr><td colspan='2'>".$GLOBALS['tree_include_file']."</td></tr>":'';?>

<tr>
    <td align="center" colspan="2"><input class="submit" type="submit" name="btSave" value="Запис" />&nbsp;<input type="button" class="submit" value="Назад" onclick="self.location='index.php?node=<?=$_REQUEST['id'];?>';" /> &nbsp;&nbsp;<input type="submit" name="btDelete" value="Изтрий" <?=$GLOBALS['delete_message'];?> class="delete_button" /></td>
</tr>
</tbody>
</table>
    