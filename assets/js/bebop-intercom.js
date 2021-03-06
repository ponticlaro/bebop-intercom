;(function(window, document, undefined, $){

    // Check if we're inside an iframe
    var isInIframe = window.frameElement && window.frameElement.nodeName == "IFRAME";

    // Load intercom only if not inside an iframe
    if (!isInIframe) {

        // Load Intercom Javascript library
        (function(){
        	var w=window;
        	var ic=w.Intercom;
        	if(typeof ic==="function"){
        		ic('reattach_activator');
        		ic('update', window.intercomSettings);
        	}

        	else{
        		var d=document;
        		var i=function(){
        			i.c(arguments);
        		};
        		i.q=[];
        		i.c=function(args){
        			i.q.push(args);
        		};
        		w.Intercom=i;

        		var l = function(){
        			var s=d.createElement('script');
        			s.type='text/javascript';
        			s.async=true;
        			s.src='https://widget.intercom.io/widget/jki7e6dv';
        			var x=d.getElementsByTagName('script')[0];
        			x.parentNode.insertBefore(s, x);
        		};

        		if(w.attachEvent){
        			w.attachEvent('onload', l);
        		}

        		else{
        			w.addEventListener('load', l, false);
        		}
        	}
        })();
        
        // When DOM is ready...
        $(function(){
            window.Intercom('boot', window.intercomSettings);
        });
    }

})(window, document, undefined, jQuery || $);
