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

define(['jquery', 'i18n', 'context', 'helpers', 'ui/feedback', 'jqueryui'], function($, __, context, helpers, feedback) {
	
    var UiBootstrap = {
            init: function(options) {
                var self = this;
                
                this._$tabs = $('#tabs');
                
                this.initAjax();
                this.initNav();
                
                //create tabs
                this.tabs = this._$tabs.tabs({
                        load: function(){
                                $("#section-trees").empty().css({display: 'none'});
                                $("#section-actions").empty().css({display: 'none'});
                                self.initTrees();
                        },
                        select: function(event, ui) {
                                $("#section-trees").empty().css({display: 'none'});
                                $("#section-actions").empty().css({display: 'none'});
                                $("#" + self.tabs.attr('id') + " > .ui-tabs-panel").each(function(){
                                        if ($(this).attr('id') !== ui.panel.id) {
                                                $(this).empty();
                                        }
                                });
                        }
                });

                    //Enable the closing tab if added after the init
                    this.tabs.tabs("option", "tabTemplate", '<li class="closable"><a href="#{href}"><span>#{label}</span></a><span class="tab-closer" title="'+__('Close tab')+'">X</span></li>');
                    this.tabs.on("tabsadd", function(event, ui) {
                            //Close the new content div
                            $(ui.panel).addClass('ui-tabs-hide');
                    });
                    //Closer tab icon
                    $(document).on('click', '.tab-closer', function(e) {
                            e.preventDefault();
                            self.tabs.tabs('remove', $(this).parent().index());
                            //Select another by default ?
                            self.tabs.tabs('select', 0);
                    });
            },

            /**
             * initialize common ajavx behavior
             */
            initAjax: function(){
                var self = this;
                
                    //just before an ajax request
                    $("body").ajaxSend(function(event,request, settings){
                            helpers.loading();
                    });

                    //when an ajax request complete
                    $("body").ajaxComplete(function(event, request, settings){
                            helpers.loaded();

                            if (settings.dataType === 'html') {
                                    helpers._autoFx();
                                    self.initSize();
                            }
                    });

                    //intercept errors
                    $(document).ajaxError(function(event, request, settings, exception){
                        
                        var errorMessage = __('Unknown Error');

                        if(request.status === 404 && settings.type === 'HEAD'){

                            //consider it as a "test" to check if resource exists
                            return;

                        }else if(request.status === 404 || request.status === 500){

                            try{
                                // is it a common_AjaxResponse? Let's "ducktype"
                                var ajaxResponse = $.parseJSON(request.responseText);
                                if (	ajaxResponse !== null &&
                                                typeof ajaxResponse['success'] !== 'undefined' &&
                                                typeof ajaxResponse['type'] !== 'undefined' &&
                                                typeof ajaxResponse['message'] !== 'undefined' &&
                                                typeof ajaxResponse['data'] !== 'undefined'){

                                        errorMessage = request.status + ': ' + ajaxResponse.message;
                                }
                                else{
                                        errorMessage = request.status + ': ' + request.responseText;
                                }

                            }
                            catch (exception){
                                // It does not seem to be valid JSON.
                                errorMessage = request.status + ': ' + request.responseText;
                            }

                        }else if(request.status === 403){

                            window.location = context.root_url + 'tao/Main/logout';
                        }

                        feedback().error(errorMessage);
                    });
            },

            /**
             * initialize common naviguation
             */
            initNav: function(){
                    //load the links target into the main container instead of loading a new page
                    $(document).off('click', 'a.nav').on('click', 'a.nav', function() {
                            try{
                                    helpers._load(helpers.getMainContainerSelector(helpers.tabs), this.href);
                            }
                            catch(exp){return false;}
                            return false;
                    });
            },

            /**
             * initialize the tree component
             */
            initTrees: function(callback){
                    //left menu trees init by loading the tab content
                    if(this.tabs.length > 0){
                        var $sectionTrees = $('#section-trees');
                        
                        //get the link text of the selected tab
                        var section = $("li a[href=#" + $('.ui-tabs-panel')[this.tabs.tabs('option', 'selected')].id + "]:first").attr('id');
                        if (section !== undefined) {
                                $.ajax({
                                        url: context.root_url + 'tao/Main/getSectionTrees',
                                        type: "GET",
                                        data: {
                                                section: section,
                                                structure: context.shownStructure,
                                                ext: context.shownExtension
                                        },
                                        dataType: 'html',
                                        success: function(response){
                                                if(!response){
                                                        $sectionTrees.css({display: 'none'});
                                                } else if($sectionTrees.css('display') === 'none'){
                                                        $sectionTrees.css({display: 'block'});
                                                }
                                                $sectionTrees.html(response);
                                                if (callback !== undefined) callback();
                                        }
                                });
                        }
                    }
            },

            /**
             * initialize the actions component
             */
            initActions: function(uri, classUri){
                    //left menu actions init by loading the tab content
                    if(this.tabs && this.tabs.length > 0){
                        var $sectionActions = $('#section-actions');
                        
                        //get the link text of the selected tab
                        var section = $("li a[href=#" + $('.ui-tabs-panel')[this.tabs.tabs('option', 'selected')].id + "]:first").attr('id');
                            $.ajax({
                                    url: context.root_url + 'tao/Main/getSectionActions',
                                    type: "GET",
                                    data: {
                                            section: section,		
                                            structure: context.shownStructure,
                                            ext: context.shownExtension,
                                            uri: uri,
                                            classUri: classUri
                                    },
                                    dataType: 'html',
                                    success: function(response){
                                            if(!response) {
                                                $sectionActions.css({display: 'none'});
                                            } else if($sectionActions.css('display') === 'none') {
                                                $sectionActions.css({display: 'block'});
                                            }
                                            $sectionActions.html(response);
                                            $(document).trigger('actionInitiated', [response]);
                                    }
                            });
                    }
            },

            /**
             * re-calculate the container size regarding the components content
             */
            initSize: function(){
                    //set up the container size
                    var $myPanel = $('.ui-tabs-panel')[this.tabs.tabs('option', 'selected')];
                    if($myPanel){
                        var uiTab = $myPanel.id;
                        var $tabContainer =  $("div#"+uiTab);
                        var $sectionActions = $('#section-actions');
                        var $sectionTrees = $('#section-trees');
                        if($sectionActions.html() == '' && $sectionTrees.html()  == '' && $tabContainer.css('width') === '79.5%' ){
                            $tabContainer.css({'width': '100%', 'left': 0});
                        }
                        if($sectionActions.html() != '' || $sectionTrees.html()  != '' ){
                            $tabContainer.css({'width': '79.5%', 'float': 'right'});
                        }
                    }
            },

            initMenuBar: function(){
             /*   var self = this;
                    //add a focus selector
                    var lastFocussed = null;
                    $(':text').live('focus',function(){
                            lastFocussed = this;
                    });

                    //initialize the media manager menu
                    $("#main-menu .file-manager").fmload({type: 'file', showselect: false}, lastFocussed, function(element, url){
                            if(lastFocussed != null){
                                    $(lastFocussed).val($(lastFocussed).val() + url);
                            }
                    });

                    //initialize the settings menu
                    $("#main-menu .settings-loader").click(function(){
                            this._load(helpers.getMainContainerSelector(self.tabs), this.href);
                            return false;
                    });*/
            }

    };

    return UiBootstrap;
});
