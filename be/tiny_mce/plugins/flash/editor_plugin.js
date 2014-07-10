
(function() {
	tinymce.create('tinymce.plugins.FlashPlugin', {
		init : function(ed, url) {
			// Register commands
			ed.addCommand('mceFlash', function() {
				ed.windowManager.open({
					file : url + '/flash.htm',
					width : 500  ,
					height : 500 ,
					inline : 1
				}, {
					plugin_url : url
				});
			});

			// Register buttons
			ed.addButton('flash', {
				title : 'SWF Flash',
				cmd : 'mceFlash',
				image : url + '/images/flash.gif'
			});
		},

		getInfo : function() {
			return {
				longname : 'Flash',
				author : 'Studioitti',
				authorurl : 'http://studioitti.com',
				infourl : 'http://studioitti.com',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('flash', tinymce.plugins.FlashPlugin);
})();