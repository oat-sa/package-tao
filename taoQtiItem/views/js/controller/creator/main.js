/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */
define([
    'jquery',
    'lodash',
    'module',
    'async',
    'core/promise',
    'history',
    'layout/loading-bar',
    'layout/section',
    'taoQtiItem/qtiCreator/model/helper/event',
    'taoQtiItem/qtiCreator/helper/panel',
    'taoQtiItem/qtiCreator/helper/itemLoader',
    'taoQtiItem/qtiCreator/helper/creatorRenderer',
    'taoQtiItem/qtiCreator/helper/commonRenderer', //for read-only element : preview + xinclude
    'taoQtiItem/qtiCreator/helper/qtiElements',
    'taoQtiItem/qtiCreator/helper/xincludeRenderer',
    // css editor related
    'taoQtiItem/qtiCreator/editor/editor',
    'taoQtiItem/qtiCreator/editor/interactionsToolbar',
    'taoQtiItem/qtiCreator/editor/customInteractionRegistry',
    'taoQtiItem/qtiCreator/editor/infoControlRegistry',
    'taoQtiItem/qtiCreator/editor/blockAdder/blockAdder'
], function(
    $,
    _,
    module,
    async,
    Promise,
    history,
    loadingBar,
    section,
    event,
    panel,
    loader,
    creatorRenderer,
    commonRenderer,
    qtiElements,
    xincludeRenderer,
    editor,
    interactionsToolbar,
    ciRegistry,
    icRegistry,
    blockAdder
    ){
    'use strict';

    loadingBar.start();

    function _getAuthoringElements(interactionModels){

        var toolbarInteractions = qtiElements.getAvailableAuthoringElements();

        _.each(interactionModels, function(interactionModel){
            var data = ciRegistry.getAuthoringData(interactionModel.getTypeIdentifier());
            if(data.tags && data.tags[0] === interactionsToolbar.getCustomInteractionTag()){
                toolbarInteractions[data.qtiClass] = data;
            }else{
                throw 'invalid authoring data for custom interaction';
            }
        });

        return toolbarInteractions;
    }


    function _initializeInteractionsToolbar($toolbar, interactionModels){

        //create toolbar:
        interactionsToolbar.create($toolbar, _getAuthoringElements(interactionModels));

        //init accordions:
        panel.initSidebarAccordion($toolbar);
        panel.closeSections($toolbar.find('section'));
        panel.openSections($toolbar.find('#sidebar-left-section-common-interactions'), false);

        //init special subgroup
        panel.toggleInlineInteractionGroup();

    }

    function _initializeElementAdder(item, $itemPanel, interactionModels){

        var authoringElements = _getAuthoringElements(interactionModels);

        blockAdder.create(item, $itemPanel, authoringElements);
    }

    function _initializeHooks(uiHooks, configProperties){

        require(_.values(uiHooks), function(){

            _.each(arguments, function(hook){
                hook.init(configProperties);
            });
        });
    }

    return {
        /**
         *
         * @param {object} _config (baseUrl, uri, lang)
         */
        start : function(_config){
            
            var config, 
                configProperties,
                //references to useful dom element
                $doc = $(document),
                $editorScope = $('#item-editor-scope'),
                $itemContainer = $('#item-editor-scroll-inner'),
                $propertySidebar = $('#item-editor-item-widget-bar');
            
            //first all, start loading bar
            loadingBar.start();
            
            //init config
            config = _.merge({}, _config || {},  module.config() || {});
            
            //reinitialize the renderer:
            creatorRenderer.get(true, config);

            configProperties = config.properties;

            configProperties.dom = {
                getEditorScope : function(){
                    return $editorScope;
                },
                getMenuLeft : function(){
                    return $editorScope.find('.item-editor-menu.lft');
                },
                getMenuRight : function(){
                    return $editorScope.find('.item-editor-menu.rgt');
                },
                getInteractionToolbar : function(){
                    return $editorScope.find('#item-editor-interaction-bar');
                },
                getItemPanel : function(){
                    return $itemContainer;
                },
                getItemPropertyPanel : function(){
                    return $editorScope.find('#sidebar-right-item-properties');
                },
                getItemStylePanel : function(){
                    return $propertySidebar.find('#item-style-editor-bar');
                },
                getModalContainer : function(){
                    return $editorScope.find('#modal-container');
                }
            };

            //back button
            $('#authoringBack').on('click', function(e){
                e.preventDefault();

                if (history) {
                    history.back();
                }

            });

            //initialize hooks
            _initializeHooks(_.union(config.uiHooks, config.hooks), configProperties);

            async.parallel([
                //register custom interactions
                function(callback){
                    ciRegistry.register(config.interactions);
                    ciRegistry.loadAll(function(hooks){
                        callback(null, hooks);
                    });
                },
                //register info controls
                function(callback){
                    icRegistry.register(config.infoControls);
                    icRegistry.loadAll(function(hooks){
                        callback(null, hooks);
                    });
                },
                //load item
                function(callback){
                    loader.loadItem({uri : configProperties.uri, label : configProperties.label}, function(item){

                        //configure commonRenderer for the preview
                        commonRenderer.setOption('baseUrl', configProperties.baseUrl);
                        commonRenderer.setContext(configProperties.dom.getItemPanel());

                        //set reference to item object
                        $editorScope.data('item', item);

                        //fires event itemloaded
                        $doc.trigger('itemloaded.qticreator', [item]);

                        //set useful data :
                        item.data('uri', configProperties.uri);

                        callback(null, item);

                    });
                }
            ], function(err, res){

                //get results from paralleled ajax calls:
                var interactionHooks = res[0],
                    infoControlHooks = res[1],
                    item = res[2];

                //init interaction sidebar
                _initializeInteractionsToolbar(configProperties.dom.getInteractionToolbar(), interactionHooks);
                if(config.properties['multi-column']){
                    _initializeElementAdder(item, configProperties.dom.getItemPanel(), interactionHooks);
                }

                //load creator renderer
                creatorRenderer
                    .get()
                    .setOptions(configProperties)
                    .load(function(){
                        var widget;

                        //set renderer
                        item.setRenderer(this);

                        //render item (body only) into the "drop-area"
                        configProperties.dom.getItemPanel().append(item.render());

                        //"post-render it" to initialize the widget
                        Promise
                         .all(item.postRender(_.clone(configProperties)))
                         .then(function(){
                            widget = item.data('widget');
                            _.each(item.getComposingElements(), function(element){
                                if(element.qtiClass === 'include'){
                                    xincludeRenderer.render(element.data('widget'), config.properties.baseUrl);
                                }
                            });

                            editor.initGui(widget, configProperties);
                            panel.initSidebarAccordion($propertySidebar);
                            panel.initFormVisibilityListener();

                            //hide loading bar when completed
                            loadingBar.stop();

                            //destroy by leaving the section
                            section.on('hide', function(hiddenSection){
                                if(hiddenSection.id === 'authoring'){

                                    //remove global events
                                    $(window).off('.qti-widget');
                                    $doc.off('.qti-widget').off('.qti-creator');
                                }
                            });

                            //set reference to item widget object
                            $editorScope.data('widget', item);

                            //fires event itemloaded
                            $doc.trigger('widgetloaded.qticreator', [widget]);

                            //init event listeners:
                            event.initElementToWidgetListeners();
                        })
                        .catch(function(err){
                            console.error(err);
                        });

                    }, item.getUsedClasses());

            });

        }
    };
});
