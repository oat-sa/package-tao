Qti.Math = Qti.Element.extend({
    qtiTag : 'math',
    render : function(data, $container){

        var defaultData = {
            block : (this.attr('display') === 'block') ? true : false,
            body : this.mathML
        };

        return this._super($.extend(true, defaultData, data || {}), $container);
    },
    postRender : function(data){
        var renderer = this.getRenderer();
        if(renderer){
            renderer.postRender(this.qtiTag, this, data);
        }
    }
});