define(['IMSGlobal/jquery_2_1_1', 'mathJax'], function($, MathJax){

    return {
        render : function render($container){

            $container.find('math').each(function(){

                var $math = $(this);
                $math.wrap($('<span>', {'class' : 'math-renderer'}));
                var $wrap = $math.parent('.math-renderer');
                MathJax.Hub.Queue(["Typeset", MathJax.Hub, $wrap[0]]);
            });
        }
    };
});