define(['lodash', 'taoQtiItem/qtiCreator/model/qtiClasses'], function(_, qtiClasses){

    var methods = {
        createElements : function(container, body, callback){
            
            var regex = /{{([a-z0-9_]*):new}}/ig;

            //first pass to get required qti classes, but do not replace
            var required = {};
            body.replace(regex,
                function(original, qtiClass){
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

                //create new elements
                var newElts = {};
                var newBody = body.replace(regex,
                    function(original, qtiClass){
                        if(Qti[qtiClass]){
                            //create new element
                            var elt = new Qti[qtiClass]();
                            if(container.getRenderer()){
                                elt.setRenderer(container.getRenderer());
                            }
                            newElts[elt.getSerial()] = elt;
                            return elt.placeholder();
                        }else{
                            return original;
                        }
                    });

                //insert them:    
                container.setElements(newElts, newBody);

                //operations after insertions:
                for(var i in newElts){
                    var elt = newElts[i];
                    if(typeof(elt.buildIdentifier) === 'function'){
                        elt.buildIdentifier();
                    }
                    if(typeof(elt.afterCreate) === 'function'){
                        elt.afterCreate();
                    }
                    $(document).trigger('elementCreated.qti-widget', {'parent' : container.parent(), 'element' : elt});
                }

                if(typeof(callback) === 'function'){
                    callback.call(container, newElts);
                }
            });

        }
    };

    return methods;
});