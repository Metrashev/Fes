<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8">
<table class="viewHeader" cellpadding="0" cellspacing="0">
	<tr>
		<td width="1" ><img src="/be/i/design/header_l.png" /></td>
		<td width="100%" class="viewHeaderTitle">Реклама</td>
		<td width="1"><img src="/be/i/design/header_r.png" /></td>
	</tr>
	<tr><td  class="viewList" colspan="4"><div>Редакция</div></td></tr>
</table>
<table cellpadding="5" cellspacing="0" class="table" align="center" border='0'>
<colgroup span="2" width="0*">
<col width='50%' align='right'>
<col width='50%*' align='left'>
</colgroup>
<tbody>
	
<tr>
<td><label for="advertiser">Рекламодател</label></td>
<td><ITTI field_name='advertiser'></ITTI></td>
</tr>
<tr>
<td><label for="active_from_date">активен от</label></td>
<td><ITTI field_name='active_from_date'></ITTI></td>
</tr>
<tr>
<td><label for="active_to_date">активен до</label></td>
<td><ITTI field_name='active_to_date'></ITTI></td>
</tr>
<tr>
<td><label onclick="document.getElementById('position_id').focus();">позиция</label></td>
<td><ITTI field_name='position_id' onChange="this.form.submit();"></ITTI></td>
</tr>
<tr>
<td><label onclick="document.getElementById('ad_type_id').focus();">Тип</label></td>
<td><ITTI field_name='ad_type_id'></ITTI></td>
</tr>
<tr>
<td valign="top"><label for="ad_image">картинка</label></td>
<td><ITTI field_name='ad_image'></ITTI></td>
</tr>
<tr>
<td valign="top"><label for="ad_file">flash файл</label></td>
<td><ITTI field_name='ad_file'></ITTI></td>
</tr>
<tr>
<td><label for="ad_link">URL адрес</label></td>
<td><ITTI field_name='ad_link'></ITTI></td>
</tr>
<tr>
<td><label for="target">отваряне в</label></td>
<td><ITTI field_name='target'></ITTI></td>
</tr>


	<tr>
		<td colspan="2" align="center" style="padding-right:10px;">
							<input type="submit" class="submit" name="btSave" value="Запази" />&nbsp;&nbsp;&nbsp;<input class="submit" type="button" onclick="self.location='<?=($_GET['bkp']);?>'" value="Back" />
	</td></tr>
</tbody></table>