(function() {
	tinymce.PluginManager.requireLangPack('wpflow');
	tinymce.create('tinymce.plugins.FlowPlugin', {
		init : function(ed, url) {
			ed.addCommand('mceFlowInsert', function() {
				ed.execCommand('mceInsertContent', 0, insertFlow('visual', ''));
			});
			ed.addButton('wpflow', {
				title: 'Insert Flow Video',
				cmd : 'mceFlowInsert',
				image : url + '/img/wpflow.gif'
			});
			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('wpflow', n.nodeName == 'IMG');
			});
		},

		createControl : function(n, cm) {
			return null;
		},
		getInfo : function() {
			return {
				longname : 'wordpress-flowplayer',
				author : 'Lee Ferrett',
				authorurl : 'http://jpegserv.com',
				infourl : 'http://jpegserv.com/video-on-your-blog-with-flowplayer-wordpress-plugin/',
				version : '0.3'
			};
		}
	});
	tinymce.PluginManager.add('wpflow', tinymce.plugins.FlowPlugin);
})();