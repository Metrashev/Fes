<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8">
<table class="viewHeader" cellpadding="0" cellspacing="0">
	<tr>
		<td width="1" ><img src="/be/i/design/header_l.png" /></td>
		<td width="100%" class="viewHeaderTitle">News</td>
		<td width="1"><img src="/be/i/design/header_r.png" /></td>
	</tr>
	<tr><td  class="viewList" colspan="4"><div>Редакция</div></td></tr>
</table>
<table cellpadding="5" cellspacing="0" class="table" align="center" border='0'>
<colgroup span="2" width="0*">
<col width='5%' align='right'>
<col width='95%*' align='left'>
</colgroup>
<tbody>
<tr>
<td><label for="title">Title</label></td>
<td><ITTI field_name='title' style="width:610px;"></ITTI></td>
</tr>
<tr>
<td><label for="subtitle">Short description</label></td>
<td><ITTI field_name='subtitle' style="width:95%"></ITTI></td>
</tr>

<?php/* if($GLOBALS['nodeData']['type_id']==255 && $GLOBALS['nodeData']['template_id']==7 ) { */?>
<tr>
<td><label for="picture">Picture</label></td>
<td><ITTI field_name='picture' style="width:95%"></ITTI></td>
</tr>


<tr>
<td><label for="due_date">Due date</label></td>
<td><ITTI field_name='due_date'></ITTI></td>
</tr>
<tr>
<td></td>
<td>
<ITTI field_name='is_visible'></ITTI> <label for="is_visible">Visible</label>
</td>
</tr>
<?php if($GLOBALS['nodeData']['type_id']==255 && $GLOBALS['nodeData']['template_id']==6 ) { ?>
<tr>
<td><label for="visible_from_date">Visible from</label></td>
<td><ITTI field_name='visible_from_date'></ITTI></td>
</tr>

<?}?>


<?php if($GLOBALS['nodeData']['type_id']==255 && $GLOBALS['nodeData']['template_id']==5 ) { ?>
<tr>
<td><label for="source">Source</label></td>
<td><ITTI field_name='source' style="width:90%;"></ITTI></td>
</tr>
<tr>
<td><label for="source_href">Link</label></td>
<td><ITTI field_name='source_href' style="width:90%;"></ITTI></td>
</tr>
<?php } ?>

<tr>
<td colspan="2" align="left"><label for="body">Body</label></td>
</tr>
<tr>
<td colspan="2"><ITTI field_name='body' style="width:100%;height:400px;"></ITTI></td>
</tr>		
	<tr>
		<td colspan="2" align="center" style="padding-right:10px;">
							<input type="submit" name="btSave" value="Запази" class="submit" />&nbsp;&nbsp;&nbsp;<input type="button" onclick="self.location='<?=($_GET['bkp']);?>'" value="Back" class="submit" />
	</td></tr>
</tbody></table>