define(['jquery'], function($){
    
    return {
        contains : function(elt){
            if(elt instanceof $){
                elt = elt[0];
            }
            return $.contains(document, elt);
        }
    }
    
});