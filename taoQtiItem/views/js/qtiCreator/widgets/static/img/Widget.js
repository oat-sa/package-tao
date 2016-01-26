define([
    'jquery',
    'taoQtiItem/qtiCreator/widgets/static/Widget',
    'taoQtiItem/qtiCreator/widgets/static/img/states/states',
    'taoQtiItem/qtiCreator/widgets/static/helpers/widget',
    'tpl!taoQtiItem/qtiCreator/tpl/toolbars/media',
    'taoQtiItem/qtiCreator/widgets/static/helpers/inline'
], function($, Widget, states, helper, toolbarTpl, inlineHelper){

    var ImgWidget = Widget.clone();

    ImgWidget.initCreator = function(options){

        var _this = this,
            img = _this.element;

        this.registerStates(states);

        Widget.initCreator.call(this);

        inlineHelper.togglePlaceholder(this);

        //check file exists:
        inlineHelper.checkFileExists(this, 'src', options.baseUrl);
        $('#item-editor-scope').on('filedelete.resourcemgr.' + this.element.serial, function(e, src){
            if (this.getAssetManager().resolve(img.attr('src')) === this.getAssetManager().resolve(src)) {
                img.attr('src', '');
                inlineHelper.togglePlaceholder(_this);
            }
        });
    };

    ImgWidget.destroy = function(){
        $('#item-editor-scope').off('.' + this.element.serial);
    };

    ImgWidget.getRequiredOptions = function(){
        return ['baseUrl', 'uri', 'lang', 'mediaManager', 'assetManager'];
    };

    ImgWidget.buildContainer = function(){

        helper.buildInlineContainer(this);

        this.$container.css({
            width: this.element.attr('width'),
            height: this.element.attr('height')
        });
        this.$original[0].setAttribute('width', '100%');
        this.$original[0].setAttribute('height', '100%');

        return this;
    };

    ImgWidget.createToolbar = function(){

        helper.createToolbar(this, toolbarTpl);

        return this;
    };

    return ImgWidget;
});
