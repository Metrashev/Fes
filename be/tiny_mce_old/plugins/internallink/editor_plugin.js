/* Import plugin specific language pack */
tinyMCE.importPluginLanguagePack('template', 'en'); // <- Add a comma separated list of all supported languages


var TinyMCE_internallinkPlugin = {
	getInfo : function() {
		return {
			longname : 'Internal link',
			author : 'StudioITTI',
			authorurl : 'http://studioitti.com',
			infourl : 'http://studioitti.com',
			version : tinyMCE.majorVersion + "." + tinyMCE.minorVersion
		};
	},

	initInstance : function(inst) {
		//inst.addShortcut('ctrl', 'k', 'lang_advlink_desc', 'mceAdvLink');
	},

	getControlHTML : function(cn) {
		switch (cn) {
			case "internallink":
				return tinyMCE.getButtonHTML(cn, 'lang_internallink_desc', '{$pluginurl}/images/internallink.gif', 'mceinternallink');
		}

		return "";
	},

	execCommand : function(editor_id, element, command, user_interface, value) {
		switch (command) {
			case "mceinternallink":
				var internallink = new Array();
				var nc='';
				try {
					nc='n_cid='+document.getElementById('n_cid').value+'&';
				}
				catch(e) {
					n_cid='';
				}
				// internallink['file'] = '../../plugins/internallink/popup.php?selector=parent.opener.tinyMCE.insertLink'; // Relative to theme
				internallink['file'] = "../../plugins/internallink/popup.php?"+nc+"selector=parent.opener.TinyMCE_internallink_insertMyLink('"+editor_id+"',"; // Relative to theme
				internallink['width'] = 750;
				internallink['height'] =600;
				
				var doc=tinyMCE.getInstanceById(editor_id).getDoc();
				var a=expandLinkSelection(doc);

				tinyMCE.openWindow(internallink, {editor_id : editor_id, resizable: 'yes', scrollbars: 'yes'});
				// Let TinyMCE know that something was modified
				tinyMCE.triggerNodeChange(false);
				return true;
		}

		return false;
	},

	handleNodeChange : function(editor_id, node, undo_index, undo_levels, visual_aid, any_selection) {
		tinyMCE.switchClass(editor_id + '_internallink', 'mceButtonNormal');

	// Select internallink button if parent node is a strong or b
	if (node.parentNode.nodeName == "STRONG" || node.parentNode.nodeName == "B")
		//tinyMCE.switchClassSticky(editor_id + '_internallink', 'mceButtonSelected');
		tinyMCE.switchClass(editor_id + '_internallink', 'mceButtonSelected');

	return true;
	}
};

function TinyMCE_internallink_insertMyLink(editor_id,href,text) {
   text = text.replace(/&quot;/g, '"');
	var doc=tinyMCE.getInstanceById(editor_id).getDoc();
	expandLinkSelection(doc);
	createLink(doc,href,text, '','');
}

tinyMCE.addPlugin("internallink", TinyMCE_internallinkPlugin);
