<script language="javascript" type="text/javascript" src="/be/tiny_mce/tiny_mce.js"></script>
<!--<script language="javascript" type="text/javascript" src="/be/tiny_mce/tiny_mce_gzip.php"></script>-->
<script language="javascript" type="text/javascript">
	// Notice: The simple theme does not use all options some of them are limited to the advanced theme
	tinyMCE.init({
		mode : "exact",
		elements : "_#BODY#_",
		theme : "advanced",
		//plugins : "table,contextmenu,paste,fileman,internallink,templates,advlink,preview,advimage,flash,graphics",
		plugins : "table,contextmenu,paste,fileman,internallink,templates,advlink,preview,advimage,flash",
		theme_advanced_buttons1 : "bold, italic,underline,separator,justifyleft,justifycenter,justifyright,justifyfull,separator,bullist,numlist,separator,outdent,indent,separator,undo,redo,separator,link,unlink,anchor,preview",
		theme_advanced_buttons2 : "formatselect,separator,styleselect,separator,removeformat,separator,pastetext,pasteword,selectall,separator,fileman,table,code,internallink,fileman2,flash",
		//theme_advanced_buttons2 : "formatselect,separator,styleselect,separator,removeformat,separator,pastetext,pasteword,selectall,separator,fileman,table,code,internallink,fileman2,flash,graphics",
		theme_advanced_buttons3 : "",
		content_css : "/lib_be.css",
		
		relative_urls : false,
		remove_script_host : true,
		document_base_url :'/',
		convert_urls : true,
		
		lang_fileman_desc:'Insert image',
		lang_fileman2_desc:'Insert .doc, .pdf or .zip for download',
		lang_internallink_desc:'Insert internal link',
		theme_advanced_styles : "Title=title;Subtitle=subtitle;ImgLeft=ImgLeft;ImgRight=ImgRight;Link1=spLink1",
		custom_undo_redo : false,
		//verify_html : true,	//za validen html - default e true
		theme_advanced_toolbar_location : "top",
		theme_advanced_path_location : "bottom",
		extended_valid_elements : "a[name|href|target|title|onclick],map[*],area[shape|coords|href|title|target],object[*],param[*],ittiscript[*],input[*],script[*],embed[*],div[*]",
		//valid_elements : "*[*]",
		plugin_preview_width : "500",
		plugin_preview_height : "600"


	});
</script>