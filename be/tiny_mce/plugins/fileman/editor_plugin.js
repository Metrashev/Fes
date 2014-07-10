/**
 * $Id: editor_plugin_src.js 827 2008-04-29 15:02:42Z spocke $
 *
 * @author Moxiecode
 * @copyright Copyright © 2004-2008, Moxiecode Systems AB, All rights reserved.
 */

(function() {
	// Load plugin specific language pack
	//tinymce.PluginManager.requireLangPack('example');

	tinymce.create('tinymce.plugins.filemanPlugin', {
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
			ed.addCommand('mcefileman', function() {
				
				ed.windowManager.open({
					file : url+'/popup.php?selector=parent.opener.insertMyImage("'+ed.id+'",',
					width : 600,
					height : 700,
					inline : 1
				}, {
					plugin_url : url, // Plugin absolute URL
					some_custom_arg : 'custom arg' // Custom argument
				});
			});
			ed.addCommand('mcefileman2', function() {
				
				ed.windowManager.open({
					file : url+'/popup.php?selector=parent.opener.insertMyLink("'+ed.id+'",',
					width : 600,
					height : 700,
					inline : 1
				}, {
					plugin_url : url, // Plugin absolute URL
					some_custom_arg : 'custom arg' // Custom argument
				});
			});

			// Register example button
			
			ed.addButton('fileman', {
				title : 'File manager',
				cmd : 'mcefileman',
				image : url + '/images/fileman.gif'
			});
			ed.addButton('fileman2', {
				title : 'File manager2',
				cmd : 'mcefileman2',
				image : url + '/images/fileman2.gif'
			});
			

			// Add a node change handler, selects the button in the UI when a image is selected
			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('fileman', n.nodeName == 'IMG');
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
	tinymce.PluginManager.add('fileman', tinymce.plugins.filemanPlugin);
})();






//
//tinyMCE.importPluginLanguagePack('template', 'en'); // <- Add a comma separated list of all supported languages
//
//var TinyMCE_filemanPlugin = {
//	getInfo : function() {
//		return {
//			longname : 'File manager',
//			author : 'studioitti',
//			authorurl : 'http://studioitti.com',
//			infourl : 'http://studioitti.com',
//			version : tinyMCE.majorVersion + "." + tinyMCE.minorVersion
//		};
//	},
//
//	initInstance : function(inst) {
//		//inst.addShortcut('ctrl', 'k', 'lang_advlink_desc', 'mcefileman');
//	},
//
//	getControlHTML : function(cn) {
//		switch (cn) {
//			case "fileman":
//				return tinyMCE.getButtonHTML(cn, 'lang_fileman_desc', '{$pluginurl}/images/fileman.gif', 'mcefileman');
//			case "fileman2":
//				return tinyMCE.getButtonHTML(cn, 'lang_fileman2_desc', '{$pluginurl}/images/fileman2.gif', 'mcefileman2');
//		}
//
//		return "";
//	},
//
//	execCommand : function(editor_id, element, command, user_interface, value) {
//		switch (command) {
//			case "mcefileman": {
//			// Show UI/Popup
//		//	if (user_interface) {
//				// Open a popup window and send in some custom data in a window argument
//				var fileman = new Array();
//				fileman['file'] = '../../plugins/fileman/popup.php?selector=parent.opener.insertMyImage('; // Relative to theme
//				fileman['width'] = 600;
//				fileman['height'] =700;
//
//				tinyMCE.openWindow(fileman, {editor_id : editor_id, resizable: 'yes', scrollbars: 'yes'});
//
//				// Let TinyMCE know that something was modified
//				tinyMCE.triggerNodeChange(false);
//		//	} else {
//				// Do a command this gets called from the fileman popup
//		//		alert("execCommand: mcefileman gets called from popup.");
//		//	}
//
//			return true;
//		}
//			case "mcefileman2": {
//			//if (user_interface) {
//					// Open a popup window and send in some custom data in a window argument
//					var fileman = new Array();
//					fileman['file'] = "../../plugins/fileman/popup.php?selector=parent.opener.insertMyLink('"+editor_id+"',"; // Relative to theme
//					fileman['width'] = 600;
//					fileman['height'] =700;
//	
//					var doc=tinyMCE.getInstanceById(editor_id).getDoc();
//					var a=expandLinkSelection(doc);
//					
//					tinyMCE.openWindow(fileman, {editor_id : editor_id, resizable: 'yes', scrollbars: 'yes'});
//					
//					// Let TinyMCE know that something was modified
//					tinyMCE.triggerNodeChange(false);
//			//	} else {
//					// Do a command this gets called from the fileman popup
//			//		alert("execCommand: mcefileman gets called from popup.");
//			//	}
//	
//				return true;
//			}
//		}
//
//		return false;
//	},
//
//	handleNodeChange : function(editor_id, node, undo_index, undo_levels, visual_aid, any_selection) {
//		
//		tinyMCE.switchClass(editor_id + '_fileman', 'mceButtonNormal');
//
//	// Select fileman button if parent node is a strong or b
//		if (node.parentNode.nodeName == "STRONG" || node.parentNode.nodeName == "B")
//			//tinyMCE.switchClassSticky(editor_id + '_fileman', 'mceButtonSelected');
//			tinyMCE.switchClass(editor_id + '_fileman', 'mceButtonSelected');
//		
//		return true;
//}
//}
//;
//
//tinyMCE.addPlugin("fileman", TinyMCE_filemanPlugin);
////tinyMCE.addPlugin("fileman2", TinyMCE_fileman2Plugin);
//
//
function getFileSize(size) {
	if(size<1024) {
		return size+' bytes';
	}
	if(size<1048576) {
		return Math.round(size/1024)+"Kb";
	}
	return Math.round(size/1048576)+"Mb";
}

function getFileExt(filename) {
	var s=filename.lastIndexOf(".");
	if(s==-1)
		return '';
	if(s>=filename.length-1)
		return -1;
	s=filename.substr(s+1,filename.length-1);
	return s;
}

function insertMyImage(editor_id,href,size) {
	//tinyMCE.insertImage(href,'',0);
//	alert(href);
//	alert(filename);
	
	tinyMCE.execCommand('mceInsertContent',false,'<img src="'+href+'" alt="" />'); 
	//tinyMCE.themes['advanced']._insertImage(href,'',0);
}

function insertMyLink(editor_id,href,filename,filesize) {
	
	var conf =new Array (
		new Array('pdf','pdfStyle','свали pdf',true),
		new Array('doc','docStyle','свали doc',true),
		new Array('zip','zipStyle','свали zip',true)
		);
	/*conf[0]= {ext:'pdf',
			 style : 'pdfStyle',
			text : 'свали pdf',
			showFileSize: true
	};*/
	var style=null;
	var ext=getFileExt(filename).toLowerCase();
	if(ext!='') {
		for(i=0;i<conf.length;i++) {
			if(ext==conf[i][0]) {
				filename=conf[i][2];
				style=conf[i][1];
				if(conf[i][3])
					filename+=" ("+getFileSize(filesize)+")";
				break;
			}
		}
	}
	
	var doc=tinyMCE.getInstanceById(editor_id).getDoc();
	expandLinkSelection(doc);
	createLink(doc,href,filename, '_blank',style);
}

