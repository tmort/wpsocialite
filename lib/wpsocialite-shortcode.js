(function() {
	tinymce.create('tinymce.plugins.wpsocialite', {
		init : function(ed, url) {
			ed.addButton('wpsocialite', {
				title : 'WPSocialite',
				image : url+'/wpsocialite-tinymce.png',
				onclick : function() {
					 ed.selection.setContent('[wpsocialite]');
				}
			});
		},
		createControl : function(n, cm) {
			return null;
		}
	});
	tinymce.PluginManager.add('wpsocialite', tinymce.plugins.wpsocialite);
})();