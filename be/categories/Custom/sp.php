<hr />
<label for="has_gallery">Add Gallery</label> 
<input value="1" id="has_gallery" type="checkbox" onclick="getForm(this).submit()" name="data[sp][has_gallery]" <?=isset($data['sp']['has_gallery'])?"checked":"";?> />

<? if(isset($data['sp']['has_gallery'])) {
include(dirname(__FILE__).'/gallery.php');
}
?>