<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8">
<table class="viewHeader" cellpadding="0" cellspacing="0">
	<tr>
		<td width="1"><img src="/be/i/design/header_l.png" /></td>
		<td width="100%" class="viewHeaderTitle">News</td>
		<td width="1"><img src="/be/i/design/header_r.png" /></td>
	</tr>
	<tr><td  class="viewList" colspan="4"><div>Търси</div></td></tr>
</table>
<table cellpadding="5" cellspacing="0" class="table" align="center" border='0'>
<colgroup span="4" width="0*">
<col width='15%' align='right'>
<col width='35%*' align='left'>
<col width='15%' align='right'>
<col width='35%*' align='left'>
</colgroup>
<tbody>
<tr>
<td><label for="due_date">Due date</label></td>
<td><ITTI field_name='due_date'></ITTI></td>
<td><label for="_to_due_date">to</label></td>
<td><ITTI field_name='_to_due_date'></ITTI></td>
</tr>
<tr>
<td><label for="keywords">Keywords</label></td>
<td><ITTI field_name='keywords'></ITTI></td>
</tr>		
	<tr>
		<td colspan="4" align="center" style="padding-right:10px;">
							<input class="submit" type="submit" name="search" value="Search" />&nbsp;&nbsp;&nbsp;<input type="submit"  class="submit" name='btClear' value="Clear" />
	</td></tr>
</tbody></table>

<div style="padding:10px;border:1px solid #ccc;margin-top:1px;">
	<a href="/be/static_pages/edit.php?loadDef=1&amp;n_cid=<?=$_GET['id']?>&bkp=<?=urlencode($_SERVER['REQUEST_URI'])?>">Edit Lead Page</a>
</div>