/*
 * Helpers
 */
define(['jquery', 'context', 'jqueryui'], function($, context) {
    
    var parallelLoading = 0;
    var $loader =  $("#ajax-loading");
    
	var Helpers = {
		init: function() {
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
                
                getMainContainer : function($tabs){
                    $tabs = $tabs || $('#tabs');
                    var $mainContainer = $(".main-container");
                    if($tabs.length === 0){
                        return $mainContainer.length === 0 ? false : $mainContainer;
                    }
                    
                    var uiTab = $('.ui-tabs-panel').prop('id');
                    if (typeof $('.ui-tabs-panel')[$tabs.tabs('option', 'selected')] !== 'undefined') {
                            uiTab = $('.ui-tabs-panel')[$tabs.tabs('option', 'selected')].id;
                    }

                    if ($("div#"+uiTab+" div.main-container").css('display') === 'none') {
                            return $("#"+uiTab);
                    }

                    return $("#"+uiTab+" > .main-container");
                    
                },

		/**
		 * @return {String} the current main container jQuery selector (from the opened tab)
		 */
		getMainContainerSelector: function($tabs){
                    var $container = this.getMainContainer($tabs);
                    if($container && $container.length > 0){
                        return $container.selector;
                    }
                    return false;
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
			var elts = $("div#tabs ul.ui-tabs-nav li a");
			var i = 0;
			while (i < elts.length) {
				var elt = elts[i];
				if (elt && elt.id && elt.id === name) {
                                    return i;
				}
				i++;
			}
			return -1;
		},

		openTab: function(title, url, open) {
			if (open == undefined) open = true;
			var idx = this.getTabIndexByUrl(url);
			if (idx == -1) {
				$('#tabs').tabs("add", url, title);
				idx = $('#tabs').tabs("length")-1;
			}
			//If control pressed, not select
			if (open) {
                            $('#tabs').tabs("select", idx);
			}
		},

		getTabIndexByUrl: function(url){
			var elts = $("#tabs ul.ui-tabs-nav li a");
			var i = 0;
			var ret = -1;
			elts.each(function() {
			   var href = $.data(this, 'href.tabs');
			   if (url === href) {
                                ret = i;
                                return;
			   }
			   i++;
			});
			return ret;
		},
                
                closeTab : function(index){
                    if(index > -1){
                        $('#tabs').tabs("remove", index);
                    }
                },

		/**
		 * Add parameters to a tab
		 * @param {Object} tabObj
		 * @param {String} tabName
		 * @param {Object} parameters
		 */
		updateTabUrl: function(tabObj, tabName, url){
			var index = this.getTabIndexByName(tabName);
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
            if (parallelLoading > 0){
                 return; //Need once
            }
            parallelLoading++;
            $(window).on('click', function(e){
                    e.stopPropagation();
                    e.preventDefault();
                    return false;
            });
            $loader.show();
            setTimeout(function(){
                 //we display the overlay only if the request is slow
                 if(parallelLoading === 1){
                     $loader.addClass('overlay');
                 }
            }, 200);
		},

		/**
		 * Complete an async request, once loaded:
		 *  - hide the loader img
		 *  - enable back the submit buttons
		 */
		loaded: function(){
            if (parallelLoading > 1){
                return;
            }
            $(window).off('click');
            $loader.hide()
                    .removeClass('overlay');
            setTimeout(function(){
                parallelLoading--;
            }, 10);
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
			$(selector).hide().empty().show();
			if (url.indexOf('?') === -1) {
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
					$(this).html($(this).text().substring(0, maxLength) + "[...<img src='"+context.taobase_www+"img/bullet_add.png' />]");
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
					if (navigator.mimeTypes ["application/x-shockwave-flash"] !== undefined) hasFlash = true;
				}
				return hasFlash;
			}
			else{
				if (navigator.plugins != null && navigator.plugins.length > 0) {
					for (var i in navigator.plugins) {
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
		 * simple _url implementation, requires layout_header to set some global variables
		 */
		_url: function(action, module, extension) {
                    module = module || context.module;
                    extension = extension || context.extension;
                    return context.root_url + extension + '/' + module + '/' + action;
		}
	};

	return Helpers;
});
