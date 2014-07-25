/*
 * Helpers
 */

define(['require', 'jquery', 'class'], function(req, $) {
	var Helpers = Class.extend({
		init: function() {
			this.parallelLoading = 0;
			/**
			 * EXtends the JQuery post method for conveniance use with Json
			 * @param {String} url
			 * @param {Object} data
			 * @param {Function} callback
			 */
			$.postJson = function(url, data, callback) {
				$.post(url, data, callback, "json");
			};
		},

		/**
		 * @return {String} the current main container jQuery selector (from the opened tab)
		 */
		getMainContainerSelector: function(tabObj){
			if(tabObj == undefined){
				tabObj = uiBootstrap.tabs;	//backward compat by using the global object
			}

			if(tabObj.size() == 0) {
				if($("div.main-container").length > 0){
					return "div.main-container";
				}
				return false;
			}

			var uiTab = $('.ui-tabs-panel').prop('id');
			if (typeof $('.ui-tabs-panel')[tabObj.tabs('option', 'selected')] != 'undefined') {
				uiTab = $('.ui-tabs-panel')[tabObj.tabs('option', 'selected')].id;
			}

			if ($("div#"+uiTab+" div.main-container").css('display') == 'none') {
				return "div#"+uiTab;
			}

			return "div#"+uiTab+" > div.main-container";
		},

		/**
		 * @param {String} name the name of the tab to select
		 */
		selectTabByName: function(name){
			$("#"+name).click();
		},

		/**
		 * get the index of the tab identified by name
		 * @param {String} name
		 * @return the index or -1 if not found
		 */
		getTabIndexByName: function(name){
			elts = $("div#tabs ul.ui-tabs-nav li a");
			i = 0;
			while (i < elts.length) {
				elt = elts[i];
				if (elt) {
					if (elt.id) {
						if (elt.id == name) {
							return i;
						}
					}
				}
				i++;
			}
			return -1;
		},

		openTab: function(title, url, open) {
			if (open == undefined) open = true;
			idx = this.getTabIndexByUrl(url);
			if (idx == -1) {
				uiBootstrap.tabs.tabs("add", url, title);
				idx = uiBootstrap.tabs.tabs("length")-1;
			}
			//If control pressed, not select
			if (open) {
				uiBootstrap.tabs.tabs("select", idx);
			}
		},

		getTabIndexByUrl: function(url){
			elts = $("div#tabs ul.ui-tabs-nav li a");
			i = 0;
			ret = -1;
			elts.each(function() {
			   var href = $.data(this, 'href.tabs');
			   if (url == href) {
				   ret = i;
				   return;
			   }
			   i++;
			})
			return ret;
		},

		/**
		 * Add parameters to a tab
		 * @param {Object} tabObj
		 * @param {String} tabName
		 * @param {Object} parameters
		 */
		updateTabUrl: function(tabObj, tabName, url){
			index = this.getTabIndexByName(tabName);
			tabObj.tabs('url', index, url);
			tabObj.tabs('enable', index);
		},

		/*
		 * Naviguation and ajax helpers
		 */

		/**
		 * Begin an async request, while loading:
		 * - show the loader img
		 * - disable the submit buttons
		 */
		loading: function(){
			this.parallelLoading++;
			if (this.parallelLoading > 1) return; //Need once
			$(window).bind('click', function(e){
				e.stopPropagation();
				e.preventDefault();
				return false;
			});
			$("#ajax-loading").show();
			//$("input:submit, input:button, a").prop('disabled', true).css('cursor', 'default');
		},

		/**
		 * Complete an async request, once loaded:
		 *  - hide the loader img
		 *  - enable back the submit buttons
		 */
		loaded: function(){
			this.parallelLoading--;
			if (this.parallelLoading > 0) return; //Need once
			$(window).unbind('click');
			$("#ajax-loading").hide();
			//$("input:submit, input:button, a").prop('disabled', false).css('cursor', 'pointer');
		},

		/**
		 * Load url asyncly into selector container
		 * @param {String} selector
		 * @param {String} url
		 */
		_load: function(selector, url, data){
			if (data) {
				data.nc = new Date().getTime();
			} else {
				data = {nc: new Date().getTime()}
			}
			this.loading();
			if ($.browser.msie) {
				$(selector).empty();
			}
			if (url.indexOf('?') == -1) {
				$(selector).load(url, data, this.loaded());
			} else {
				url += '&' + ($.param(data));
				$(selector).load(url, this.loaded());
			}
		},

		/**
		 * Make a nocache url, using a timestamp
		 * @param {String} ref
		 */
		_href: function(ref){
			return (ref.indexOf('?') > -1) ? ref + '&nc='+new Date().getTime() : ref + '?nc='+new Date().getTime();
		},

		/*
		 * others
		 */

		/**
		 * apply effect to elements that are only present
		 */
		_autoFx: function(){
			setTimeout(function(){
				$(".auto-highlight").effect("highlight", {color: "#9FC9FF"}, 2500);
			}, 1000);
			setTimeout(function(){
				$(".auto-hide").fadeOut("slow");
			}, 3000);
			setTimeout(function(){
				$(".auto-slide").slideUp(1500);
			}, 11000);
		},

		/**
		 * Check and cut the text of the selector container only if the text is longer than the maxLength parameter
		 * @param {String} selector JQuery selector
		 * @param {int} maxLength
		 */
		textCutter: function(selector, maxLength){
			if(!maxLength){
				maxLength = 100;
			}
			$(selector).each(function(){
				if($(this).text().length > maxLength && !$(this).hasClass("text-cutted")){
					$(this).prop('title', $(this).text());
					$(this).css('cursor', 'pointer');
					$(this).html($(this).text().substring(0, maxLength) + "[...<img src='"+imgPath+"bullet_add.png' />]");
					$(this).addClass("text-cutted");
				}
			});
		},

		createMessage: function(message){
			if (!$('#info-box').length) $("body").append("<div id='info-box' class='ui-widget-header ui-corner-all auto-slide' >"+message+"</div>")
			else $('#info-box').html(message).show();
			this._autoFx();
		},

		/**
		 * Create a error popup to display an error message
		 * @param {String} message
		 */
		createErrorMessage: function(message){
			this.createMessage(message);
			$('#info-box').addClass('ui-state-error');
		},

		/**
		 * Create an info popup to display a message
		 * @param {String} message
		 */
		createInfoMessage: function(message){
			this.createMessage(message);
			$('#info-box').removeClass('ui-state-error');
		},

		/**
		 * Check if a flahs player is found in the plugins list
		 * @return {boolean}
		 */
		isFlashPluginEnabled: function(){
			if($.browser.msie){
				var hasFlash = false;
				try {
					var fo = new ActiveXObject('ShockwaveFlash.ShockwaveFlash');
					if (fo) hasFlash = true;
				}
				catch(e){
					if (navigator.mimeTypes ["application/x-shockwave-flash"] != undefined) hasFlash = true;
				}
				return hasFlash;
			}
			else{
				if (navigator.plugins != null && navigator.plugins.length > 0) {
					for (i in navigator.plugins) {
						if (/(Shockwave|Flash)/i.test(navigator.plugins[i]['name'])) {
							return true;
						}
					}
				}
			}
			return false;
		},

		//http://requirejs.org/docs/faq-advanced.html
		loadCss: function(url) {
				var link = document.createElement("link");
				link.type = "text/css";
				link.rel = "stylesheet";
				link.href = url;
				document.getElementsByTagName("head")[0].appendChild(link);
		},
		
		/**
		 * sinple _url implementation, requires layout_header to set some global variables
		 */
		_url: function(action, module, extension) {
			module = typeof module == 'undefined' ? ctx_module : module;
			extension = typeof extension == 'undefined' ? ctx_extension : extension;
			return root_url + extension + '/' + module + '/' + action;
		}
	});

	return Helpers;
});