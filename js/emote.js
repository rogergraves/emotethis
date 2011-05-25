
(function() {

	var parseURL = function(url) {
		var a =  document.createElement('a');
		a.href = url;
		return {
			source: url,
			protocol: a.protocol.replace(':',''),
			host: a.hostname,
			port: a.port,
			query: a.search,
			params: (function(){
				var ret = {},
				seg = a.search.replace(/^\?/,'').split('&'),
				len = seg.length, i = 0, s;
				for (;i<len;i++) {
					if (!seg[i]) { continue; }
					s = seg[i].split('=');
					ret[s[0]] = s[1];
				}
				return ret;
			})(),
			file: (a.pathname.match(/\/([^\/?#]+)$/i) || [,''])[1],
			hash: a.hash.replace('#',''),
			path: a.pathname.replace(/^([^\/])/,'/$1'),
			relative: (a.href.match(/tp:\/\/[^\/]+(.+)/) || [,''])[1],
			segments: a.pathname.replace(/^\//,'').split('/')
		};
	};

	var scripts = document.getElementsByTagName("script");
	var scriptPath = scripts[scripts.length-1].src;
	//console.log(scriptPath + ", host: " + parseURL(scriptPath).host);
	
	var Emote = {
		template: '',
		
		key: emote_uid,
		protocol: (("https:" == document.location.protocol) ? "https://ssl." : "http://"),
		host: parseURL(scriptPath).host,
		linkContent : typeof emote_link_content === 'undefined' ? 'Emote this' : emote_link_content,
		
		
		mainDisplay : '',
		iframeTpl :  '<iframe id="emote-iframe" style="background-color:transparent;" src="#{url}?#{query}" frameBorder="0" allowtransparency="true"></iframe>',
		showLinkTpl: "<a id='emote-link-client' href='#'>#{link_content}</a>",
		dialogTpl: '<div id="emote-main-block"><div id="emote-overlay">' +
					'<div id="emote-overlay-bg"></div>' +
					'</div>'+
					'<div id="emote-iframe-place">Loading</div>' +
					'<div id="emote-close"><a href="#" id="emote-close-action"><img src="#{close_image}"></a></div>'+
					'</div>',
/*
		dialogTpl: '<div class="uvOverlay1" id="#{overlay_id}" style="display:none;" onclick="return UserVoice.hidePopupWidget();">
			<div id="#{overlay_background_id}"></div><div class="uvOverlay2"><div class="uvOverlay3"><div id="#{dialog_id}">' +.
	                          '<div onclick="return UserVoice.hidePopupWidget();" id="#{dialog_close_id}" title="Close"><button>Close Dialog</button></div>' +.
	                          '<div id="#{dialog_content_id}"></div>' +
	                        '</div></div></div>',
*/
		
		getOverlayCss: function(){
			var dem = this.pageDimensions();
			var overlayCss = "#emote-overlay {\
					position: relative;\
					z-index: 100000;\
				}\
				#emote-close a img{\
					border: none;\
				}\
				#emote-close a:active { outline: none; }\
				#emote-close a:focus { -moz-outline-style: none; }\
				#emote-close {\
					position: fixed;\
					z-index: 100002;\
					left: 50%;\
					margin-left: 420px;\
					top: 10px;\
				}\
				#emote-iframe-place {\
					width: 950px;\
					height: 480px;\
					color: white;\
					top: 0;\
					left: 50%;\
					margin: 0 0 0 -475px;\
					position: fixed;\
					z-index: 100002;\
				}\
				#emote-iframe{\
					top: 0;\
					position: absolute;\
					width: 100%;\
					height: 100%;\
				}\
				#emote-overlay-bg {\
					position: fixed;\
					z-index: 100001;\
					top:0;\
					left:0;\
					background-color:black;\
					-ms-filter: alpha(opacity=5);\
					filter: alpha(opacity=5);\
					opacity: .05;\
					width: 100%;\
					height: 100%;\
				}\
				html.emote-dialog-open object,\
				html.emote-dialog-open iframe,\
				html.emote-dialog-open embed {\
					visibility: hidden;\
				}\
				html.emote-dialog-open iframe#emote-iframe {\
					visibility: visible;\
				}\
				";
				
			return overlayCss;
		},
		
		htmlElement : function() {
			return document.getElementsByTagName('html')[0];
		},
		
		addClassToElement : function(element, className) {
			element.className += (element.className ? ' ' : '') + className;
		},
		
		removeClassFromElement : function(element, className) {
			element.className = element.className.replace(new RegExp("(^|\\s+)" + className + "(\\s+|$)", "g"), ' ');
		},

		
		pageDimensions: function() {
			var de = document.documentElement;
			var width = window.innerWidth || (de && de.clientWidth) || document.body.clientWidth;
			var height = window.innerHeight || (de && de.clientHeight) || document.body.clientHeight;
			return {width: width, height: height};
		},
		
		includeCss: function(cssString) {
			var styleElement = document.createElement('style');
			styleElement.type = 'text/css'; styleElement.media = 'screen';
			if (styleElement.styleSheet) {
				styleElement.styleSheet.cssText = cssString;
			} else {
				styleElement.appendChild(document.createTextNode(cssString));
			}
			document.getElementsByTagName('head')[0].appendChild(styleElement);
		},

		
		renderTpl : function(template, params) {
			return template.replace(/\#\{([^{}]*)\}/g,
					function(a, b) {
						var r = params[b];
						return typeof r === 'string' || typeof r === 'number' ? r : a;
					});
		},

		
		showWin: function(){
			var el = document.getElementById('emote-mask-overlay');
			if(! el){
				this.includeCss(this.getOverlayCss());
				var el = document.createElement('div');
				el.setAttribute('id','emote-mask-overlay');
				el.innerHTML = this.renderTpl(this.dialogTpl,{
					close_image : this.protocol + this.host + '/images/close_button.png',
					ajax_loader : this.protocol + this.host + '/images/ajax-loader.png'
				});
				document.body.appendChild(el);
				this.createIframe();
				
				//var iframeHtml = render(this.iframeTemplate, {});
			}else{
				var main_el = document.getElementById('emote-main-block');
				main_el.style.display = this.mainDisplay;
			}
			this.addClassToElement(this.htmlElement(), "emote-dialog-open");
		},
		
		createIframe: function(){
			var obj = this;
			var el = document.getElementById('emote-iframe-place');
			if(el){
				el.innerHTML = this.renderTpl(this.iframeTpl, {
					url : this.protocol  + this.host + '/browser/index.php',
					query: 'action=widgetsurvey&survey=' + this.key
				});
				
				
				
				var close_el = document.getElementById('emote-close-action');
				if(close_el){
					//close win
					close_el.onclick = function(){
						var el = document.getElementById('emote-main-block');
						
						if(el){
							obj.mainDisplay = el.style.display;
							el.style.display = 'none';
						}
						obj.removeClassFromElement(obj.htmlElement(), "emote-dialog-open");
						return false;
					};
				}
			}
		},

		showLink: function(){
			var obj = this;
			var emote_button = document.getElementById('emote-this-button');
			if(emote_button){
				emote_button.innerHTML = this.renderTpl(this.showLinkTpl,{
				    link_content: this.linkContent
				});
				var a = emote_button.getElementsByTagName('a')[0];
				a.onclick = function(e) {
					obj.showWin();
					return false;
				};
			}
			return false;
		}
		
	};
	Emote.showLink();
})();
