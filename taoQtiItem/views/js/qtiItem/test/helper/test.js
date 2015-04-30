define([
    'lodash',
    'taoQtiItem/qtiItem/helper/interactionHelper',
    'taoQtiItem/qtiItem/core/interactions/ChoiceInteraction',
    'taoQtiItem/qtiItem/core/choices/SimpleChoice'
], function(_, interactionHelper, Interaction, Choice){

    test('convertChoices/findChoices', function(){

        var interaction1 = new Interaction().attr({'identifier' : 'interaction_1', 'maxChoices' : 1}),
        choice1 = new Choice().id('choice_1').body('answer #1'),
            choice2 = new Choice().id('choice_2').body('answer #2'),
            choice3 = new Choice().id('choice_3').body('answer #3'),
            choice4 = new Choice().id('choice_4').body('answer #3'),
            choices = [];

        choices.push(choice1);
        choices.push(choice2);
        choices.push(choice3);
        choices.push(choice4);

        _.each(choices, function(c){
            interaction1.addChoice(c);
        });

        var output1 = interactionHelper.convertChoices(choices, 'serial');
        for(var i in output1){
            equal(output1[i], choices[i].getSerial());
        }

        var output2 = interactionHelper.convertChoices(choices, 'identifier');
        for(var i in output2){
            equal(output2[i], choices[i].id());
        }

        var output3 = interactionHelper.findChoices(interaction1, output1, 'serial');
        for(var i in output3){
            equal(output3[i].getSerial(), choices[i].getSerial());
        }

        var output4 = interactionHelper.findChoices(interaction1, output2, 'identifier');
        for(var i in output4){
            equal(output4[i].id(), choices[i].id());
        }

        var recursiveChoices = [choices, choices];
        var output5 = interactionHelper.convertChoices(recursiveChoices, 'serial');
        equal(output5.length, 2);
        for(var j = 0; j < 2; j++){
            for(var i in output5[j]){
                equal(output5[j][i], choices[i].getSerial());
            }
        }
        
        var output6 = interactionHelper.findChoices(interaction1, output5, 'serial');
        equal(output6.length, 2);
        for(var j = 0; j < 2; j++){
            for(var i in output6[j]){
                equal(output6[j][i].getSerial(), choices[i].getSerial());
            }
        }


    });
});


