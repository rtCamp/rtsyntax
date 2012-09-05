var rtSyntaxHighlighter = {
                init : function() {
                        this.resize();
                },

                insert : function() {
                        var h = tinyMCEPopup.dom.encode(document.getElementById('content').value);

                        // Convert linebreaks into paragraphs
                                
                        var classes = 'prettyprint';    
                        var language = document.getElementById('language').value;
                        if ( 0 != language ) {
                            classes += ' lang-'+language;
                        }
                        if ( document.getElementById('lines_yes').checked ) {
                            classes += ' linenums:'+document.getElementById('line_start').value;
                        }
                        var content = '<pre class="'+classes+'">'+h+'</pre>';
                        tinyMCEPopup.editor.execCommand('mceInsertContent', false, content);
                        tinyMCEPopup.close();
                },

                resize : function() {
                        var vp = tinyMCEPopup.dom.getViewPort(window), el;

                        el = document.getElementById('content');

                        el.style.width  = (vp.w - 20) + 'px';
                        el.style.height = (vp.h - 150) + 'px';
                }
        };

        tinyMCEPopup.onInit.add(rtSyntaxHighlighter.init, rtSyntaxHighlighter);