/*
 * Helpers
 */
define([
    'lodash',
    'jquery',
    'context',
    'layout/loading-bar',
    'jqueryui'
], function (_, $, context, loadingBar) {


    var Helpers = {
        init: function () {
            /**
             * Extends the JQuery post method for convenience use with Json
             * @param {String} url
             * @param {Object} data
             * @param {Function} callback
             */
            $.postJson = function (url, data, callback) {
                $.post(url, data, callback, "json");
            };
        },

        getMainContainer: function () {
            console.warn('deprecated, use section instead');
            var sectionId,
                sectionIndex;
            if (!context.section) {
                sectionIndex = $('.section-container').tabs('options', 'selected');
                $('.content-panel').eq(sectionIndex).find('.content-block');
            }
            return $('#panel-' + context.section + ' .content-block');
        },

        /**
         * @return {String} the current main container jQuery selector (from the opened tab)
         */
        getMainContainerSelector: function ($tabs) {
            console.warn('deprecated, use section instead');
            var $container = this.getMainContainer();
            if ($container && $container.length > 0) {
                return $container.selector;
            }
            return false;
        },

        /**
         * @param {String} name the name of the tab to select
         */
        selectTabByName: function (name) {
            console.warn('deprecated, use section instead');
            $("#" + name).click();
        },

        /**
         * get the index of the tab identified by name
         * @param {String} name
         * @return the index or -1 if not found
         */
        getTabIndexByName: function (name) {
            console.warn('deprecated, use section instead');
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

        openTab: function (title, url, open) {
            console.warn('deprecated, use section instead');
            open = open || true;
            var idx = this.getTabIndexByUrl(url),
                $tabs = $('#tabs');
            if (idx == -1) {
                $tabs.tabs("add", url, title);
                idx = $tabs.tabs("length") - 1;
            }
            //If control pressed, not select
            if (open) {
                $tabs.tabs("select", idx);
            }
        },

        getTabIndexByUrl: function (url) {
            console.warn('deprecated, use section instead');
            var elts = $("#tabs ul.ui-tabs-nav li a");
            var i = 0;
            var ret = -1;
            elts.each(function () {
                var href = $.data(this, 'href.tabs');
                if (url === href) {
                    ret = i;
                    return;
                }
                i++;
            });
            return ret;
        },

        closeTab: function (index) {
            console.warn('deprecated, use section instead');
            if (index > -1) {
                $('#tabs').tabs("remove", index);
            }
        },

        /**
         * Add parameters to a tab
         * @param {Object} tabObj
         * @param {String} tabName
         * @param {Object} parameters
         */
        updateTabUrl: function (tabObj, tabName, url) {
            console.warn('deprecated, use section instead');
            var index = this.getTabIndexByName(tabName);
            tabObj.tabs('url', index, url);
            tabObj.tabs('enable', index);
        },

        /*
         * Navigation and ajax helpers
         */

        /**
         * Begin an async request, while loading:
         * - show the loader img
         * - disable the submit buttons
         */
        loading: function () {
            console.warn('deprecated, this should be automated');
            $(window).on('click', function (e) {
                e.stopPropagation();
                e.preventDefault();
                return false;
            });
            loadingBar.start();
        },

        /**
         * Complete an async request, once loaded:
         *  - hide the loader img
         *  - enable back the submit buttons
         */
        loaded: function () {
            console.warn('deprecated, this should be automated');
            $(window).off('click');
            loadingBar.stop();
        },

        /**
         * Load url asyncly into selector container
         * @param {String} selector
         * @param {String} url
         */
        _load: function (selector, url, data) {

            url = url || '';

            if (data) {
                data.nc = new Date().getTime();
            }
            else {
                data = {nc: new Date().getTime()};
            }
            $(selector).hide().empty().show();
            if (url.indexOf('?') === -1) {
                $(selector).load(url, data);
            }
            else {
                url += '&' + ($.param(data));
                $(selector).load(url);
            }
        },

        /**
         * Make a nocache url, using a timestamp
         * @param {String} ref
         */
        _href: function (ref) {
            return (ref.indexOf('?') > -1) ? ref + '&nc=' + new Date().getTime() : ref + '?nc=' + new Date().getTime();
        },

        /*
         * others
         */

        /**
         * apply effect to elements that are only present
         */
        _autoFx: function () {
            
            console.warn('deprecated');
            setTimeout(function () {
                $(".auto-highlight").effect("highlight", {color: "#9FC9FF"}, 2500);
            }, 1000);
            setTimeout(function () {
                $(".auto-hide").fadeOut("slow");
            }, 3000);
            setTimeout(function () {
                $(".auto-slide").slideUp(1500);
            }, 11000);
        },

        /**
         * Check and cut the text of the selector container only if the text is longer than the maxLength parameter
         * @param {String} selector JQuery selector
         * @param {int} maxLength
         */
        textCutter: function (selector, maxLength) {
            console.warn('deprecated, use css instead');
            if (!maxLength) {
                maxLength = 100;
            }
            $(selector).each(function () {
                if ($(this).text().length > maxLength && !$(this).hasClass("text-cutted")) {
                    $(this).prop('title', $(this).text());
                    $(this).css('cursor', 'pointer');
                    $(this).html($(this).text().substring(0, maxLength) + "[...<img src='" + context.taobase_www + "img/bullet_add.png' />]");
                    $(this).addClass("text-cutted");
                }
            });
        },

        createMessage: function (message) {
            console.warn('deprecated, use feedback instead');
            if (!$('#info-box').length) {
                $("body").append("<div id='info-box' class='ui-widget-header ui-corner-all auto-slide' >" + message + "</div>")
            }
            else {
                $('#info-box').html(message).show();
            }
            this._autoFx();
        },

        /**
         * Create a error popup to display an error message
         * @param {String} message
         */
        createErrorMessage: function (message) {
            this.createMessage(message);
            $('#info-box').addClass('ui-state-error');
        },

        /**
         * Create an info popup to display a message
         * @param {String} message
         */
        createInfoMessage: function (message) {
            this.createMessage(message);
            $('#info-box').removeClass('ui-state-error');
        },

        /**
         * Check if a flahs player is found in the plugins list
         * @return {boolean}
         */
        isFlashPluginEnabled: function () {
            return   (typeof navigator.plugins !== "undefined" && typeof navigator.plugins["Shockwave Flash"] === "object") || 
                     (window.ActiveXObject && (new window.ActiveXObject("ShockwaveFlash.ShockwaveFlash")) !== false);
        },

        //http://requirejs.org/docs/faq-advanced.html
        loadCss: function (url) {
            console.warn('deprecated');
            var link = document.createElement("link");
            link.type = "text/css";
            link.rel = "stylesheet";
            link.href = url;
            document.getElementsByTagName("head")[0].appendChild(link);
        },

        /**
         * simple _url implementation, requires layout_header to set some global variables
         */
        _url: function (action, controller, extension, params) {
    
            var url;

            if(typeof action !== 'string' || typeof controller !== 'string' || typeof extension !== 'string'){
                throw new TypeError('All parts are required to build an URL');
            }

            url = context.root_url + extension + '/' + controller + '/' + action;

            if(_.isString(params)) {
                url += '?' + params;
            } else if (_.isPlainObject(params)) {
                url += '?' + $.param(params);
            }
            return url;
        }
    };

    return Helpers;
});
