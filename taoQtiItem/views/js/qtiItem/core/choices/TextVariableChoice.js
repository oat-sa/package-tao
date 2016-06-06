define([
    'jquery',
    'lodash',
    'taoQtiItem/qtiItem/core/choices/Choice',
    'taoQtiItem/qtiItem/helper/rendererConfig'
], function($, _, QtiChoice, rendererConfig){

    var QtiTextVariableChoice = QtiChoice.extend({
        init : function(serial, attributes, text){
            this._super(serial, attributes);
            this.val(text || '');
        },
        is : function(qtiClass){
            return (qtiClass === 'textVariableChoice') || this._super(qtiClass);
        },
        val : function(text){
            if(typeof text === 'undefined'){
                return this.text;
            }else{
                if(typeof text === 'string'){
                    this.text = text;
                    $(document).trigger('choiceTextChange', {
                        choice: this,
                        text: text
                    });
                }else{
                    throw 'text must be a string';
                }
            }
            return this;
        },
        render : function(){
            var args = rendererConfig.getOptionsFromArguments(arguments),
                renderer = args.renderer||this.getRenderer(),
                defaultData = {
                    body : this.text
                };

            return this._super(_.merge(defaultData, args.data), args.placeholder, args.subclass, renderer);
        }
    });

    return QtiTextVariableChoice;
});


