define([
    'taoQtiItem/qtiCreator/widgets/static/Widget',
    'taoQtiItem/qtiCreator/widgets/static/object/states/states',
    'taoQtiItem/qtiCreator/widgets/static/helpers/widget',
    'tpl!taoQtiItem/qtiCreator/tpl/toolbars/media',
    'taoQtiItem/qtiCreator/widgets/static/helpers/inline',
    'tpl!taoQtiItem/qtiCreator/tpl/notifications/widgetOverlay',
    'i18n'
], function(Widget, states, helper, toolbarTpl, inlineHelper, overlayTpl, __) {

    var ObjectWidget = Widget.clone();

    ObjectWidget.initCreator = function() {
        
        this.registerStates(states);
        
        Widget.initCreator.call(this);
        
        inlineHelper.togglePlaceholder(this);
        
        this.notAvailable();
    };
    
    ObjectWidget.getRequiredOptions = function(){
        return ['baseUrl', 'uri', 'lang', 'mediaManager'];
    };
    
    ObjectWidget.buildContainer = function(){
        
        helper.buildBlockContainer(this);
        
        return this;
    };
    
    ObjectWidget.createToolbar = function() {
        
         helper.createToolbar(this, toolbarTpl);

        return this;
    };
    
    ObjectWidget.notAvailable = function(){
        
        
        this.$container.append(overlayTpl({
            message : __('Editing this element is not supported currently.')
        }));
        this.$container.attr('contenteditable', false);
         this.$container.css({cursor:'default'});
        this.$container.on('click, mousedown', function(e){
            e.stopPropagation();
        });
    };
    
    return ObjectWidget;
});
