define([
    'jquery',
    'lodash',
    'taoQtiItem/qtiCreator/editor/preview',
    'taoQtiItem/qtiCreator/editor/preparePrint',
    'taoQtiItem/qtiCreator/helper/panel',
    'taoQtiItem/qtiCreator/helper/itemLoader',
    'taoQtiItem/qtiCreator/helper/creatorRenderer',
    'taoQtiItem/qtiCreator/helper/commonRenderer', //for the preview
    // css editor related
    'taoQtiItem/qtiCreator/editor/styleEditor/fontSelector',
    'taoQtiItem/qtiCreator/editor/styleEditor/colorSelector',
    'taoQtiItem/qtiCreator/editor/styleEditor/fontSizeChanger',
    'taoQtiItem/qtiCreator/editor/styleEditor/itemResizer',
    'taoQtiItem/qtiCreator/editor/styleEditor/styleEditor',
    'taoQtiItem/qtiCreator/editor/styleEditor/styleSheetToggler',
    'taoQtiItem/qtiCreator/editor/editor'
], function(
    $,
    _,
    preview,
    preparePrint,
    panel,
    loader,
    creatorRenderer,
    commonRenderer,
    fontSelector,
    colorSelector,
    fontSizeChanger,
    itemResizer,
    styleEditor,
    styleSheetToggler,
    editor
    ){

    // workaround to get ajax loader out of the way
    // item editor has its own loader with the correct background color
    var $loader = $('#ajax-loading');
    var loaderLeft = $loader.css('left');

    var _initUiComponents = function(item, widget, config){



        styleEditor.init(widget.element, config);

        styleSheetToggler.init(config);

        // CSS widgets
        fontSelector();
        colorSelector();
        fontSizeChanger();
        itemResizer(widget.element);
        preview.init($('.preview-trigger'), item, widget);

        preparePrint();


        editor.initGui(widget);

    };

    return {
        /**
         * 
         * @param {object} config (baseUrl, uri, lang)
         */
        start : function(config){

            var $tabs = $('#tabs');
            var $tabNav = $('ul.ui-tabs-nav > li', $tabs);
            var currentTab = $tabs.tabs('option', 'selected');


            $loader.css('left', '-10000px');

            //load item from REST service
            loader.loadItem({uri : config.uri}, function(item){

                var $itemContainer = $('#item-editor-scroll-inner');

                //configure commonRenderer for the preview
                commonRenderer.setOption('baseUrl', config.baseUrl);
                commonRenderer.setContext($itemContainer);

                //load creator renderer
                creatorRenderer.setOptions(config);
                creatorRenderer.get().load(function(){

                    var widget;

                    item.setRenderer(this);

                    //render item (body only) into the "drop-area"
                    $itemContainer.append(item.render());

                    //"post-render it" to initialize the widget
                    widget = item.postRender(_.clone(config));
                    
                    _initUiComponents(item, widget, config);
                    panel.initFormVisibilityListener();
                    panel.toggleInlineInteractionGroup();

                    //leaving the tab, we try to let the place as clean as possible.
                    $tabs.off('tabsselect.qti-creator').on('tabsselect.qti-creator', function(e, ui){
                        var index = $tabNav.index($(this).parents('li'));
                        if(index !== currentTab){
                            //remove global events
                            $(window).off('.qti-widget');
                            $(document).off('.qti-widget');
                            $(document).off('.qti-creator');
                            $tabs.off('tabsselect.qti-creator');
        
                            $loader.css('left', loaderLeft);
                        }
                    });
                    
                }, item.getUsedClasses());

            });

        }
    };
});
