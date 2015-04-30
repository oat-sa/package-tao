define(['jquery', 'lodash', 'taoQtiItem/qtiCreator/model/qtiClasses'], function($, _, qtiClasses){
    "use strict";
    var methods = {
        createElements : function(container, body, callback){

            var regex = /{{([a-z_]+)\.?([a-z_]*):new}}/ig;

            //first pass to get required qti classes, but do not replace
            var required = {};
            body.replace(regex,
                function(original, qtiClass, subClass){
                    if(qtiClasses[qtiClass]){
                        required[qtiClass] = qtiClasses[qtiClass];
                    }else{
                        throw 'missing required class : ' + qtiClass;
                    }
                });
                
            //second pass after requiring classes:
            require(_.values(required), function(){

                //register and name all loaded classes:
                var Qti = {};
                for(var i in arguments){
                    Qti[arguments[i].prototype.qtiClass] = arguments[i];
                }
                
//                debugger;
                
                //create new elements
                var newElts = {};
                var newBody = body.replace(regex,
                    function(original, qtiClass, subClass){
                        if(Qti[qtiClass]){
                            //create new element
                            var elt = new Qti[qtiClass]();
                            if(container.getRenderer()){
                                elt.setRenderer(container.getRenderer());
                            }
                            newElts[elt.getSerial()] = elt;

                            //manage sub-classed qtiClass
                            if(subClass){
                                //@todo generalize it from customInteraction
                                elt.typeIdentifier = subClass;
                            }

                            return elt.placeholder();
                        }else{
                            return original;
                        }
                    });

                //insert them:
                container.setElements(newElts, newBody);
                
                //operations after insertions:
                var $doc = $(document);
                _.each(newElts, function(elt){
                    if(_.isFunction(elt.buildIdentifier)){
                        elt.buildIdentifier();
                    }
                    if(_.isFunction(elt.afterCreate)){
                        elt.afterCreate();
                    }
                    $doc.trigger('elementCreated.qti-widget', {parent : container.parent(), element : elt});
                });

                if(typeof(callback) === 'function'){
                    callback.call(container, newElts);
                }
            });

        }
    };

    return methods;
});
