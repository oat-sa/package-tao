define(['tpl!taoQtiItem/qtiCommonRenderer/tpl/math', 'taoQtiItem/qtiCommonRenderer/helpers/Helper', 'mathJax', 'lodash'], function(tpl, Helper, MathJax, _){
    return {
        qtiClass : 'math',
        template : tpl,
        getContainer : Helper.getContainer,
        render : function(math, data){
            if(typeof(MathJax) !== 'undefined' && MathJax){
                _.delay(function(){//defer execution fix some rendering issue in chrome
                     MathJax.Hub.Queue(["Typeset", MathJax.Hub, Helper.getContainer(math).parent()[0]]);
                },60);
            }
        }
    };
});