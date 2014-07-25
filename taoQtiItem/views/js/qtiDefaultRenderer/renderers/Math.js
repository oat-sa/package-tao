define(['tpl!taoQtiItem/qtiDefaultRenderer/tpl/math', 'mathJax'], function(tpl, MathJax){
    return {
        qtiClass : 'math',
        template : tpl,
        render : function(math, data){
            var $mathElt = $('#' + math.serial);
            if(typeof(MathJax) !== 'undefined' && MathJax){
                MathJax.Hub.Queue(["Typeset", MathJax.Hub, $mathElt.parent()[0]]);
            }
        }
    };
});