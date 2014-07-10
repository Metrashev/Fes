(function() {
	// Load plugin specific language pack
	//tinymce.PluginManager.requireLangPack('example');

	tinymce.create('tinymce.plugins.internallinkPlugin', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function(ed, url) {
			
			// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceExample');
			ed.addCommand('mceinternallink', function() {
				var nc='';
				try {
					nc='n_cid='+document.getElementById('n_cid').value+'&';
				}
				catch(e) {
					n_cid='';
				}
				
				
				ed.windowManager.open({
					file : url+'/popup.php?'+nc+'selector=parent.opener.TinyMCE_internallink_insertMyLink("'+ed.id+'",',
					width : 750,
					height : 600,
					inline : 1
				}, {
					plugin_url : url, // Plugin absolute URL
					some_custom_arg : 'custom arg' // Custom argument
				});
			});
			
			// Register example button
			
			ed.addButton('internallink', {
				title : 'Internal link',
				cmd : 'mceinternallink',
				image : url + '/images/internallink.gif'
			});
			

			// Add a node change handler, selects the button in the UI when a image is selected
			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('internallink', n.nodeName == 'IMG');
			});
		},

		/**
		 * Creates control instances based in the incomming name. This method is normally not
		 * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
		 * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
		 * method can be used to create those.
		 *
		 * @param {String} n Name of the control to create.
		 * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
		 * @return {tinymce.ui.Control} New control instance or null if no control was created.
		 */
		createControl : function(n, cm) {
			return null;
		},

		/**
		 * Returns information about the plugin as a name/value array.
		 * The current keys are longname, author, authorurl, infourl and version.
		 *
		 * @return {Object} Name/value array containing information about the plugin.
		 */
		getInfo : function() {
			return {
				longname : 'File manager',
				author : 'studioitti',
				authorurl : 'http://studioitti.com',
				infourl : 'http://studioitti.com',
				version : '1.0'
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('internallink', tinymce.plugins.internallinkPlugin);
})();



///* Import plugin specific language pack */
//tinyMCE.importPluginLanguagePack('template', 'en'); // <- Add a comma separated list of all supported languages
//
//
//var TinyMCE_internallinkPlugin = {
//	getInfo : function() {
//		return {
//			longname : 'Internal link',
//			author : 'StudioITTI',
//			authorurl : 'http://studioitti.com',
//			infourl : 'http://studioitti.com',
//			version : tinyMCE.majorVersion + "." + tinyMCE.minorVersion
//		};
//	},
//
//	initInstance : function(inst) {
//		//inst.addShortcut('ctrl', 'k', 'lang_advlink_desc', 'mceAdvLink');
//	},
//
//	getControlHTML : function(cn) {
//		switch (cn) {
//			case "internallink":
//				return tinyMCE.getButtonHTML(cn, 'lang_internallink_desc', '{$pluginurl}/images/internallink.gif', 'mceinternallink');
//		}
//
//		return "";
//	},
//
//	execCommand : function(editor_id, element, command, user_interface, value) {
//		switch (command) {
//			case "mceinternallink":
//				var internallink = new Array();
//				var nc='';
//				try {
//					nc='n_cid='+document.getElementById('n_cid').value+'&';
//				}
//				catch(e) {
//					n_cid='';
//				}
//				// internallink['file'] = '../../plugins/internallink/popup.php?selector=parent.opener.tinyMCE.insertLink'; // Relative to theme
//				internallink['file'] = "../../plugins/internallink/popup.php?"+nc+"selector=parent.opener.TinyMCE_internallink_insertMyLink('"+editor_id+"',"; // Relative to theme
//				internallink['width'] = 750;
//				internallink['height'] =600;
//				
//				var doc=tinyMCE.getInstanceById(editor_id).getDoc();
//				var a=expandLinkSelection(doc);
//
//				tinyMCE.openWindow(internallink, {editor_id : editor_id, resizable: 'yes', scrollbars: 'yes'});
//				// Let TinyMCE know that something was modified
//				tinyMCE.triggerNodeChange(false);
//				return true;
//		}
//
//		return false;
//	},
//
//	handleNodeChange : function(editor_id, node, undo_index, undo_levels, visual_aid, any_selection) {
//		tinyMCE.switchClass(editor_id + '_internallink', 'mceButtonNormal');
//
//	// Select internallink button if parent node is a strong or b
//	if (node.parentNode.nodeName == "STRONG" || node.parentNode.nodeName == "B")
//		//tinyMCE.switchClassSticky(editor_id + '_internallink', 'mceButtonSelected');
//		tinyMCE.switchClass(editor_id + '_internallink', 'mceButtonSelected');
//
//	return true;
//	}
//};
//
function TinyMCE_internallink_insertMyLink(editor_id,href,text) {
   text = text.replace(/&quot;/g, '"');
	var doc=tinyMCE.getInstanceById(editor_id).getDoc();
	expandLinkSelection(doc);
	createLink(doc,href,text, '','');
}

//tinyMCE.addPlugin("internallink", TinyMCE_internallinkPlugin);
