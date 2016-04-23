var rtSyntax = {
                init : function() {
                        this.resize();
                },

                insert : function() {
                        // Convert linebreaks into paragraphs
                        var h = tinyMCEPopup.dom.encode(document.getElementById('content').value);
                        var content;
                        var language = document.getElementById('language');
                        if ( '0' != language.value ) {
                            content = '<pre class="'+language.value+'">'+h+'</pre>';
                        } else {
                            content = '<pre class="no-highlight">'+h+'</pre>';
                        }
                            tinyMCEPopup.editor.execCommand('mceInsertContent', false, content);
                            tinyMCEPopup.close();
                },

                resize : function() {
                        var vp = tinyMCEPopup.dom.getViewPort(window), el;

                        el = document.getElementById('content');

                        el.style.width  = (vp.w - 20) + 'px';
                        el.style.height = (vp.h - 80) + 'px';
                }
        };

tinyMCEPopup.onInit.add(rtSyntax.init, rtSyntax);