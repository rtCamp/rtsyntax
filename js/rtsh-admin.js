(function() {
    tinymce.create('tinymce.plugins.rtsh', {
        init : function(d, url) {
                    var plugin_url = url.replace('/js', '');
                    d.addButton('rtsh', {
                        title : 'rtSyntaxHighlighter',
                        image : plugin_url+'/img/rtsh.png',
                        cmd : 'mcertSyntaxHighlighter'

                    });
                    d.addCommand("mcertSyntaxHighlighter",function(){d.windowManager.open({file:plugin_url+"/htm/rtsh.htm",
                        width:parseInt(500),
                        height:parseInt(350),
                        inline:1})});
                },
        createControl : function(n, cm) {
                            return null;
                        },
        getInfo : function() {
                        return {
                            longname : "rtSyntaxHighlighter",
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
                            longname : "rtCode",
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
                            longname : "rtKey",
                            author : 'rtCamp',
                            authorurl : 'http://rtcamp.com/',
                            infourl : 'http://rtcamp.com/',
                            version : "1.0"
                        };
                    }
    });
    tinymce.PluginManager.add('rtsh', tinymce.plugins.rtsh);
    tinymce.PluginManager.add('rtcode', tinymce.plugins.rtcode);
    tinymce.PluginManager.add('rtkey', tinymce.plugins.rtkey);
})();