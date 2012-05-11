(function() {
    tinymce.create('tinymce.plugins.rtprettifier', {
        init : function(d, url) {
                    var plugin_url = url.replace('/js', '');
                    d.addButton('rtprettifier', {
                        title : 'rtPrettify',
                        image : plugin_url+'/img/rtprettify.png',
                        cmd : 'mcertPrettify'

                    });
                    d.addCommand("mcertPrettify",function(){d.windowManager.open({file:plugin_url+"/htm/rtprettify.htm",
                        width:parseInt(500),
                        height:parseInt(350),
                        inline:1})});
                },
        createControl : function(n, cm) {
                            return null;
                        },
        getInfo : function() {
                        return {
                            longname : "rtPrettifier",
                            author : 'rtCamp',
                            authorurl : 'http://rtcamp.com/',
                            infourl : 'http://rtcamp.com/',
                            version : "1.0"
                        };
                    }
    });
    tinymce.PluginManager.add('rtprettifier', tinymce.plugins.rtprettifier);
})();