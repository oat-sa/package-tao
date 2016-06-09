define([
    'lodash',
    'taoQtiItem/qtiCreator/model/mixin/editable',
    'taoQtiItem/qtiItem/core/feedbacks/ModalFeedback'
], function(_,editable,ModalFeedback){
    "use strict";
    var methods = {};
    _.extend(methods, editable);
    _.extend(methods, {
        getDefaultAttributes : function(){
            return {
                title : 'modal feedback title',
                showHide : 'show'
            };
        }
    });
    
    return ModalFeedback.extend(methods);
});