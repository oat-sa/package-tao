Qti.ModalFeedback = Qti.Element.extend({
    'qtiTag' : 'modalFeedback',
    postRender : function(data){
        var renderer = this.getRenderer();
        if(renderer){
            renderer.postRender(this.qtiTag, this, data);
        }else{
            throw 'no renderer found';
        }
    }
}, {
    'container' : Qti.traits.container
});