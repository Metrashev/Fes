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
function TinyMCE_template_initInstance(inst) {
	// You can take out plugin specific parameters
	
}

/**
 * Gets executed when a editor needs to generate a button.
 */
function TinyMCE_templates_getControlHTML(control_name) {
	switch (control_name) {
		case "templates":
			return '<img id="{$editor_id}_templates" src="{$pluginurl}/images/templates1.gif" title="Template" width="20" height="20" class="mceButtonNormal" onmouseover="tinyMCE.switchClass(this,\'mceButtonOver\');" onmouseout="tinyMCE.restoreClass(this);" onmousedown="tinyMCE.restoreAndSwitchClass(this,\'mceButtonDown\');" onClick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'mcetemplates\', true);" />';
	}

	return "";
}

/**
 * Gets executed when a command is called.
 */
function TinyMCE_templates_execCommand(editor_id, element, command, user_interface, value) {
	// Handle commands
	switch (command) {
		// Remember to have the "mce" prefix for commands so they don't intersect with built in ones in the browser.
		case "mcetemplates":
			// Show UI/Popup
			if (user_interface) {
				// Open a popup window and send in some custom data in a window argument
				var templates = new Array();
				templates['file'] = "../../plugins/templates/popup.php?selector=parent.opener.TinyMCE_templates_insertMyTemplate"; // Relative to theme
				templates['width'] = 750;
				templates['height'] =600;
				
				
				tinyMCE.openWindow(templates, {editor_id : editor_id, resizable: 'yes', scrollbars: 'yes'});
				// Let TinyMCE know that something was modified
				tinyMCE.triggerNodeChange(false);
			} else {
				// Do a command this gets called from the internallink popup
				alert("execCommand: mcetemplates gets called from popup.");
			}

			return true;
	}

	// Pass to next handler in chain
	return false;
}



function TinyMCE_templates_insertMyTemplate(html) {
	if(html) {
	 	tinyMCE.execCommand('mceInsertContent', false, html);
	}
	//tinyMCEPopup.close();
}


/**
 * Gets executed when the selection/cursor position was changed.
 */
function TinyMCE_templates_handleNodeChange(editor_id, node, undo_index, undo_levels, visual_aid, any_selection) {
	// Deselect internallink button
	//tinyMCE.switchClassSticky(editor_id + '_templates', 'mceButtonNormal');
	tinyMCE.switchClass(editor_id + '_templates', 'mceButtonNormal');

	// Select internallink button if parent node is a strong or b
	if (node.parentNode.nodeName == "STRONG" || node.parentNode.nodeName == "B")
		//tinyMCE.switchClassSticky(editor_id + '_templates', 'mceButtonSelected');
		tinyMCE.switchClass(editor_id + '_templates', 'mceButtonSelected');

	return true;
}

/**
 * Gets executed when contents is inserted / retrived.
 */
function TinyMCE_templates_cleanup(type, content) {
	switch (type) {
		case "get_from_editor":
			//alert("[FROM] Value HTML string: " + content);

			// Do custom cleanup code here

			break;

		case "insert_to_editor":
			//alert("[TO] Value HTML string: " + content);

			// Do custom cleanup code here

			break;

		case "get_from_editor_dom":
			//alert("[FROM] Value DOM Element " + content.innerHTML);

			// Do custom cleanup code here

			break;

		case "insert_to_editor_dom":
			//alert("[TO] Value DOM Element: " + content.innerHTML);

			// Do custom cleanup code here

			break;
	}

	return content;
}
