define(['mathJax'], function(MathJax){
    
    function render($container){
        
        $container.find('math').each(function(){
            
            var $math = $(this);
            $math.wrap($('<span>', {'class':'math-renderer'}));
            var $wrap = $math.parent('.math-renderer');
            MathJax.Hub.Queue(["Typeset", MathJax.Hub, $wrap[0]]);
        });
    }
    
    return {
        render : render
    };
});