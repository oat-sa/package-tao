define([
    'jquery',
    'taoQtiItem/qtiCreator/widgets/static/Widget',
    'taoQtiItem/qtiCreator/widgets/static/text/states/states',
    'tpl!taoQtiItem/qtiCreator/tpl/toolbars/textBlock'
], function($, Widget, states, toolbarTpl){

    var TextWidget = Widget.clone();

    TextWidget.initCreator = function(){

        Widget.initCreator.call(this);

        this.registerStates(states);

    };

    TextWidget.buildContainer = function(){

        var $wrap = $('<div>', {'data-serial' : this.element.serial, 'data-qti-class' : '_container', 'class' : 'widget-box widget-block widget-textBlock'})
            .append($('<div>', {'data-html-editable' : true}));

        this.$original.wrapInner($wrap);

        this.$container = this.$original.children('.widget-box');
    };

    TextWidget.createToolbar = function(){

        var _this = this,
            $tlb = $(toolbarTpl({
            serial : this.serial,
            state : 'active'
        }));

        this.$container.append($tlb);

        $tlb.find('[data-role="delete"]').on('click.widget-box', function(e){
            e.stopPropagation();//to prevent direct deleting;
            _this.changeState('deleting');
        });

        return this;
    };
    
    return TextWidget;
});