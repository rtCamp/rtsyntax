let hljs = require('./highlight.min');

// console.log(hljs);

self.addEventListener('message',function(e){

	postMessage(hljs.highlight(e.data.language,e.data.content));

});