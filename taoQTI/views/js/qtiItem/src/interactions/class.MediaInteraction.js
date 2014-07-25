Qti.MediaInteraction = Qti.ObjectInteraction.extend({
    qtiTag : 'mediaInteraction',
    render : function(data, $container){

        var defaultData = {
            'media' : this.object.render()
        };
        var tplData = $.extend(true, defaultData, data || {});

        return this._super(tplData, $container);

    }
});

