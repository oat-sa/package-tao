//absolutely requires QUnit before execution

var Tester = {};
Tester.Interaction = Class.extend({
    init : function($itemContainer, interaction){
        this.$container = $itemContainer;
        this.interaction = interaction;
        this.type = interaction.qtiTag;
    },
    testChoices : function(){
        throw 'Tester.Interaction.testChoices() to be implemented';
    },
    testInteractivity : function(){
        throw 'Tester.Interaction.testInteractivity() to be implemented';
    },
    testResponse : function(){
        throw 'Tester.Interaction.testResponse() to be implemented';
    },
    countObjectElements : function(elts){
        var count = 0;
        for(var i in elts){
            if(elts.hasOwnProperty(i)){
                count++;
            }
        }
        return count;
    },
    countChoices : function(){
        var choices = this.interaction.getChoices();
        return this.countObjectElements(choices);
    },
    getLastChoice : function(){
        var ret = null;
        var choices = this.interaction.getChoices();
        for(var i in choices){
            ret = choices[i];
        }
        return ret;
    },
    getChoiceIds : function(){
        var list = [];
        for(var i in this.interaction.choices){
            list.push(this.interaction.choices[i].id());
        }
        return list;
    },
    testResponseSettingPairs : function(values){
        this.interaction.setResponse(values);
        var response = this.interaction.getResponse();
        for(var i in response.value){
            var pair = response.value[i];
            if(typeof pair === 'object' && this.countObjectElements(pair) === 2){
                ok((pair[0] === values[i][0]) && (pair[1] === values[i][1]), 'response pair ok');
            }else{
                //is a single pair:
                ok((response.value[0] === values[i][0]) && (response.value[1] === values[i][1]), 'response pair ok');
                break;
            }
        }
    },
    testResponseSettingSingle : function(value){
        this.interaction.setResponse(value);
        var response = this.interaction.getResponse();
        ok(response.value === value, 'response ok');
    },
    testResponseSettingList : function(values){
        this.interaction.setResponse(values);
        var response = this.interaction.getResponse();
        for(var i in response.value){
            ok(response.value[i] === values[i], 'response list elt ok');
        }
    }
});

Tester.ChoiceInteraction = Tester.Interaction.extend({
    testChoices : function(){
        ok(this.$container.find('ul').length, 'rendered item has choice list');

        var choicesCount = this.countChoices();
        ok(choicesCount, 'has ' + choicesCount + ' choice' + ((choicesCount > 1) ? 's' : ''));
    },
    testResponse : function(){
        var choice = this.getLastChoice();

        this.interaction.setResponse(choice.id());
        ok(this.$container.find('li#' + choice.id()).hasClass('tabActive'), 'set response success');
    }
});

Tester.InlineChoiceInteraction = Tester.Interaction.extend({
    testChoices : function(){
        var choicesCount = this.countChoices();
        ok(choicesCount, 'has ' + choicesCount + ' choice' + ((choicesCount > 1) ? 's' : ''));
    },
    testResponse : function(){
        var choice = this.getLastChoice();

        var value = choice.id();
        this.interaction.setResponse(value);
        ok(this.interaction.getResponse().value === value, 'response ok');
    }
});

Tester.OrderInteraction = Tester.Interaction.extend({
    testChoices : function(){
        var choicesCount = this.countChoices();
        ok(choicesCount, 'has ' + choicesCount + ' choice' + ((choicesCount > 1) ? 's' : ''));
    },
    testResponse : function(){
        this.testResponseSettingList(this.getChoiceIds());
    }
});

Tester.AssociateInteraction = Tester.Interaction.extend({
    testChoices : function(){
        var choicesCount = this.countChoices();
        ok(choicesCount, 'has ' + choicesCount + ' choice' + ((choicesCount > 1) ? 's' : ''));
    },
    testResponse : function(){
        var values = [];
        var i = 0;
        var pair = [];
        for(var serial in this.interaction.choices){
            pair.push(this.interaction.choices[serial].id());
            if(i % 2){
                values.push(pair);
                pair = [];
            }
            i++;
        }

        this.testResponseSettingPairs(values);
    }
});

Tester.TextEntryInteraction = Tester.Interaction.extend({
    testChoices : function(){
        ok(true, 'no choice in text entry interaction');
    },
    testResponse : function(){
        var value = 'my personal answer...';
        this.interaction.setResponse(value);
        var response = this.interaction.getResponse();
        ok(response.value === value, 'response ok');
    }
});

Tester.ExtendedTextInteraction = Tester.Interaction.extend({
    testChoices : function(){
        //no choice in text entry interaction
    },
    testResponse : function(){
        var tagname = $('#' + this.interaction.id()).prop('tagName').toLowerCase();
        var multiple = false;
        if(tagname === 'div'){//usual case: one textarea vs special case, multiple text inputs
            multiple = true;
        }

        if(multiple){
            var values = [];
            var maxStrings = parseInt(this.interaction.attr('maxStrings'));
            for(var i = 0; i < maxStrings; i++){
                values.push('answer ' + i);
            }
            this.interaction.setResponse(values);
            var response = this.interaction.getResponse();
            for(var i in response.value){
                ok(values[i] === response.value[i], 'response ok');
            }
        }else{
            var value = 'my personal answer...';
            this.testResponseSettingSingle(value);
        }
    }
});

Tester.MatchInteraction = Tester.Interaction.extend({
    testChoices : function(){
        //no choice in text entry interaction
        for(var i = 0; i < 2; i++){
            var choicesCount = this.countObjectElements(this.interaction.getChoices(i));
            ok(choicesCount, 'the match set #' + i + ' has ' + choicesCount + ' choice' + ((choicesCount > 1) ? 's' : ''));
        }
    },
    testResponse : function(){
        var firstPair = [];
        for(var i = 0; i < 2; i++){
            var set = this.interaction.getChoices(i);
            for(var serial in set){
                firstPair.push(set[serial].id());
                break;
            }
        }

        var values = [];
        values.push(firstPair);

        this.testResponseSettingPairs(values);
    }
});

Tester.GapMatchInteraction = Tester.Interaction.extend({
    testChoices : function(){
        var choicesCount = this.countChoices();
        ok(choicesCount, 'has ' + choicesCount + ' choice' + ((choicesCount > 1) ? 's' : ''));

        var gaps = this.interaction.getBody().getElements('Gap');//Qti.Gap is a subclass of Qti.Choice
        var gapsCount = this.countObjectElements(gaps);
        ok(gapsCount, 'has ' + gapsCount + ' gap' + ((gapsCount > 1) ? 's' : ''));
    },
    testResponse : function(){
        var values = [];
        var choice = this.getLastChoice();
        var gaps = this.interaction.getBody().getElements('Gap');
        for(var i in gaps){
            //values format = array(['gap', 'choice'])
            values.push([gaps[i].id(), choice.id()]);
            break;
        }

        this.testResponseSettingPairs(values);
    }
});

Tester.HottextInteraction = Tester.Interaction.extend({
    testChoices : function(){
        var choicesCount = this.countChoices();
        ok(choicesCount, 'has ' + choicesCount + ' choice' + ((choicesCount > 1) ? 's' : ''));
    },
    testResponse : function(){
        var choice = this.getLastChoice();
        this.testResponseSettingSingle(choice.id());
    }
});

Tester.SelectPointInteraction = Tester.Interaction.extend({
    testChoices : function(){
        ok(true, 'no choice to be tested');
    },
    testResponse : function(){
        var values = [
            [80, 100],
            [100, 200]
        ];
        this.testResponseSettingPairs(values);
    }
});

Tester.HotspotInteraction = Tester.Interaction.extend({
    testChoices : function(){
        var choicesCount = this.countChoices();
        ok(choicesCount, 'has ' + choicesCount + ' choice' + ((choicesCount > 1) ? 's' : ''));
    },
    testResponse : function(){
        this.testResponseSettingSingle(this.getLastChoice().id());
    }
});


Tester.GraphicOrderInteraction = Tester.OrderInteraction.extend({});

Tester.GraphicAssociateInteraction = Tester.AssociateInteraction.extend({});

Tester.GraphicGapMatchInteraction = Tester.Interaction.extend({
    testChoices : function(){
        var hotspotCount = this.countChoices();
        ok(hotspotCount, 'has ' + hotspotCount + ' choice' + ((hotspotCount > 1) ? 's' : ''));

        var gapImgs = this.interaction.getGapImgs();
        var gapImgsCount = this.countObjectElements(gapImgs);
        ok(gapImgsCount, 'has ' + gapImgsCount + ' gap' + ((gapImgsCount > 1) ? 's' : ''));
    },
    testResponse : function(){
        var values = [];
        var hotspot = this.getLastChoice();
        var gapImgs = this.interaction.getGapImgs();
        for(var i in gapImgs){
            //values format = array(['gap', 'choice'])
            values.push([hotspot.id(), gapImgs[i].id()]);
            break;
        }
        this.testResponseSettingPairs(values);
    }
});

Tester.SliderInteraction = Tester.Interaction.extend({
    testChoices : function(){
        ok(true, 'no choice in slider interaction');
    },
    testResponse : function(){
        this.testResponseSettingSingle(60);
    }
});

Tester.MediaInteraction = Tester.Interaction.extend({
    testChoices : function(){
        ok(true, 'no choice in slider interaction');
    },
    testResponse : function(){
        this.testResponseSettingSingle(0);
    }
});