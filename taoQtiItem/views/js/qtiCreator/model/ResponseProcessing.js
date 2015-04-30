define([
    'lodash',
    'taoQtiItem/qtiCreator/model/mixin/editable',
    'taoQtiItem/qtiItem/core/ResponseProcessing'
], function(_, editable, ResponseProcessing){
    "use strict";
    var methods = {};
    _.extend(methods, editable);
    _.extend(methods, {
        getDefaultAttributes : function(){
            return {};
        },
        setProcessingType : function(processingType){
        
            if(this.processingType !== processingType){
                
                if(this.processingType === 'custom'){

                    //change all response template to default : "correct"
                    _.each(this.getRelatedItem().getResponses(), function(r){
                         r.setTemplate('MATCH_CORRECT');
                    });
                }
                
                this.processingType = processingType;
            }

        }
    });

    return ResponseProcessing.extend(methods);
});