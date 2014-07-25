Qti.ResponseDeclaration = Qti.Element.extend({
    qtiTag:'responseDeclaration',
    howMatch : null,
    correctResponses : {},
    mapping : {},
    mappingAttributes : {},
    toArray : function(){
        var arr = this._super();
        arr.howMatch = this.howMatch;
        arr.correctResponses = this.correctResponses;
        arr.mapping = this.mapping;
        arr.mappingAttributes = this.mappingAttributes;
        return arr;
    }
});

