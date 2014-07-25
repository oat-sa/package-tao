Qti.Object = Qti.Element.extend({
    qtiTag : 'object',
    getMediaType : function(){
        var type = 'undefined';
        var mimetype = this.attr('type');
        if(mimetype !== ''){
            if(mimetype.indexOf('video') === 0){
                type = 'video';
            }else if(mimetype.indexOf('audio') === 0){
                type = 'audio';
            }else if(mimetype.indexOf('image') === 0){
                type = 'image';
            }else{
                type = 'object';
            }
        }
        return type;
    },
    render : function(data, $container){
    
        var defaultData = {};

        switch(this.getMediaType()){
            case 'video':
                defaultData.video = true;
                break;
            case 'audio':
                defaultData.audio = true;
                break;
            case 'image':
            case 'default':
                defaultData.object = true;
        }

        return this._super($.extend(true, defaultData, data || {}), $container);
    },
    postRender : function(data){
        var renderer = this.getRenderer();
        if(renderer){
            renderer.postRender(this.qtiTag, this, data);
        }
    }
});