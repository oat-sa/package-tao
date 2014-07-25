Qti.GraphicInteraction = Qti.ObjectInteraction.extend({
    render : function(data, $container){

        var defaultData = {
            'backgroundImage' : this.object.getAttributes()
        };
        var tplData = $.extend(true, defaultData, data || {});

        return this._super(tplData, $container);

    }
});

