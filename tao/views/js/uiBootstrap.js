/**
 * UiBootstrap class enable you to run the naviguation mode,
 * bind the events on the main components and initialize handlers
 *
 *
 * @require jquery >= 1.3.2 [http://jquery.com/]
 * @require [helpers.js]
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @author Jehan Bihin (using class.js)
 */

define([
    'jquery',
    'i18n',
    'context',
    'helpers',
    'ui/feedback',
    'layout/actions',
    'layout/tree',
    'uiForm',
    'jqueryui'],

    function ($, __, context, helpers, feedback, actions, treeFactory, uiForm) {

    console.warn('Hello I am the UIBootstrap and I am deprecated. I am there since TAO 1.0 but now I am tired, I need to retire.');

    /*
     * DEPRECATED
     */

    var UiBootstrap = {
        init: function (options) {

            //TODO move tabs to layout/section or layout/tabs

            var self = this;
            var $tabs = $('.section-container');

            this.initAjax();
            this.initNav();

            //create tabs
            this.tabs = $tabs.tabs({
                show: function (e, ui) {
                    var $section = $(ui.panel);

                    // update hash in uri
                    window.location.href = ui.tab.href;

                    window.scrollTo(0,0);

                    context.section = $section.attr('id').replace('panel-', '');

                    actions.init($section);


                    $('.taotree', $section).each(function(){
                        var $treeElt = $(this),
                            $actionBar = $('.tree-action-bar-box', $section);

                        var rootNode = $treeElt.data('rootnode');
                        treeFactory($treeElt, $treeElt.data('url'), {
                            serverParameters : {
                                extension   : context.shownExtension,
                                perspective : context.shownStructure,
                                section     : context.section,
                                classUri	: rootNode ? rootNode : undefined
                            },
                            actions : {
                                'selectClass'    : $treeElt.data('action-selectclass'),
                                'selectInstance' : $treeElt.data('action-selectinstance'),
                                'moveInstance'   : $treeElt.data('action-moveinstance'),
                                'delete'         : $treeElt.data('action-delete')
                            }
                        });
                        $treeElt.on('ready.taotree', function() {
                            $actionBar.addClass('active');
                        });
                    });
                    if ($('.taotree', $section).length == 0) {
                        $('.navi-container',$section).hide();
                        helpers._load(helpers.getMainContainerSelector(), ui.tab.getAttribute('data-url'));
                    };


                    // navBar.init() replace
                }
            });

            //Enable the closing tab if added after the init
            this.tabs.tabs("option", "tabTemplate", '<li class="closable"><a href="#{href}"><span>#{label}</span></a><span class="tab-closer" title="' + __('Close tab') + '">x</span></li>');
            this.tabs.on("tabsadd", function (event, ui) {

                // @todo: add link to sub-menu
                //Close the new content div
                $(ui.panel).addClass('ui-tabs-hide');
            });
            //Closer tab icon
            $(document).on('click', '.tab-closer', function (e) {
                e.preventDefault();
                self.tabs.tabs('remove', $(this).parent().index());
                // @todo: remove link from sub-menu


                //Select another by default ?
                self.tabs.tabs('select', 0);
            });

            var $tabContainer = $('.tab-container');
            if($tabContainer.find('li').length < 2) {
                $tabContainer.hide();
            }
        },

        /**
         * initialize common ajax behavior
         */
        initAjax: function () {

            //TODO move this somewhere else (main controller?)

            var self = this,
                $body = $(document.body);

            //just before an ajax request
            $body.ajaxSend(function (event, request, settings) {
                helpers.loading();
            });

            //when an ajax request complete
            $body.ajaxComplete(function (event, request, settings) {
                helpers.loaded();

                if (settings.dataType === 'html') {
                    helpers._autoFx();
                }
            });

            //intercept errors
            $(document).ajaxError(function (event, request, settings, exception) {

                var errorMessage = __('Unknown Error');

                if (request.status === 404 && settings.type === 'HEAD') {

                    //consider it as a "test" to check if resource exists
                    return;

                }
                else if (request.status === 404 || request.status === 500) {

                    try {
                        // is it a common_AjaxResponse? Let's "duck type"
                        var ajaxResponse = $.parseJSON(request.responseText);
                        if (ajaxResponse !== null &&
                            typeof ajaxResponse['success'] !== 'undefined' &&
                            typeof ajaxResponse['type'] !== 'undefined' &&
                            typeof ajaxResponse['message'] !== 'undefined' &&
                            typeof ajaxResponse['data'] !== 'undefined') {

                            errorMessage = request.status + ': ' + ajaxResponse.message;
                        }
                        else {
                            errorMessage = request.status + ': ' + request.responseText;
                        }

                    }
                    catch (exception) {
                        // It does not seem to be valid JSON.
                        errorMessage = request.status + ': ' + request.responseText;
                    }

                }
                else if (request.status === 403) {

                    window.location = context.root_url + 'tao/Main/logout';
                }

                feedback().error(errorMessage);
            });
        },

        /**
         * initialize common navigation
         */
        initNav: function () {

            // enable tab functionality on navigation sub menus
            $('nav .menu-dropdown').on('click', function(e) {
                var href = e.target.href,
                    hrefArr,
                    locationArr = (function() {
                        var arr = window.location.href.split('#');
                        if(arr.length < 2) {
                            arr.push('');
                        }
                        return arr;
                    }()),
                    $tab;


                if(!href) {
                    return true;
                }

                hrefArr = e.target.href.split('#');

                $tab = $('#tabs').find('a[href="#' + hrefArr[1] + '"]');

                // already on correct page
                if(locationArr[0] === hrefArr[0]) {

                    // correct tab => do nothing
                    // Note: this cannot depend on hrefArr[1]
                    if(locationArr[1] === $tab.hasClass('.ui-state-active')) {
                        return false;
                    }

                    // wrong tab, update uri hash
                    window.location.href = href;
                    e.preventDefault();
                    $('#tabs').find('a[href="#' + hrefArr[1] + '"]').trigger('click');
                }
            });

            //TODO move this somewhere else (layout/nav)

            //load the links target into the main container instead of loading a new page
            //$(document).off('click', 'a.nav').on('click', 'a.nav', function () {
                //try {
                    //helpers._load(helpers.getMainContainerSelector(helpers.tabs), this.href);
                //}
                //catch (exp) {
                    //return false;
                //}
                //return false;
            //});
        }
    };

    return UiBootstrap;
});
