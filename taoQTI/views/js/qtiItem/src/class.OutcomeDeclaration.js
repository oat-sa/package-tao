Qti.OutcomeDeclaration = Qti.Element.extend({
    qtiTag:'outcomeDeclaration',
    defaultValue : null,
    toArray : function(){
        var arr = this._super();
        arr.defaultValue = this.defaultValue;
        return arr;
    }
});

