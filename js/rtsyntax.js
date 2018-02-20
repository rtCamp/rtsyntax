(function() {
    tinymce.create('tinymce.plugins.rtsyntax', {
        init : function(d, url) {
                    var plugin_url = url.replace('/js', '');
                    d.addButton('rtsyntax', {
                        title : 'rtSyntax',
                        image : plugin_url+'/img/rtsyntax.png',
                        cmd : 'mcertSyntax'

                    });
                    d.addCommand("mcertSyntax",function(){d.windowManager.open({file:plugin_url+"/html/rtsyntax.html",
                        width:parseInt(500),
                        height:parseInt(300),
                        inline:1})});
                },
        createControl : function(n, cm) {
                            return null;
                        },
        getInfo : function() {
                        return {
                            longname : "rtSyntax",
                            author : 'rtCamp',
                            authorurl : 'http://rtcamp.com/',
                            infourl : 'http://rtcamp.com/',
                            version : "1.0"
                        };
                    }
    });
    tinymce.create('tinymce.plugins.rtcode', {
        init : function(d, url) {
                    var plugin_url = url.replace('/js', '');
                    d.addButton('rtcode', {
                        title : 'code',
                        image : plugin_url+'/img/rtcode.png',
                        cmd : 'mceCode'

                    });
                    d.addCommand("mceCode",function(ui, v) {
                        d.execCommand('mceInsertContent', true, '<code>'+d.selection.getContent()+'</code>');
                    });
                },
        createControl : function(n, cm) {
                            return null;
                        },
        getInfo : function() {
                        return {
                            longname : "code",
                            author : 'rtCamp',
                            authorurl : 'http://rtcamp.com/',
                            infourl : 'http://rtcamp.com/',
                            version : "1.0"
                        };
                    }
    });
    tinymce.create('tinymce.plugins.rtkey', {
        init : function(d, url) {
                    var plugin_url = url.replace('/js', '');
                    d.addButton('rtkey', {
                        title : 'key',
                        image : plugin_url+'/img/rtkey.png',
                        cmd : 'mceKey'

                    });
                    d.addCommand("mceKey",function(ui, v) {
                        d.execCommand('mceInsertContent', true, '<span class="code-key">'+d.selection.getContent()+'</span>');
                    });
                },
        createControl : function(n, cm) {
                            return null;
                        },
        getInfo : function() {
                        return {
                            longname : "Key",
                            author : 'rtCamp',
                            authorurl : 'http://rtcamp.com/',
                            infourl : 'http://rtcamp.com/',
                            version : "1.0"
                        };
                    }
    });
    tinymce.PluginManager.add('rtsyntax', tinymce.plugins.rtsyntax);
    tinymce.PluginManager.add('rtcode', tinymce.plugins.rtcode);
    tinymce.PluginManager.add('rtkey', tinymce.plugins.rtkey);
})();