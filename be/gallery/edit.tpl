<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8">

<table class="viewHeader" cellpadding="0" cellspacing="0">
	<tr>
		<td width="1" ><img src="/be/i/design/header_l.png" /></td>
		<td width="100%" class="viewHeaderTitle">Галерия</td>
		<td width="1"><img src="/be/i/design/header_r.png" /></td>
	</tr>
	<tr><td  class="viewList" colspan="4"><div>Редакция</div></td></tr>
</table>

<table cellpadding="5" cellspacing="0" class="table" align="center" border='0'>
<colgroup span="4" width="0*">
<col width='15%' align='right'>
<col width='35%' align='left'>
<col width='15%' align='right'>
<col width='35%*' align='left'>
</colgroup>
<tbody>
	
<td><label for="img">Снимка</label></td>
<td><ITTI field_name='img'></ITTI></td>
<td><label for="order_field">Подредба</label></td>
<td><ITTI field_name='order_field'></ITTI><br />(оставете празно или 0, за да добавите като последно)</td>
</tr>
<tr>
<td><label for="text">Текст</label></td>
<td colspan="3"><ITTI field_name='text' style="width:92%"></ITTI></td>
</tr>		

<?php
if(!empty($GLOBALS['_LANGUAGE_COLS'])) {
	foreach ($GLOBALS['_LANGUAGE_COLS'] as $k=>$v) {
		echo <<<EOD
		<tr>
<td><label for="{$k}">{$v}</label></td>
<td colspan="3"><ITTI field_name='{$k}' style="width:92%"></ITTI></td>
</tr>	
EOD;
	}
}
?>

	<tr>
		<td colspan="4" align="center" style="padding-right:10px;">
							<input class="submit" type="submit" name="btSave" value="Запази" />&nbsp;&nbsp;&nbsp;<input class="submit" type="button" onclick="self.location='<?=($_GET['bkp']);?>'" value="Back" />
	</td></tr>
</tbody></table>