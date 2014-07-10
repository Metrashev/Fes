/* Import plugin specific language pack */
tinyMCE.importPluginLanguagePack('template', 'en'); // <- Add a comma separated list of all supported languages

/****
 * Steps for creating a plugin from this template:
 *
 * 1. Change all "template" to the name of your plugin.
 * 2. Remove all the callbacks in this file that you don't need.
 * 3. Remove the popup.htm file if you don't need any popups.
 * 4. Add your custom logic to the callbacks you needed.
 * 5. Write documentation in a readme.txt file on how to use the plugin.
 * 6. Upload it under the "Plugins" section at sourceforge.
 *
 ****/

/**
 * Gets executed when a editor instance is initialized
 */

var TinyMCE_filemanPlugin = {
	getInfo : function() {
		return {
			longname : 'File manager',
			author : 'studioitti',
			authorurl : 'http://studioitti.com',
			infourl : 'http://studioitti.com',
			version : tinyMCE.majorVersion + "." + tinyMCE.minorVersion
		};
	},

	initInstance : function(inst) {
		//inst.addShortcut('ctrl', 'k', 'lang_advlink_desc', 'mcefileman');
	},

	getControlHTML : function(cn) {
		switch (cn) {
			case "fileman":
				return tinyMCE.getButtonHTML(cn, 'lang_fileman_desc', '{$pluginurl}/images/fileman.gif', 'mcefileman');
			case "fileman2":
				return tinyMCE.getButtonHTML(cn, 'lang_fileman2_desc', '{$pluginurl}/images/fileman2.gif', 'mcefileman2');
		}

		return "";
	},

	execCommand : function(editor_id, element, command, user_interface, value) {
		switch (command) {
			case "mcefileman": {
			// Show UI/Popup
		//	if (user_interface) {
				// Open a popup window and send in some custom data in a window argument
				var fileman = new Array();
				fileman['file'] = '../../plugins/fileman/popup.php?selector=parent.opener.insertMyImage('; // Relative to theme
				fileman['width'] = 600;
				fileman['height'] =700;

				tinyMCE.openWindow(fileman, {editor_id : editor_id, resizable: 'yes', scrollbars: 'yes'});

				// Let TinyMCE know that something was modified
				tinyMCE.triggerNodeChange(false);
		//	} else {
				// Do a command this gets called from the fileman popup
		//		alert("execCommand: mcefileman gets called from popup.");
		//	}

			return true;
		}
			case "mcefileman2": {
			//if (user_interface) {
					// Open a popup window and send in some custom data in a window argument
					var fileman = new Array();
					fileman['file'] = "../../plugins/fileman/popup.php?selector=parent.opener.insertMyLink('"+editor_id+"',"; // Relative to theme
					fileman['width'] = 600;
					fileman['height'] =700;
	
					var doc=tinyMCE.getInstanceById(editor_id).getDoc();
					var a=expandLinkSelection(doc);
					
					tinyMCE.openWindow(fileman, {editor_id : editor_id, resizable: 'yes', scrollbars: 'yes'});
					
					// Let TinyMCE know that something was modified
					tinyMCE.triggerNodeChange(false);
			//	} else {
					// Do a command this gets called from the fileman popup
			//		alert("execCommand: mcefileman gets called from popup.");
			//	}
	
				return true;
			}
		}

		return false;
	},

	handleNodeChange : function(editor_id, node, undo_index, undo_levels, visual_aid, any_selection) {
		
		tinyMCE.switchClass(editor_id + '_fileman', 'mceButtonNormal');

	// Select fileman button if parent node is a strong or b
		if (node.parentNode.nodeName == "STRONG" || node.parentNode.nodeName == "B")
			//tinyMCE.switchClassSticky(editor_id + '_fileman', 'mceButtonSelected');
			tinyMCE.switchClass(editor_id + '_fileman', 'mceButtonSelected');
		
		return true;
}
}
;

tinyMCE.addPlugin("fileman", TinyMCE_filemanPlugin);
//tinyMCE.addPlugin("fileman2", TinyMCE_fileman2Plugin);


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

function insertMyImage(href,filename,size) {
	//tinyMCE.insertImage(href,'',0);
	//tinyMCE.execCommand('mceInsertContent',false,'custom html'); 
	tinyMCE.themes['advanced']._insertImage(href,'',0);
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

