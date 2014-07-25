Qti.GraphicGapMatchInteraction = Qti.GraphicInteraction.extend({
    qtiTag : 'graphicGapMatchInteraction',
    gapImgs : {},
    addGapImg : function(gapImg){
        if(gapImg instanceof Qti.GapImg){
            gapImg.setRelatedItem(this.getRelatedItem() || null);
            this.gapImgs[gapImg.getSerial()] = gapImg;
        }
    },
    getGapImgs : function(){
        var gapImgs = {};
        for(var id in this.gapImgs){
            gapImgs[id] = this.gapImgs[id];
        }
        return gapImgs;
    },
    getComposingElements : function(){
        var elts = this._super();
        //recursive to choices:
        for(var serial in this.gapImgs){
            elts[serial] = this.gapImgs[serial];
            elts = $.extend(elts, this.gapImgs[serial].getComposingElements());
        }
        return elts;
    },
    render : function(data, $container){
        var defaultData = {
            'gapImgs' : []
        };
        
        //note: no choice shuffling option available for graphic gap match
        var gapImgs = this.getGapImgs();
        for(var serial in gapImgs){
            if(gapImgs[serial] instanceof Qti.Choice){
                defaultData.gapImgs.push(gapImgs[serial].render());
            }
        }

        var tplData = $.extend(true, defaultData, data || {});

        return this._super(tplData, $container);
    },
    toArray : function(){
        var arr = this._super();
        arr.gapImgs = {};
        var gapImgs = this.getGapImgs();
        for(var serial in gapImgs){
            arr.gapImgs[serial] = gapImgs[serial].toArray();
        }
        return arr;
    }
});

