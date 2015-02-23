var script = document.createElement('script');
script.textContent = 'HTMLMediaElement.prototype.canPlayType = function(fileType){return "probably";};';
(document.head||document.documentElement).appendChild(script);