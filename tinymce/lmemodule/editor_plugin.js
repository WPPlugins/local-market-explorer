tinymce.create('tinymce.plugins.lmeModule', {
	init : function(ed, url) {
		ed.addCommand('lme-module', function() {
			ed.windowManager.open({
				file : url + '/dialog.php?ajaxurl='+ajaxurl,
				width : 600,
				height : 400,
				inline : 1
			}, {
				plugin_url : url
			});
		});
		ed.addButton('lmemodule', {
			title : 'Insert a Local Market Explorer module for a location',
			cmd : 'lme-module',
			image : url + '/map.png'
		});
		ed.onNodeChange.add(function(ed, cm, n) {
			cm.setActive('lmemodule', !tinymce.isIE && /^\[lme-module /.test(n.innerHTML));
		});
	},
	createControl : function(n, cm) {
		return null;
	},
	getInfo : function() {
		return {
			longname : 'Insert a Local Market Explorer module for a location',
			author : 'Andrew Mattie and Jonathan Mabe',
			infourl : 'javascript:void(0)',
			version : '1.0'
		};
	}
});
tinymce.PluginManager.add('lmemodule', tinymce.plugins.lmeModule);