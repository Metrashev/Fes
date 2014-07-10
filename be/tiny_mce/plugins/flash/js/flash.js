//tinyMCEPopup.requireLangPack();

var FlashDialog = {
	init : function(ed) {
		tinyMCEPopup.resizeToInnerSize();
	},

	insert : function(file, title) {
		var ed = tinyMCEPopup.editor, dom = ed.dom;
		var i;
		var s="";
		for(i=1;i<=4;i++) {
			if(document.getElementById('key'+i).value!="") {
				s+='fo.addVariable("'+document.getElementById('key'+i).value+'", "'+document.getElementById('value'+i).value+'");';
			}
		}
		tinyMCEPopup.execCommand('mceInsertContent', false,'<div id="'+document.getElementById('container_id').value+'" style="width:'+document.getElementById('width').value+'px;height:'+document.getElementById('height').value+'px;"><div style="background:#FFFFC0;border:1px dashed #999999;width:'+document.getElementById('width').value+'px;height:'+document.getElementById('height').value+'px;"><img src="'+tinyMCEPopup.getWindowArg('plugin_url') + '/images/flash.gif" alt="" /></div></div>'+
		'<script type="text/javascript">var fo=new FlashObject("'+
		document.getElementById('url').value+'", "flash", "'+document.getElementById('width').value+
		'", "'+document.getElementById('height').value+'", "'+document.getElementById('version').value+'");'+
		(document.getElementById('wmode').value!=''?'fo.addParam("wmode", "'+document.getElementById('wmode').value+'");':'')+
		(document.getElementById('scriptAccess').value!=''?'fo.addParam("scriptAccess", "'+document.getElementById('scriptAccess').value+'");':'')+
		s+'fo.write("'+document.getElementById('container_id').value+'");</script>');
	/*	tinyMCEPopup.execCommand('mceInsertContent', false, dom.createHTML('img', {
			src : tinyMCEPopup.getWindowArg('plugin_url') + '/img/' + file,
			alt : ed.getLang(title),
			title : ed.getLang(title),
			border : 0
		}));*/

		tinyMCEPopup.close();
	}
};

tinyMCEPopup.onInit.add(FlashDialog.init, FlashDialog);