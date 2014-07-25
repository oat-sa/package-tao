define(['taoQtiItem/qtiItem/core/IdentifiedElement'], function(IdentifiedElement){

    /**
     * It is the top abstract class for all variable classes
     * (so not renderable and qtiClass undefined)
     */
    var VariableDeclaration = IdentifiedElement.extend({
        init : function(serial, attributes){
            this._super(serial, attributes);
            this.defaultValue = null;
        },
        is : function(qtiClass){
            return (qtiClass === 'variableDeclaration') || this._super(qtiClass);
        },
        toArray : function(){
            var arr = this._super();
            arr.defaultValue = this.defaultValue;
            return arr;
        }
    });

    return VariableDeclaration;
});