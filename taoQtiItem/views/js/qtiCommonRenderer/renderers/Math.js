define(['tpl!taoQtiItem/qtiCommonRenderer/tpl/math', 'taoQtiItem/qtiCommonRenderer/helpers/container', 'mathJax', 'lodash'], function(tpl, containerHelper, MathJax, _){
    return {
        qtiClass : 'math',
        template : tpl,
        getContainer : containerHelper.get,
        render : function(math, data){
            if(typeof(MathJax) !== 'undefined' && MathJax){
                _.delay(function(){//defer execution fix some rendering issue in chrome
                     MathJax.Hub.Queue(["Typeset", MathJax.Hub, containerHelper.get(math).parent()[0]]);
                },60);
            }
        }
    };
});
