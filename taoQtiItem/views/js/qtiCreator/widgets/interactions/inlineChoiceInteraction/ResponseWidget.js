define([
    'taoQtiItem/qtiCommonRenderer/renderers/Renderer',
    'taoQtiItem/qtiCommonRenderer/helpers/Helper',
    'lodash',
    'i18n'
], function(CommonRenderer, helper, _, __){

    var ResponseWidget = {
        create : function(widget, callback){

            var _this = this,
                interaction = widget.element,
                $placeholder = $('<select>'),
                $responseWidget = widget.$container.find('.widget-response').append($placeholder);

            this.commonRenderer = new CommonRenderer({shuffleChoices : false});
            this.commonRenderer.load(function(){

                interaction.render({}, $placeholder, '', this);
                interaction.postRender({
                    allowEmpty : false,
                    placeholderText : __('select correct choice')
                }, '', this);

                callback.call(_this, this);
                
                $responseWidget.siblings('.padding').width($responseWidget.width() + 50);//plus icons width
                
            }, ['inlineChoice', 'inlineChoiceInteraction']);

        },
        setResponse : function(widget, response){
            this.commonRenderer.setResponse(widget.element, this.formatResponse(response));
        },
        destroy : function(widget){
            widget.$container.find('.widget-response').empty();
            widget.$container.find('.padding').removeAttr('style');
        },
        formatResponse : function(response){
            if(!_.isString(response)){
                response = _.values(response);
                if(response && response.length){
                    response = response[0];
                }
            }
            return {base : {identifier : response}};
        },
        unformatResponse : function(formatedResponse){

            var response = [];

            if(formatedResponse.base && formatedResponse.base.identifier){
                response.push(formatedResponse.base.identifier);
            }
            return response;
        }
    };

    return ResponseWidget;
});