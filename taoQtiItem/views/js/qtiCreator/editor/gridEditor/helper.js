define(function(){
    "use strict";
    var helpers = {
        getColUnits : function getColUnits($elt){

            var cssClasses = $elt.attr('class');
            if(!cssClasses){
                throw new Error('the element has no css class');
            }

            var colMatch = cssClasses.match(/col-([\d]+)/);
            if(colMatch && colMatch.length){
                return parseInt(colMatch.pop());
            }else{
                throw 'the element has no col-* class';
            }
        },
        setUnitsFromClass : function setUnitsFromClass($el){
            var units = helpers.getColUnits($el);
            $el.attr('data-units', units);
            return units;
        }
    };

    return helpers;
});
