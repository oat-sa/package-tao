define(['taoQtiItem/qtiItem/core/interactions/GraphicInteraction', 'taoQtiItem/qtiItem/core/Element', 'lodash', 'taoQtiItem/qtiItem/helper/rendererConfig'], function(GraphicInteraction, Element, _, rendererConfig){

    var GraphicGapMatchInteraction = GraphicInteraction.extend({
        qtiClass : 'graphicGapMatchInteraction',
        init : function(serial, attributes){
            this._super(serial, attributes);
            this.gapImgs = {};
        },
        addGapImg : function(gapImg){
            if(Element.isA(gapImg, 'gapImg')){
                gapImg.setRelatedItem(this.getRelatedItem() || null);
                this.gapImgs[gapImg.getSerial()] = gapImg;
            }
        },
        removeGapImg : function(gapImg){
            var serial = '';
            if(typeof(gapImg) === 'string'){
                serial = gapImg;
            }else if(Element.isA(gapImg, 'gapImg')){
                serial = gapImg.getSerial();
            }
            delete this.gapImgs[serial];
            return this;
        },
        getGapImgs : function(){
            return _.clone(this.gapImgs);
        },
        getGapImg : function(serial){
            return this.gapImgs[serial];
        },
        getComposingElements : function(){
            var elts = this._super();
            //recursive to choices:
            for(var serial in this.gapImgs){
                elts[serial] = this.gapImgs[serial];
                elts = _.extend(elts, this.gapImgs[serial].getComposingElements());
            }
            return elts;
        },
        find : function(serial){
            var found = this._super(serial);
            if(!found){
                if(this.gapImgs[serial]){
                    found = {'parent' : this, 'element' : this.gapImgs[serial]};
                }
            }
            return found;
        },
        render : function(){
            
            var args = rendererConfig.getOptionsFromArguments(arguments),
                renderer = args.renderer || this.getRenderer(),
                defaultData = {
                    'gapImgs' : []
                };

            //note: no choice shuffling option available for graphic gap match
            var gapImgs = this.getGapImgs();
            for(var serial in gapImgs){
                if(Element.isA(gapImgs[serial], 'choice')){
                    defaultData.gapImgs.push(gapImgs[serial].render({}, null, '', renderer));
                }
            }

            return this._super(_.merge(defaultData, args.data), args.placeholder, args.subclass, renderer);
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

    return GraphicGapMatchInteraction;
});
