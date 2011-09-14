
Ext.ns('App');
//main ui

var App = Ext.apply(new Ext.util.Observable,{

	/**
	 * UI Container object
	 *
	 * @type object
	 */
	ui: {},
	activePage: 0,
	maxPageNum: 2,
	prevCard: null,
	urlPath : window.location.pathname,
	surveyData: {}, /*Surveys data*/
	surveyLoaded: false,
	surveyCodeError: false,
	preloaded: [],
	/**
	 * UI pages
	 */

	preloadImages : [
	    '../images/intensity_gauge_background.png',
	    '../images/submit_button.png',
	    '../images/shadow.png',
	    '../images/red_instruction_bar1.png',
	    '../images/v_iphone.png',
	    '../images/h_iphone.png',
	    '../images/red_arrow.png',
	    '../images/faces/faces_1_1.jpg',
	    '../images/faces/faces_1_2.jpg',
	    '../images/faces/faces_1_3.jpg',
	    '../images/faces/faces_1_4.jpg',
	    '../images/faces/faces_1_5.jpg',
	    '../images/faces/faces_1_6.jpg', 
	    '../images/faces/faces_1_7.jpg', 
	    '../images/faces/faces_1_8.jpg', 
	    '../images/faces/faces_1_9.jpg', 
	    '../images/faces/faces_1_10.jpg',
	    '../images/faces/faces_2_1.jpg', 
	    '../images/faces/faces_2_2.jpg', 
	    '../images/faces/faces_2_3.jpg', 
	    '../images/faces/faces_2_4.jpg', 
	    '../images/faces/faces_2_5.jpg',
	    '../images/faces/faces_2_6.jpg', 
	    '../images/faces/faces_2_7.jpg', 
	    '../images/faces/faces_2_8.jpg',
	    '../images/faces/faces_2_9.jpg', 
	    '../images/faces/faces_2_10.jpg',
	    '../images/facebook_icon.png',
	    '../images/twitter_icon.png',
	    '../images/emotion_instructions.jpg',
	    '../images/intensity_instructions.png'
	],
	
	/**
	 * bootstrap
	 */
	preload: function(imgArr,clb){
		var total = imgArr.length;
		var loaded = 0;
		for(var i=0; i< imgArr.length ; i++){
		    var myEl = new Ext.Element(document.createElement('img'));
		    myEl.on('load', function() {
			++loaded;
			if(loaded == total && clb) return clb();
		    });
		    myEl.set({ 'src' : imgArr[i]});
		}
		
	},
	
	bootstrap: function() {
		var self = App;

		if(Apple.isProduct()){
		    FB.init({ 
			appId:'207296725962034', 
			cookie:true,
			status:true,
			oauth  : true, //enable
			xfbml:true 
		    });
		}


		self.preload(self.preloadImages,function(){
			self.initUi();
			self.initEventListener();
			Ext.get("loading").hide();
			Ext.get("loading-mask").hide();
		});
	}

	,setSurvey: function(data){
		App.surveyCodeError = false;
		App.surveyData = data;
		App.surveyLoaded = true;
		
		App.surveyDisabled = (data['status'] && data['status'] == 'disabled') ? true : false;
		App.storeContacts = data['store_contacts'];
		
		var els = Ext.select('.short-stimulus-place');
		els.each(function(el){
			el.setHTML(data.short_stimulus);
		});

		var elsB = Ext.select('.short-stimulus-place-big');
		elsB.each(function(el){
			el.setHTML(data.short_stimulus.toUpperCase());
		});
		//Ext.get(Ext.DomQuery.selectNode('title')).setHTML("How do you feel about " + data.short_stimulus + "?");
		
	}
	
	,getSurvey: function(){
		var self = App;
		var survey_code = Ext.get("survey_code").getValue();
		if(survey_code){
			Ext.Ajax.request({
				url: App.urlPath,
				method: 'GET',
				params: { action : 'getsurvey' , survey: survey_code, device: 'phone' },
				success: function(result){
					var data = Ext.util.JSON.decode(result.responseText);
					
					if(data.status == 'error'){
						App.surveyCodeError = true;
						self.ui = new App.Ui();
					}else{
						App.setSurvey(data);
						self.ui = new App.Ui();
						App.setSurvey(data);
					}
					
					var els = Ext.select('.short-stimulus-place');
					els.each(function(el){
					    el.setHTML(data.short_stimulus);
					});
					
					self.ui.on('orientationchange', function() {
					    self.orientationUpdate();
					});
				},
				failure: function ( result, request) { 
					App.surveyCodeError = true;
					self.ui = new App.Ui();
					self.ui.on('orientationchange', function() {
					    self.orientationUpdate();
					});
				} 
			});
		}else{
			self.ui = new App.Ui();
			self.ui.on('orientationchange', function() {
			    self.orientationUpdate();
			});
		}
	}
	
	/**
	 * init the application ui
	 */
	, initUi: function() {
		App.getSurvey();
	}

	/**
	 * Init Event Listener
	 */
	, initEventListener: function() {
		var self = App;
		/*
		this.ui.on('orientationchange', function() {
			self.orientationUpdate();
		});
		*/
	}
	
	, orientationUpdate: function(){
		if(App.ui.getActiveItem() === App.ui.verbatimPage){
			return;
		}
		
		if("onorientationchange" in window){
		    if( Ext.orientation == 'landscape'){
			var w = window.innerWidth;
			var h = window.innerHeight;
			//no need for big devices
			if(h > 400 || h > w){
				return;
			}
			Ext.get('landscape-overlay').setVisibilityMode(Element.DISPLAY).show(true);
		    }else{
			Ext.get('landscape-overlay').setVisibilityMode(Element.DISPLAY).hide(true);
		    } 
		}
	}
	
	, nextCode: function(e, from){
		var self = App;
		var animation = {
				type: 'flip',
				direction: 'left'
		};
		//App.ui.setActiveItem(this.ui.stimulusPage,animation);
		App.ui.setActiveItem(self.ui.emotePage,animation);
	}
	
	, nextStimulus: function(e, from){
		var self = App;
		if(e.type == 'swipe' && e.direction != 'left') return; //don't handle this direction
		
		var animation = {
				type: 'slide',
				direction: 'left'
		};
		if(e.type == 'tap'){
			animation = {
					type: 'flip',
					direction: 'left'
			};
		}
		
		var nextCard = self.ui.emotePage;
		if( self.prevCard ){
			nextCard = self.prevCard;
		}
		App.ui.setActiveItem(nextCard,animation);
	}
	
	, nextTutorial: function(e, from){
		var self = App;
		var animation = {
				type: 'slide',
				direction: 'left'
		};
		App.ui.setActiveItem(self.ui.emotePage,animation);
	}
	
	, prevTutorial: function(e,from){
		var self = App;
		var animation = {
				type: 'slide',
				direction: 'right'
		};
		App.ui.setActiveItem(self.ui.stimulusPage,animation);
	}
	
	, prevEmote: function(e,from){
		var self = App;
		var animation = {
				type: 'slide',
				direction: 'right'
		};
		App.ui.setActiveItem(self.ui.stimulusPage,animation);
	}
	
	, popupEmote: function(e,from){
		var self = App;
		var animation = {
			type: 'flip',
			direction: 'right'
		};
		self.prevCard = self.ui.emotePage;
		App.ui.setActiveItem(self.ui.stimulusPage, animation);
	}
	
	, nextEmote: function(e,from){
		var self = App;
		var animation = {
				type: 'slide',
				direction: 'left'
		};
		var emoteId = self.ui.emotePage.faceCol + '_' + self.ui.emotePage.faceRow;
		var imgSrc = self.ui.intensityPage.faceIntensity[emoteId].img;
		
		Ext.get('preloading-win').setVisibilityMode(Element.DISPLAY).show(true);
		
		self.preload(['../images/' + imgSrc],function(){
			Ext.get('preloading-win').setVisibilityMode(Element.DISPLAY).hide(true);
			self.ui.intensityPage.setEmote(emoteId);
			App.ui.setActiveItem(self.ui.intensityPage,animation);
			
		});

	}
	
	, prevIntensity: function(e,from){
		var self = App;
		var animation = {
				type: 'slide',
				direction: 'right'
		};
		App.ui.setActiveItem(self.ui.emotePage,animation);
	} 
	
	,nextIntensity: function(e,from){
		var self = App;
		var animation = {
				type: 'slide',
				direction: 'left'
		};
		
		var intensityId = self.ui.intensityPage.faceRow;
		var emoteId = self.ui.emotePage.faceCol + '_' + self.ui.emotePage.faceRow;
		var faceName = self.ui.emotePage.faceNames[emoteId] + "_intensity_"+ 
			     intensityId + ".png";

		var imagePath = '../images/verbatim_intensity/' + faceName;
		Ext.get('preloading-win').setVisibilityMode(Element.DISPLAY).show(true);
		
		self.preload([imagePath],function(){
			Ext.get('preloading-win').setVisibilityMode(Element.DISPLAY).hide(true);
			Ext.get('verbatim-image').set({ "src" : imagePath});
			App.ui.setActiveItem(self.ui.verbatimPage,animation);
		});
		var faceName = self.ui.emotePage.faceNames[emoteId];
		Ext.get('emotion-name-place').setHTML(faceName.toUpperCase());

		//Ext.get('verbatim-image').set({ "src" : '../images/verbatim_intensity/' + faceName});
		//App.ui.setActiveItem(this.ui.verbatimPage,animation);
	}
	
	,prevVerbatim: function(e,from){
		var self = App;
		var animation = {
				type: 'slide',
				direction: 'right'
		};
		App.ui.setActiveItem(self.ui.intensityPage,animation);
	}

	,nextVerbatim: function(e,from){
		var self = App;
		var animation = {
				type: 'slide',
				direction: 'left'
		};
		
		Ext.get('preloading-win').setVisibilityMode(Element.DISPLAY).show(true);
		
		var emoteId = self.ui.emotePage.faceCol + '_' + self.ui.emotePage.faceRow;
		var faceName = self.ui.emotePage.faceNames[emoteId];
		var intensity_level = self.ui.intensityPage.intensity_level;
		var verbatim_el = Ext.get('verbatim-textarea');

		/*Thanks page*/
		var verbatim_text = verbatim_el.dom.value;
		
		var social_text = "I e.moted about " + self.surveyData.short_stimulus + " ... I felt " + 
				faceName.toUpperCase() +
				" because \"" +
				verbatim_text.replace(/^\s*because\s*\.*\s*/i,'') + "\"";
		
		Ext.get('twitter_url').set({"href" : "http://twitter.com/share?text=" + encodeURIComponent(social_text)+" via e.mote"+"&url=http://www.inspirationengine.com"});
		
		var faceRow = self.ui.intensityPage.faceRow;
		var fileName = faceName + "_intensity_" + faceRow;
		
		/*
		Ext.get('facebook_url').set({"href" : "http://www.facebook.com/dialog/feed?app_id=207296725962034&display=touch&message="
		+ encodeURIComponent(social_text) + "&picture=" + 'http://' + window.location.host + '/images/browser/small/' + fileName + '.png' +
		"&link=http://www.inspirationengine.com&redirect_uri=http://" + window.location.host});
		*/
		
		if(Apple.isProduct()){
		    self.mon(Ext.get('facebook_url'), {
			tap: function(){
				FB.login(function(response) {
				    if (response.authResponse) {
					var userId = response.authResponse.userID;
					FB.api( '/' + userId +'/feed', 'post',{
						picture: 'http://' + window.location.host + '/images/browser/small/' + fileName + '.png',
						link : 'http://www.inspirationengine.com',
						message: social_text
					}, function(response) {
						if(!response){
							alert("Error occured ");
						}else if(response.error){
							alert("Error occured " + response.error);
						}
					});
				    } else {
					alert('User cancelled login or did not fully authorize.');
				    }
				}, {scope: 'publish_stream,read_stream'});
			},
			scope: App
		    });
		}else{
		    Ext.get('facebook_url').set({"href" : "http://www.facebook.com/dialog/feed?app_id=207296725962034&display=touch&message="
			+ encodeURIComponent(social_text) + "&picture=" + 'http://' + window.location.host + '/images/browser/small/' + fileName + '.png' +
			"&link=http://www.inspirationengine.com&redirect_uri=" +
			encodeURIComponent('http://' + window.location.host + "/close.html") + "&sdk=joey",
			"target" : "_blank"});
		}

		/*
		self.mon(Ext.get('facebook_url'), {
		    tap: function(){
		    	FB.ui({ method: 'feed',
				picture: 'http://' + window.location.host + '/images/browser/small/' + fileName + '.png',
				link : 'http://www.inspirationengine.com',
				message: social_text
			});
		    },
		    scope: App
		});
		*/


		Ext.Ajax.request({
			url: App.urlPath,
			method: 'POST',
			params: { action: 'savesurveyresult',emote : faceName, intensity_level: intensity_level, 
				verbatim: verbatim_el.dom.value, device: 'phone', out: 'json' },
			success: function(result){
				App.ui.setActiveItem(self.ui.thanksPage,animation);
				Ext.get('preloading-win').setVisibilityMode(Element.DISPLAY).hide(true);
			},
			failure: function ( result, request) { 
				App.ui.setActiveItem(self.ui.thanksPage,animation);
				Ext.get('preloading-win').setVisibilityMode(Element.DISPLAY).hide(true);
			} 
		});

	}

});


Ext.ns('App.Ui');
App.Ui = Ext.extend(Ext.Container, {
	
	codePage: null,
	stimulusPage: null,
	tutorialPage: null,
	emotePage: null,
	intensityPage: null,
	verbatimPage: null,
	thanksPage: null,
	
	initComponent: function() {
		var pages = [];
		if(App.surveyDisabled){
		    pages = [
			//new App.Ui.ThanksPage()
			new App.Ui.DisabledPage()
		    ];
		}else{
		    this.emotePage = new App.Ui.EmotePage();
		    this.intensityPage = new App.Ui.IntensityPage();
		    this.verbatimPage = new App.Ui.VerbatimPage();
		    this.thanksPage = new App.Ui.ThanksPage();
		    pages = [
			this.emotePage,
			this.intensityPage,
			this.verbatimPage,
			this.thanksPage
		    ];
		
		    if( ! App.surveyLoaded){
			this.codePage = new App.Ui.CodePage();
			pages.unshift(this.codePage);
		    }
		}
		//add event handlers
		var config = {
			fullscreen: true,
			layout: 'card',
			activeItem: 0,
			animation: 'slide',
			autoDestroy: false,
			items : pages
		};
		Ext.apply(this, config);
		App.Ui.superclass.initComponent.call(this);
	}
});

App.Ui.DisabledToolbar = Ext.extend(Ext.Toolbar, {
	initComponent: function() {
		var config = {
			title: '<div style="padding-top: 5px;"><img src="../images/e.mote-logo.png"></div>',
			dock: 'top',
			cls: 'emote-toolbar-blue',
			layout: 'hbox'
		};
		Ext.apply(this, config);
		App.Ui.DisabledToolbar.superclass.initComponent.call(this);
	}
});
Ext.reg('App.Ui.DisabledToolbar', App.Ui.DisabledToolbar);


App.Ui.DisabledPage = Ext.extend(Ext.Panel, {
	initComponent: function() {
		var toolBar = new App.Ui.DisabledToolbar();
		var config = {
			fullscreen: true,
			layout: 'card',
			scroll: 'vertical',
			cls: 'disabled-bg',
			html : '<div style="text-align: center;color: white;padding-top: 10%;font-family: Verdana, Arial, Helvetica, sans-serif;">' + 
				'The e.mote&#0153;  you are attempting to access is currently unavailable.' + 
				'</div>',
			dockedItems:[
			             toolBar
			]
		};
		
		Ext.apply(this, config);
		App.Ui.DisabledPage.superclass.initComponent.call(this);
	}
	, afterRender: function() {
		App.Ui.DisabledPage.superclass.afterRender.call(this);
		
	}

});
Ext.reg('App.Ui.DisabledPage', App.Ui.DisabledPage);



App.Ui.StimulusPageArea = Ext.extend(Ext.Panel, {
	initComponent: function() {
		var config = {
			fullscreen: true,
			layout: 'card',
			scroll: 'vertical',
			html: '<div class="stimulus-desc"><div class="stimulus-desc-title">Arabiatta Sauce</div>' + 
				'<div class="stimulus-desc-text">You just experienced Pepe & Pants Arrabiata Pasta Sauce.' +
				' We\'d like to know how you felt about your experience.</div></div><div class="stimulus-item"><img src="../images/bottle_item.png"></div>'
		};
		Ext.apply(this, config);
		App.Ui.StimulusPageArea.superclass.initComponent.call(this);
	}
	, afterRender: function() {
		App.Ui.StimulusPageArea.superclass.afterRender.call(this);
		this.mon(this.el, {
				swipe: function (e){ 
					return App.nextStimulus(e,this); 
				},
				scope: this
		});
	}
});
Ext.reg('App.Ui.StimulusPageArea', App.Ui.StimulusPageArea);

App.Ui.StimulusToolbar = Ext.extend(Ext.Toolbar, {
	initComponent: function() {
		var config = {
			title: '<div style="padding-top: 5px;"><img src="../images/e.mote-logo.png"></div>',
			dock: 'top',
			cls: 'emote-toolbar-blue',
			layout: 'hbox'
		};
		Ext.apply(this, config);
		App.Ui.StimulusToolbar.superclass.initComponent.call(this);
	}
	, afterRender: function() {
		App.Ui.StimulusToolbar.superclass.afterRender.call(this);
		/*
		this.mon(this.el, {
			tap: App.nextStimulus,
			scope: App
		});
		*/
	}
});
Ext.reg('App.Ui.StimulusToolbar', App.Ui.StimulusToolbar);


App.Ui.StimulusPage = Ext.extend(Ext.Panel, {
	initComponent: function() {
	
		var toolBar = new App.Ui.StimulusToolbar();
		var stimulusPageArea = new App.Ui.StimulusPageArea();

		var config = {
			title: 'Stimulus page',
			layout:'card',
			fullscreen: true,
			cls: 'stimulus-bg',
			activeItem: 0, // make sure the active item is set on the container config!
			items: [
			        stimulusPageArea
			],
			dockedItems:[
			             toolBar
			]
		};
		Ext.apply(this, config);
		App.Ui.StimulusPage.superclass.initComponent.call(this);
	}
});
Ext.reg('App.Ui.StimulusPage', App.Ui.StimulusPage);


App.Ui.WelcomePage = Ext.extend(Ext.Panel, {
	initComponent: function() {
	
		var toolBar = new App.Ui.StimulusToolbar();

		var config = {
			title: 'Welcome page',
			layout:'card',
			fullscreen: true,
			cls: 'stimulus-bg',
			activeItem: 0, // make sure the active item is set on the container config!
			html: '<div class="stimulus-desc">' +
				'<div class="welcome-text">'+
				'You are about to experience <span class="bold-text">e.mote</span>&#0153;, a new <i>(and hopefully fun!)</i> way to give feedback on people, products or services.' +
				'<br/><br/>'+
				'And no worries, your <span class="bold-text">e.mote</span>&#0153; responses are anonymous' +
				'so you can express how you really feel. <br/><br/>' +
				'Enjoy <span class="bold-text">"e.moting!"</span>.<br/><br/>' +
				'</div>' +
				'<div><a href="#" id="get-started"><img src="../images/get_started_button.png"></a></div>' +
			'</div>',

			dockedItems:[
			             toolBar
			]
		};
		Ext.apply(this, config);
		App.Ui.WelcomePage.superclass.initComponent.call(this);
	},
	
	afterRender: function() {
		App.Ui.WelcomePage.superclass.afterRender.apply(this, arguments);
		this.faceEl = Ext.get('get-started');
		this.mon(Ext.get('get-started'), {
			tap: App.nextStimulus,
			scope: App
		});
		//Ext.get('get-started').on(Ext.isChrome ? 'click' : 'tap', this.onStartTap, this);
	}
});
Ext.reg('App.Ui.WelcomePage', App.Ui.WelcomePage);


App.Ui.CodePage = Ext.extend(Ext.Panel, {
	initComponent: function() {
	
		var toolBar = new App.Ui.StimulusToolbar();

		var config = {
			title: 'Code page',
			layout:'card',
			fullscreen: true,
			cls: 'stimulus-bg',
			activeItem: 0, // make sure the active item is set on the container config!
			html: 
			'<div class="stimulus-desc">' +
				'<div class="survey-code-input-text">'+
				'Please enter your e.mote&#153; code.' +
				'</div>' +
				'<br/>'+
				'<input type="text" name="input-survey-code" id="input-survey-code" value="">' + 
				'<div id="code-input-error" ' + ( ! App.surveyCodeError ? 'class="x-hidden-display"': '' ) + 
				'>e.mote code not recognized.<br/>Please try again or <a href="mailto:support@inspirationengine.com">email us</a>.</div>' +
				'<div id="submit-code-button"><a href="#" id="code-submit"><img src="../images/submit_button.png"></a></div>' +
			'</div>',

			dockedItems:[
			             toolBar 
			]
		};
		Ext.apply(this, config);
		App.Ui.CodePage.superclass.initComponent.call(this);
	},
	
	afterRender: function() {
		App.Ui.CodePage.superclass.afterRender.apply(this, arguments);
		this.mon(Ext.get('input-survey-code'), {
			blur: function(){
				scroll(0,0);
			}
		});
		
		this.mon(Ext.get('code-submit'), {
			tap: function(){
//				Ext.get('preloading-win').setDisplayMode(Element.DISPLAY).show(true);
				Ext.get('preloading-win').setVisibilityMode(Element.DISPLAY).show(true);
				var survey_code = Ext.get('input-survey-code').getValue();
				Ext.Ajax.request({
					url: App.urlPath,
					method: 'GET',
					params: { action : 'getsurvey' , survey: survey_code, device: 'phone' },
					success: function(result){
						var data = Ext.util.JSON.decode(result.responseText);
						if(data.status == 'error'){
							var el = Ext.get('code-input-error');
							el.setVisibilityMode(Element.DISPLAY);
							el.show(true);
							
						}else{
							App.setSurvey(data);
							App.nextCode();
						}
						Ext.get('preloading-win').setVisibilityMode(Element.DISPLAY).hide(true);

					},
					failure: function ( result, request) { 
						Ext.get('code-input-error').show();
						Ext.get('preloading-win').setVisibilityMode(Element.DISPLAY).hide(true);
					} 
				});
			}
		});
		
	}
});
Ext.reg('App.Ui.CodePage', App.Ui.CodePage);


App.Ui.TutorialPage = Ext.extend(Ext.Panel, {
	initComponent: function() {
		var toolBar = new Ext.Toolbar({
			title: 'Tutorial',
			dock: 'top',
			layout: 'hbox',
			cls: 'emote-toolbar-blue',
			items: [
			        
			        new Ext.Button({
			        	text: 'back',
			        	ui: 'back',
			        	cls: 'emote-toolbar-blue',
			        	style: {
			        		//backgroundImage: "-webkit-gradient(linear, 0% 0%, 0% 100%,   color-stop(0.33, rgb(91,127,219) ), color-stop(0.7, rgb(35,83,194)))"
			        		backgroundImage: "-webkit-gradient(linear, 0% 0%, 0% 100%,   color-stop(0.33, rgb(91,127,219) ), color-stop(0.7, rgb(35,83,194)))"
			        	},
			        	handler: function(e){
			        		return App.prevTutorial(e,this);
			        	}
			        }),
			        
			        {
			        	xtype: 'spacer'
			        },
			        new Ext.Button({
			        	text: 'next',
			        	style: {
			        		//backgroundImage: "-webkit-gradient(linear, 0% 0%, 0% 100%,   color-stop(0.33, rgb(91,127,219) ), color-stop(0.7, rgb(35,83,194)))"
			        		backgroundImage: "-webkit-gradient(linear, 0% 0%, 0% 100%,   color-stop(0.7, rgb(3,43,117) ), color-stop(0.33, rgb(106,127,183)))"
			        	},
			        	handler: function(e){
		        			return App.nextTutorial(e,this);
		        		}
			        })
			]
		});

		var config = {
			fullscreen: true,
			layout: 'card',
			html: '<i>Tutorial page here...</i>',
			dockedItems:[
			            toolBar
			]
		};
		Ext.apply(this, config);
		App.Ui.TutorialPage.superclass.initComponent.call(this);
	}

});
Ext.reg('App.Ui.TutorialPage', App.Ui.TutorialPage);


App.Ui.EmotePageToolbar = Ext.extend(Ext.Toolbar, {
	initComponent: function() {
		var config = {
			title: '&nbsp;',
			dock: 'top',
			cls: 'emote-toolbar-blue',
			layout: 'hbox',
			items:[/*
					new Ext.Button({
						text: '&nbsp;back&nbsp;',
						handler: App.prevEmote,
						style: {
							backgroundImage: "-webkit-gradient(linear, 0% 0%, 0% 100%,   color-stop(0.7, rgb(3,43,117) ), color-stop(0.33, rgb(106,127,183)))"
						},
						scope: App
					}),
					*/
					{xtype: 'spacer'},
					new Ext.Button({
						text: '&nbsp;next&nbsp;',
						handler: App.nextEmote,
						disabled: true,
						cls: 'emote-toolbar-button',
//						style: {
//							backgroundImage: "-webkit-gradient(linear, 0% 0%, 0% 100%,   color-stop(0.7, rgb(117,3,3) ), color-stop(0.33, rgb(255,101,101)))"
//						},
						scope: App
					})
			]
		};
		Ext.apply(this, config);
		App.Ui.EmotePageToolbar.superclass.initComponent.call(this);
	}
});


App.Ui.EmotePage = Ext.extend(Ext.Panel, {
	faceEl: null,
	faceElX: null,
	faceElY: null,
	faceCol: 2,
	faceRow: 7,
	maxWidth: 302,
	maxHeight: 325,
	faceHeight: 260,
	showInstruction: true,
	nextEnabled: false,
	
	faceNames: {
		'1_1': 'outraged',
		'1_2': 'angry',
		'1_3': 'unhappy',
		'1_4': 'frustrated',
		'1_5': 'disgusted',
		'1_6': 'miserable',
		'1_7': 'irritated',
		'1_8': 'humiliated',
		'1_9': 'dissatisfied',
		'1_10': 'uneasy',
		'2_1': 'delighted',
		'2_2': 'elated',
		'2_3': 'happy',
		'2_4': 'excited',
		'2_5': 'thrilled',
		'2_6': 'enthusiastic',
		'2_7': 'amazed',
		'2_8': 'surprised',
		'2_9': 'satisfied',
		'2_10': 'content'
	},
	
	faceImages: {
		'1_1': '../images/faces/faces_1_1.jpg',
		'1_2': '../images/faces/faces_1_2.jpg',
		'1_3': '../images/faces/faces_1_3.jpg',
		'1_4': '../images/faces/faces_1_4.jpg',
		'1_5': '../images/faces/faces_1_5.jpg',
		'1_6': '../images/faces/faces_1_6.jpg',
		'1_7': '../images/faces/faces_1_7.jpg',
		'1_8': '../images/faces/faces_1_8.jpg',
		'1_9': '../images/faces/faces_1_9.jpg',
		'1_10': '../images/faces/faces_1_10.jpg',
		'2_1': '../images/faces/faces_2_1.jpg',
		'2_2': '../images/faces/faces_2_2.jpg',
		'2_3': '../images/faces/faces_2_3.jpg',
		'2_4': '../images/faces/faces_2_4.jpg',
		'2_5': '../images/faces/faces_2_5.jpg',
		'2_6': '../images/faces/faces_2_6.jpg',
		'2_7': '../images/faces/faces_2_7.jpg',
		'2_8': '../images/faces/faces_2_8.jpg',
		'2_9': '../images/faces/faces_2_9.jpg',
		'2_10': '../images/faces/faces_2_10.jpg'
	},
	
	initComponent: function() {
		var toolBar = new App.Ui.EmotePageToolbar();
		var config = {
			fullscreen: true,
			layout: 'card',
//			html: '<div id="main_face_desc_area" style="font-weight: bold;font-size: 20px;text-align: center;">AMAZING</div><div id="main_face_area"><div id="face_area"></div><div id="face_shadow"></div></div>',

			html: '<div id="emotion-stimulus"><div class="stimulus-short">Tell us how you feel about <span class="bold-text short-stimulus-place"></span>.</div>'+
			'</div>' +
			'<div class="red-instruction"><div class="red-instruction-place"><div id="face-name"><div style="font-size: 12px;">Move your finger around to change the <br/>emotion that best reflects your experience.</div></div></div></div>' +
			'<div class="face-area-bg">'+
			'<div id="negative">Negative<br/>Emotions</div>' +
		
			'<div id="face-area" ><img id="emote-instruction" src="../images/emotion_instructions.jpg"></div> ' + 
			
			'<div id="positive">Positive<br/>Emotions</div>'+
			'<div class="face-area-bg-col1"></div> <div class="face-area-bg-col2"></div>'+
			'</div>',

			dockedItems: [
			              toolBar
			]

		};
		Ext.apply(this, config);
		App.Ui.EmotePage.superclass.initComponent.call(this);
	}
	
	, afterRender: function() {
		App.Ui.EmotePage.superclass.afterRender.call(this);
		this.faceEl = Ext.get('face-area');
		this.mon(this.faceEl, {
			touchmove: this.onSelectFace,
			scope: this
		});
		this.mon(this.faceEl, {
			tap: this.onSelectFace,
			scope: this
		});
		

		
		this.mon(Ext.get('emotion-stimulus'),{
			tap: function(e){
    			return App.popupEmote(e,this);
    		},
			scope: this
		});
		
	}
	
	, onSelectFace: function(e) {
		if(this.showInstruction){
		    Ext.get('emote-instruction').hide();
		    this.showInstruction = false;
		}
		
		if(! this.nextEnabled){
			var button = this.dockedItems.items[0].items.items[1];
			if(button.disabled){
				button.enable();
			}
			this.nextEnabled = true;
		}

		
		if(! this.faceElX ) this.faceElX = this.faceEl.getX();
		if(! this.faceElY ) this.faceElY = this.faceEl.getY();
		var el_x = e.pageX - this.faceElX;
		var el_y = e.pageY - this.faceElY;
		
		if(el_x > 0 && el_x < this.maxWidth && el_y > 0 && el_y < this.faceHeight){
			var numCol = Math.ceil(el_x/(this.maxWidth/2));
			var numRow = Math.ceil(el_y/(this.faceHeight/10));
			if(numCol != this.faceCol || numRow != this.faceRow){
			/*
				var offset_x = (numCol - 1) * this.maxWidth;
				var offset_y = (numRow - 1) * this.maxHeight + 25;
				this.faceEl.setStyle('background-position', offset_x +'px'+' -' + offset_y + 'px' );
			*/
				this.faceEl.setStyle('background-image', 'url(\''+this.faceImages[numCol + '_' + numRow]+'\')' );
				var faceName = this.faceNames[numCol + '_' + numRow];
				Ext.get('face-name').update(faceName.toUpperCase());
				this.faceCol = numCol;
				this.faceRow = numRow;
			}
		}
	}

});
Ext.reg('App.Ui.EmotePage', App.Ui.EmotePage);


App.Ui.IntensityPageToolbar = Ext.extend(Ext.Toolbar, {
	initComponent: function() {
		var config = {
			title: '&nbsp;',
			dock: 'top',
			cls: 'emote-toolbar-blue',
			layout: 'hbox',
			items:[
					new Ext.Button({
						text: '&nbsp;back&nbsp;',
						handler: App.prevIntensity,
						style: {
							backgroundImage: "-webkit-gradient(linear, 0% 0%, 0% 100%,   color-stop(0.7, rgb(3,43,117) ), color-stop(0.33, rgb(106,127,183)))"
						},
						scope: App
					}),
					{xtype: 'spacer'},
					new Ext.Button({
						text: '&nbsp;next&nbsp;',
						handler: App.nextIntensity,
						disabled: true,
						cls: 'emote-toolbar-button',
						scope: App
					})
			]
		};
		Ext.apply(this, config);
		App.Ui.IntensityPageToolbar.superclass.initComponent.call(this);
	}
});
Ext.reg('App.Ui.IntensityPageToolbar', App.Ui.IntensityPageToolbar);


App.Ui.IntensityPage = Ext.extend(Ext.Panel, {
	
	faceIntensity: {
		'1_1': {
				img: 'outraged.png',
				color: '#ca2828'
			},
		'1_2': {
				img: 'angry.png',
				color: '#ca2828'
			},
		'1_3': {
				img: 'unhappy.png',
				color: '#ca2828'
			},
		'1_4': {
				img: 'frustrated.png',
				color: '#ca2828'
			},
		'1_5': {
				img:'disgusted.png',
				color: '#ca2828'
			},
		'1_6': {
				img: 'miserable.png',
				color: '#ca2828'
			},
		'1_7': {
				img: 'irritated.png',
				color: '#ca2828'
			},
		'1_8': {
				img: 'humiliated.png',
				color: '#ca2828'
			},
		'1_9': {
				img: 'dissatisfied.png',
				color: '#ca2828'
			},
		'1_10': {
				img: 'uneasy.png',
				color: '#ca2828'
			},
		'2_1': {
				img: 'delighted.png',
				color: '#ca2828'
			},
		'2_2': {
				img: 'elated.png',
				color: '#ca2828'
			},
		'2_3': {
				img: 'happy.png',
				color: '#ca2828'
			},
		'2_4': {
				img: 'excited.png',
				color: '#ca2828'
			},
		'2_5': {
				img: 'thrilled.png',
				color: '#ca2828'
			},
		'2_6': {
				img: 'enthusiastic.png',
				color: '#ca2828'
			},
		'2_7': {
				img: 'amazed.png',
				color: '#ca2828'
			},
		'2_8': {
				img: 'surprised.png',
				color: '#ca2828'
			},
		'2_9': {
				img: 'satisfied.png',
				color: '#ca2828'
			},
		'2_10': {
				img: 'content.png',
				color: '#ca2828'
			}
	},
	
	prevEmoteId: null,
	intensityEl: null,
	intensityFaceEl: null,
	intensityBgEl: null,
	start_y: null,
	maxBgHeight: 300,
	maxHeight: 325,
	faceRow: 1,
	faceName: '',
	intensity_level: 50,
	showInstruction: true,
	nextEnabled: false,
		
	initComponent: function() {
		var toolBar = new App.Ui.IntensityPageToolbar();
		var config = {
			fullscreen: true,
			layout: 'card',
			html: '<div id="intensity-stimulus">' + 
				'<div class="stimulus-short">Just how <span id="iface-name-title">amazing</span>' + 
				' does <span class="bold-text short-stimulus-place"></span> make you feel?</div>' + 
				'<div class="red-instruction red-instruction2"><div class="red-instruction-place"><div id="face-name"><div style="font-size: 12px;">Move your finger up and down to set <br/>intensity.</div></div></div></div>' +
//				'<div id="iface-name"><div style="color: red;font-size: 12px;">Move finger up and down near emoticon to set intensity.</div></div>' + 
				'</div>' + 
				'<div id="intensity-big-area" class="face-area-bg2"> <div id="intensity-area"><img id="intensity-instruction" style="float: right;" src="../images/intensity_instructions.png"></div> <div id="intensity-bg2"></div>  </div> ',
			dockedItems: [
			              toolBar
			]
		};
		Ext.apply(this, config);
		App.Ui.IntensityPage.superclass.initComponent.call(this);
	}
	
	, afterRender: function() {
		App.Ui.IntensityPage.superclass.afterRender.call(this);
		this.intensityEl = Ext.get('intensity-big-area');
		this.intensityFaceEl = Ext.get('intensity-area');
		this.intensityBgEl = Ext.get('intensity-bg2');
		this.start_y = this.intensityBgEl.getY();
/*		
		this.mon(this.intensityEl, {
			touchmove: this.onSelectIntensity,
			scope: this
		});
		this.mon(this.intensityBgEl, {
			touchmove: this.onSelectIntensity,
			scope: this
		});
*/
		this.mon(this.intensityEl, {
			touchmove: this.onSelectIntensity,
			scope: this
		});
		
		this.mon(this.intensityEl,{
			tap: this.onSelectIntensity,
			scope: this
		});

	}
	
	,setEmote: function(emoteId){
		var imgSrc = this.faceIntensity[emoteId].img;
		
		this.intensity_level = 20;
		var num_row = this.intensity_level > 33 ? (this.intensity_level > 67 ? 3 : 2 ) : 1;
		var offset_x = (num_row - 1) * 302;
		this.intensityFaceEl.setStyle('background', "url('../images/" + imgSrc + "') -" + offset_x + "px -35px" );
		
		this.faceName = App.ui.emotePage.faceNames[emoteId];

		Ext.get('iface-name-title').update(this.faceName.toLowerCase());
		
		//Ext.get('iface-name').update(this.faceName.toLowerCase());
		var maxBgHeight = this.start_y - this.intensityEl.getY();//this.maxBgHeight
		var bg_height = Math.ceil(maxBgHeight / 5) - 15;
		this.intensityBgEl.setStyle('height', bg_height + 'px' );
	}
	
	,onSelectIntensity: function(e){
		if(this.showInstruction){
		    Ext.get('intensity-instruction').hide();
		    this.showInstruction = false;
		}
		if(! this.nextEnabled){
			var button = this.dockedItems.items[0].items.items[2];
			if(button.disabled){
				button.enable();
			}
			this.nextEnabled = true;
		}
		var el_y = e.pageY - this.intensityEl.getY();
		var bg_height = this.start_y - e.pageY;
		
		var maxBgHeight = this.start_y - this.intensityEl.getY();//this.maxBgHeight
		
		//console.log("bg h: " + bg_height + ", max bg h: " + this.maxBgHeight);
		//console.log("el height  : " + this.intensityEl.getY() + ", start_y: " + this.start_y + ", bg_height " + bg_height);
		
		
		if(bg_height <= maxBgHeight ){
			this.intensityBgEl.setStyle('height', bg_height + 'px' );
		}
		
		this.intensity_level = Math.ceil((bg_height * 100) / maxBgHeight);

		if(el_y > 0 && el_y < maxBgHeight){
//			var num_row = Math.ceil(el_y/(maxBgHeight/3));
			var num_row = this.intensity_level > 33 ? (this.intensity_level > 67 ? 3 : 2 ) : 1;

			if( num_row != this.faceRow){
				var offset_x = (num_row - 1) * 302;
				this.intensityFaceEl.setStyle('background-position', '-' + offset_x +'px'+' -35px' );
				var fullFaceName = this.faceName;
				if(num_row == 3)
					fullFaceName = 'very ' + fullFaceName;
				if(num_row == 1)
					fullFaceName = 'a little ' + fullFaceName;
				//Ext.get('iface-name').update(fullFaceName.toUpperCase());
				
			}
			this.faceRow = num_row;
		}
	}
});
Ext.reg('App.Ui.IntensityPage', App.Ui.IntensityPage);


App.Ui.VerbatimToolbar = Ext.extend(Ext.Toolbar, {
	initComponent: function() {
		var config = {
			title: '&nbsp',
			dock: 'top',
			cls: 'emote-toolbar-blue',
			layout: 'hbox',
			items:[
					new Ext.Button({
						text: '&nbsp;back&nbsp;',
						handler: App.prevVerbatim,
						style: {
							backgroundImage: "-webkit-gradient(linear, 0% 0%, 0% 100%,   color-stop(0.7, rgb(3,43,117) ), color-stop(0.33, rgb(106,127,183)))"
						},
						scope: App
					}),
					{xtype: 'spacer'},
					new Ext.Button({
						text: '&nbsp;next&nbsp;',
						handler: App.nextVerbatim,
						disabled: true,
						cls: 'emote-toolbar-button',
						scope: App
					})
			]
		};
		Ext.apply(this, config);
		App.Ui.VerbatimToolbar.superclass.initComponent.call(this);
	}
});

App.Ui.VerbatimPage = Ext.extend(Ext.Panel, {
	initComponent: function() {
	
		var toolBar = new App.Ui.VerbatimToolbar();

		var config = {
			title: '&nbsp;',
			layout:'card',
			fullscreen: true,
			cls: 'stimulus-bg',
			activeItem: 0, // make sure the active item is set on the container config!
			html: '<div id="verbatim-title"><span class="bold-text">Why did you feel <span id="emotion-name-place"></span></span>  about <span class="short-stimulus-place"></span>?</div>' + 
			'<div id="verbatim-image-place"><img id="verbatim-image" src=""></div><div class="clear_both"></div>' +
			'<textarea id="verbatim-textarea">Because...</textarea>',
			dockedItems:[
			             toolBar
			]
		};
		Ext.apply(this, config);
		App.Ui.VerbatimPage.superclass.initComponent.call(this);
	}

	,afterRender: function() {
		App.Ui.VerbatimPage.superclass.afterRender.apply(this, arguments);
		var button = this.dockedItems.items[0].items.items[2];
		this.mon(Ext.get('verbatim-textarea'), {
			keypress: function(){
		    	    if(button.disabled){
				button.enable();
			    }
			},
			blur: function(){
				scroll(0,0);
			}
			
		});
		//Ext.get('get-started').on(Ext.isChrome ? 'click' : 'tap', this.onStartTap, this);
	}

});
Ext.reg('App.Ui.VerbatimPage', App.Ui.VerbatimPage);


App.Ui.ThanksToolbar = Ext.extend(Ext.Toolbar, {
	initComponent: function() {
		var config = {
			title: '<div style="padding-top: 5px;"><img src="../images/e.mote-logo.png"></div>',
			dock: 'top',
			cls: 'emote-toolbar-blue',
			layout: 'hbox'
		};
		Ext.apply(this, config);
		App.Ui.ThanksToolbar.superclass.initComponent.call(this);
	}

});
Ext.reg('App.Ui.ThanksToolbar', App.Ui.ThanksToolbar);


App.Ui.ThanksPage = Ext.extend(Ext.Panel, {
	initComponent: function() {
	
		var toolBar = new App.Ui.ThanksToolbar();
		var html;
		
		if(App.storeContacts){
			html = '<div id="thanks-block-mail">' + 
			'<div id="thanks-msg" class="x-hidden-visibility">Thank you for e.moting!</div>' +
			'<div class="main-block">' +
			'<div id="email-block">' + 
			'<div>Start a conversation with us... Enter your email below - we\'d be delighted to contact you!</div>' + 
			'<div id="error-block" class="x-hidden-visibility">' +
			'<span class="typo">EMAIL TYPO DETECTED:</span><br/>' + 
			'<span>Please re-check the email address you entered.</span>' + 
			'</div>' + 
			'<form id="send-email">' +
			'<input type="text" id="input-email" value=""><br/>' +
			'<input type="image" src="../images/phone/contact_me_button.png">' +
			'</form>' + 
			'</div>' +
			'<div class="share-block">' +
			'<div id="icons-text">Share your e.mote&#0153; with your friends and family.</div>' + 
			'<a id="facebook_url" href="#"><img src="../images/facebook_icon.png"></a>' + 
			'<a id="twitter_url" href="#" target="_blank"><img src="../images/twitter_icon.png"></a>' +
			'</div>' +
			'</div>' +
			'</div>';
		}else{
			html = '<div id="thanks-block"><div id="thanks-title">Thank you for e.moting!</div>' + 
			'<div class="share-block">' +
			'<a id="facebook_url" href="#"><img src="../images/facebook_icon.png"></a>' + 
			'<a id="twitter_url" href="#" target="_blank"><img src="../images/twitter_icon.png"></a>' +
			'</div>' +
			'<div id="thanks-close">You may now close this browser window.</div>' + 
			'</div>';
		}
		
		var config = {
			title: '&nbsp;',
			layout:'card',
			fullscreen: true,
			cls: 'stimulus-bg',
			activeItem: 0, // make sure the active item is set on the container config!
			html: html,
			dockedItems:[
			             toolBar
			]
		};
		Ext.apply(this, config);
		App.Ui.ThanksPage.superclass.initComponent.call(this);
	}
	
	,afterRender: function() {
		App.Ui.ThanksPage.superclass.afterRender.apply(this, arguments);
		
		var sendEmailEl = Ext.get('send-email');
		
		if(sendEmailEl){
		this.mon(sendEmailEl, {
			submit: function(ev){
				ev.preventDefault();
				var errEl = Ext.get(Ext.query("#thanks-block-mail #error-block"));
				var email = Ext.get('input-email').getValue();
				if(!checkEmail(email)){
					Ext.get(Ext.query("#thanks-block-mail #error-block .typo")).setHTML('EMAIL TYPO DETECTED:');
					errEl.removeCls(['x-hidden-visibility']);
				}else{
					
					errEl.addCls(['x-hidden-visibility']);
					Ext.get('preloading-win').setVisibilityMode(Element.DISPLAY).show(true);
					
					Ext.Ajax.request({
						url: App.urlPath,
						method: 'GET',
						params: { action : 'setemail' , email: email, device: 'phone' , out: 'json'},
						success: function(result){
							Ext.get('preloading-win').setVisibilityMode(Element.DISPLAY).hide(true);
							
							//console.log(result);
							var data = Ext.util.JSON.decode(result.responseText);
							if(data.status == 'error'){
								Ext.get(Ext.query("#thanks-block-mail #error-block .typo")).setHTML(data.msg + ':');
								
								errEl.removeCls(['x-hidden-visibility']);
							}else{
								Ext.get(Ext.query("#thanks-block-mail #email-block")).addCls(['x-hidden-visibility']);
								Ext.get(Ext.query("#thanks-block-mail #thanks-msg")).removeCls(['x-hidden-visibility']);
							}
						},
						failure: function ( result, request) {
							Ext.get('preloading-win').setVisibilityMode(Element.DISPLAY).hide(true);
							
							Ext.get(Ext.query("#thanks-block-mail #error-block .typo")).setHTML('SERVER ERROR:');
							errEl.removeCls(['x-hidden-visibility']);
						} 
					});
				}
			}
		});
		}
	}

});
Ext.reg('App.Ui.ThanksPage', App.Ui.ThanksPage);

Ext.setup({
		fullscreen: true,
		tabletStartupScreen: 'images/sencha_ipad.png',
		phoneStartupScreen: 'images/sencha_iphone.png',
		//icon: 'icon.png',
		addGlossToIcon: false,
		onReady: App.bootstrap,
		scope: App
});

var Apple = {};
Apple.UA = navigator.userAgent;
Apple.Device = false;
Apple.Types = ["iPhone", "iPod", "iPad"];

Apple.isProduct = function(){
    if(Apple.UA.match(/iPhone|iPod|iPad/i)) {
	return true;
    }
    return false;
};

function checkEmail(value){
    return /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i.test(value);
};
