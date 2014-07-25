define([
    'jquery',
    'taoQtiItem/qtiCreator/widgets/static/Widget',
    'taoQtiItem/qtiCreator/widgets/static/img/states/states',
    'taoQtiItem/qtiCreator/widgets/static/helpers/widget',
    'tpl!taoQtiItem/qtiCreator/tpl/toolbars/media',
    'taoQtiItem/qtiCreator/widgets/static/helpers/inline',
    'taoQtiItem/qtiItem/helper/util'
], function($, Widget, states, helper, toolbarTpl, inlineHelper, itemUtil){

    var ImgWidget = Widget.clone();

    ImgWidget.initCreator = function(options){

        var _this = this,
            img = _this.element,
            baseUrl = options.baseUrl;

        this.registerStates(states);

        Widget.initCreator.call(this);

        inlineHelper.togglePlaceholder(this);

        //check file exists:
        inlineHelper.checkFileExists(this, 'src', options.baseUrl);
        $('#item-editor-scope').on('filedelete.resourcemgr.' + this.element.serial, function(e, src){
            if(itemUtil.fullpath(img.attr('src'), baseUrl) === itemUtil.fullpath(src, baseUrl)){
                img.attr('src', '');
                inlineHelper.togglePlaceholder(_this);
            }
        });
    };

    ImgWidget.destroy = function(){
        $('#item-editor-scope').off('.' + this.element.serial);
    };

    ImgWidget.getRequiredOptions = function(){
        return ['baseUrl', 'uri', 'lang', 'mediaManager'];
    };

    ImgWidget.buildContainer = function(){

        helper.buildInlineContainer(this);

        return this;
    };

    ImgWidget.createToolbar = function(){

        helper.createToolbar(this, toolbarTpl);

        return this;
    };

    return ImgWidget;
});
