Qti.MatchInteraction = Qti.BlockInteraction.extend({
    qtiTag : 'matchInteraction',
    init : function(serial, attributes){
        this._super(serial, attributes);
        this.choices = [[], []];
    },
    addChoice : function(choice, matchSet){
        matchSet = parseInt(matchSet);
        if(this.choices[matchSet]){
            choice.setRelatedItem(this.getRelatedItem() || null);
            this.choices[matchSet][choice.getSerial()] = choice;
        }
    },
    getChoices : function(matchSet){
        matchSet = parseInt(matchSet);
        if(this.choices[matchSet]){
            return this.choices[matchSet];
        }else{
            return this.choices;
        }
    },
    getComposingElements : function(){

        var elts = this._super();
        //recursive to both match sets:
        for(var i = 0; i < 2; i++){
            var matchSet = this.getChoices(i);
            for(var serial in matchSet){
                if(matchSet[serial] instanceof Qti.Choice){
                    elts[serial] = matchSet[serial];
                    elts = $.extend(elts, matchSet[serial].getComposingElements());
                }
            }
        }

        return elts;
    },
    render : function(data, $container){

        var defaultData = {
            'matchSet1' : [],
            'matchSet2' : []
        };

        for(var i = 0; i < 2; i++){
            //shuffle here:
            var matchSet = (this.attr('shuffle')) ? this.shuffleChoices(this.getChoices(i)) : this.getChoices(i);
            for(var serial in matchSet){
                if(matchSet[serial] instanceof Qti.Choice){
                    defaultData['matchSet' + (i + 1)].push(matchSet[serial].render());
                }
            }
        }

        var tplData = $.extend(true, defaultData, data || {});

        return this._super(tplData, $container);
    },
    toArray : function(){
        var arr = this._super();
        arr.choices = {0 : {}, 1 : {}};
        for(var i = 0; i < 2; i++){
            var matchSet = this.getChoices(i);
            for(var serial in matchSet){
                if(matchSet[serial] instanceof Qti.Choice){
                    arr.choices[i][serial] = matchSet[serial].toArray();
                }
            }
        }
        return arr;
    }
});


