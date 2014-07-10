<hr />

<table cellpadding="5" cellspacing="0"" width="100%" border='0'>
<col width="100">
<col width="*" >

<tr>
    <td align="right">Count</td>
<td><input type="text" name="data[ItemsPerPage]" value="<?=htmlspecialchars($data['ItemsPerPage']);?>" />
</td>
    </tr>
</table>
<hr />
<label for="has_gallery">Add Gallery</label> <input value="1" id="has_gallery" type="checkbox" onclick="getForm(this).submit()" name="data[news][has_gallery]" <?=isset($data['news']['has_gallery'])?"checked":"";?> />

<? if(isset($data['news']['has_gallery'])) {
include(dirname(__FILE__).'/Custom/gallery.php');
}
?>