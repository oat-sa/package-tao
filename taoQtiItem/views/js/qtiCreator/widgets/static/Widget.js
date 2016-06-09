define([
    'jquery',
    'taoQtiItem/qtiCreator/widgets/Widget',
    'taoQtiItem/qtiCreator/editor/widgetToolbar'
], function($, Widget, toolbar){
    "use strict";

    var StaticWidget = Widget.clone();

    StaticWidget.initCreator = function(){

        Widget.initCreator.call(this);

        this.createToolbar();
    };

    StaticWidget.buildContainer = function(){
        var $wrap = $('<div>', {'data-serial' : this.element.serial, 'class' : 'widget-box'});
        this.$original.wrap($wrap);
        this.$container = this.$original.parent();
    };

    StaticWidget.createToolbar = function(){

        return this;
    };

    StaticWidget.getAssetManager = function () {
        if (!this.options || !this.options.assetManager) {
            throw new Error('Asset Manager have to be provided');
        }
        return this.options.assetManager;
    };

    StaticWidget.createOkButton = function(){

        var _this = this;

        //@todo: use handlebars tpl instead?
        this.$container.append($('<button>', {
            'class' : 'btn-info small',
            'type' : 'button',
            'text' : 'OK',
            'data-edit' : 'active'
        }).css({
            margin : '5px 10px',
            display : 'none'
        }).on('click.qti-widget', function(e){
            e.stopPropagation();
            _this.changeState('sleep');
        }));
    };

    return StaticWidget;
});