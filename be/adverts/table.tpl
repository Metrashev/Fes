<html>
	<table id="dg_adverts" class="test1 list_table" cellspacing="0" cellpadding="0" border="0">
	<thead>
	<tr class="list_header">
			<td in_index="1" id='_id' field_name="id" class="header_add"><a href='/be/adverts/edit.php?<?=FE_Utils::getBackLink();?>'>Нов</a></td>
<td in_index="2"  id='_advertiser' field_name="advertiser" class="header_nor"><a order="advertiser">Рекламодател</a></td>
<td in_index="3"  id='_active_from_date' field_name="active_from_date"  class="header_nor"><a order="active_from_date">от</a></td>
<td in_index="4"  id='_active_to_date' field_name="active_to_date"  class="header_nor"><a order="active_to_date">до</a></td>
<td in_index="5"  id='_position_id' field_name="position_id"  class="header_nor"><a order="position_id">позиция</a></td>
<td in_index="6"  id='_ad_type_id' field_name="ad_type_id"  class="header_nor"><a order="ad_type_id">тип</a></td>

<td in_index="8"  id='_ad_link' field_name="ad_link"  class="header_nor"><a order="ad_link">URL</a></td>
<td in_index="9"  id='_target' field_name="target"  class="header_nor"><a order="target">цел</a></td>

<td in_index="11"  id='_num_views' field_name="num_views"  class="header_nor"><a order="num_views">виждания</a></td>
<td in_index="12"  id='_num_clicks' field_name="num_clicks"  class="header_nor"><a order="num_clicks">кликания</a></td>
<td in_index="13" id='t13' field_name="id"  class="header_nor"><a>Изтрий</a></td>
</tr>
	</thead>
	<tbody>
	<tr>
			<td>
<? if(!isset($_GET['search'])) { ?>
			<a field_name="id" href='/be/adverts/edit.php?id=_#VAL#_&amp;<?=FE_Utils::getBackLink();?>'>Редакция</a>
		<?} else { ?>
			<input type="checkbox" name="_#CONTROL#_[fields][_ch_sel_][_#UNIQUE#_]" value="1" />
		<? } ?>
		</td>
			<td><ITTI field_name="advertiser"   ></ITTI></td>
			<td><ITTI field_name="active_from_date"   format="%d/%m/%Y"></ITTI></td>
			<td><ITTI field_name="active_to_date"   format="%d/%m/%Y"></ITTI></td>
			<td><ITTI field_name="position_id" arrayname="AdsPositions"  ></ITTI></td>
			<td><ITTI field_name="ad_type_id" arrayname="AdsTypes"  ></ITTI></td>

			<td><ITTI field_name="ad_link"   ></ITTI></td>
			<td><ITTI field_name="target"   ></ITTI></td>

			<td align="right"><ITTI field_name="num_views"   ></ITTI></td>
			<td align="right"><ITTI field_name="num_clicks"   ></ITTI></td>
<td><a field_name="id" href='#' onclick='if(window.confirm("Сигурни ли сте?")) {document.getElementById("hdDelete").value="_#VAL#_";getParentFormElement(this).submit();} else return false;'>Изтрий</a></td>
</tr>
	</tbody>
	</table>
</html>