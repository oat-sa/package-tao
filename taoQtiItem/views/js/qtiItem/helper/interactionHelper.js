/**
 * Common helper functions
 */
define(['lodash', 'taoQtiItem/qtiItem/core/Element'], function(_, Element){
    return {
        convertChoices : function(choices, outputType){

            var ret = [], _this = this;

            _.each(choices, function(c){
                if(Element.isA(c, 'choice')){
                    switch(outputType){
                        case 'serial':
                            ret.push(c.getSerial());
                            break;
                        case 'identifier':
                            ret.push(c.id());
                            break;
                        default:
                            ret.push(c);
                    }

                }else if(_.isArray(c)){
                    ret.push(_this.convertChoices(c, outputType));
                }
            });

            return ret;
        },
        findChoices : function(interaction, choices, inputType){

            var ret = [], _this = this;

            _.each(choices, function(c){
                var choice;
                if(_.isString(c)){
                    if(inputType === 'serial'){
                        choice = interaction.getChoice(c);
                        if(choice){
                            ret.push(choice);
                        }
                    }else if(inputType === 'identifier'){
                        choice = interaction.getChoiceByIdentifier(c);
                        if(choice){
                            ret.push(choice);
                        }
                    }else{
                        ret.push(c);
                    }
                }else if(_.isArray(c)){
                    ret.push(_this.findChoices(interaction, c, inputType));
                }else{
                    ret.push(c);
                }
            });

            return ret;
        },
        shuffleChoices : function(choices){
            var r = [], //returned array
                f = {}, //fixed choices array
                j = 0;

            for(var i in choices){
                if(Element.isA(choices[i], 'choice')){
                    var choice = choices[i];
                    if(choice.attr('fixed')){
                        f[j] = choice;
                    }
                    r.push(choice);
                    j++;
                }else{
                    throw 'invalid element in array: is not a qti choice';
                }
            }

            for(var n = 0; n < r.length - 1; n++){
                if(f[n]){
                    continue;
                }
                var k = -1;
                do{
                    k = n + Math.floor(Math.random() * (r.length - n));
                }while(f[k]);
                var tmp = r[k];
                r[k] = r[n];
                r[n] = tmp;
            }

            return r;
        },
        serialToIdentifier : function(interaction, choiceSerial){
            var choice = interaction.getChoice(choiceSerial);
            if(choice){
                return choice.id();
            }else{
                return '';
            }
        }
    }
});