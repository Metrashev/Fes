<html>
	<table id="dg_gallery" class="test1 list_table" cellspacing="0" cellpadding="0" border="0">
	<thead>
	<tr class="list_header">
			<td in_index="1" id='_id'  field_name="id" class="header_add"><a href='/be/gallery/edit.php?cid=<?=$_GET['cid'];?>&amp;page_id=<?=$_GET['page_id'];?>&amp;<?=FE_Utils::getBackLink();?>'>Нов</a></td>
<td in_index="4"  id='_img' field_name="img"  class="header_nor"><a order="img">Снимка</a></td>
<td in_index="5"  id='_text' field_name="text"  class="header_nor"><a order="text">Текст</a></td>
<td in_index="6"  id='_order_field' field_name="order_field"  class="header_nor"><a order="order_field">Подредба</a></td>
<td in_index="7" id='t7' field_name="id"  class="header_nor"><a>Изтрий</a></td>
</tr>
	</thead>
	<tbody>
	<tr>
			<td>
<? if(!isset($_GET['search'])) { ?>
			<a field_name="id" href='/be/gallery/edit.php?cid=<?=$_GET['cid'];?>&amp;page_id=<?=$_GET['page_id'];?>&amp;id=_#VAL#_&amp;<?=FE_Utils::getBackLink();?>'>Редакция</a>
		<?} else { ?>
			<input type="checkbox" name="_#CONTROL#_[fields][_ch_sel_][_#UNIQUE#_]" value="1" />
		<? } ?>
		</td>
			<td><ITTI field_name="img" class="CGallery_Img" userfunc="getImgLink" encode_chars="false"></ITTI></td>
			<td><ITTI field_name="text"></ITTI></td>
			<td><ITTI field_name="order_field"></ITTI></td>
<td><a field_name="id" href='#' onclick='if(window.confirm("Сигурни ли сте?")) {document.getElementById("hdDeletegallery").value="_#VAL#_";getParentFormElement(this).submit();} else return false;'>Изтрий</a></td>
</tr>
	</tbody>
	</table>
</html>