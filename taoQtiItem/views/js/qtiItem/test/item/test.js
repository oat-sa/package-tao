define([
    'lodash',
    'taoQtiItem/qtiItem/core/Element',
    'taoQtiItem/qtiItem/core/Container',
    'taoQtiItem/qtiItem/core/Item',
    'taoQtiItem/qtiItem/core/interactions/ChoiceInteraction',
    'taoQtiItem/qtiItem/core/choices/SimpleChoice'
], function(_, Element, Container, Item, Interaction, Choice){

    var CL = console.log;

    test('instanciate', function(){
        var item1 = new Item();
        ok(item1 instanceof Item, 'is item (checked by instanceof)');
        ok(Element.isA(item1, 'assessmentItem'), 'is item (checked by qti tag)');
        ok(item1.getBody() instanceof Container, 'has body');

        var body = '<div>hello</div>'
        item1.body(body);
        ok(item1.body() === body, 'set/get body');
    });

    test('find', function(){

        var interaction1 = new Interaction().attr({'identifier' : 'interaction_1', 'maxChoices' : 1}),
            choice1 = new Choice().id('choice_1').body('answer #1'),
            choice2 = new Choice().id('choice_2').body('answer #2'),
            choice3 = new Choice().id('choice_3').body('answer #3'),
            choice4 = new Choice().id('choice_4').body('answer #4');

        interaction1.addChoice(choice1).addChoice(choice2).addChoice(choice3);

        var newBody = '<div>' + interaction1.placeholder() + '</div>';
        var item1 = new Item().setElement(interaction1, newBody);

        var found = item1.find(interaction1.serial);
        equal(_.size(found), 3, 'found interaction');
        equal(found.parent.serial, item1.serial, 'found parent ok');
        equal(found.element.serial, interaction1.serial, 'found elment ok');
        equal(found.location, 'body', 'found elment ok');

        found = item1.find(choice2.serial);
        equal(_.size(found), 2, 'found choice2');
        equal(found.parent.serial, interaction1.serial, 'found parent ok');
        equal(found.element.serial, choice2.serial, 'found elment ok');

        found = item1.find(choice4.serial);
        ok(_.isNull(found), 'not found choice4');
    });

    test('destroy', function(){

        var interaction1 = new Interaction().attr({'identifier' : 'interaction_1', 'maxChoices' : 1}),
            choice1 = new Choice().id('choice_1').body('answer #1'),
            choice2 = new Choice().id('choice_2').body('answer #2'),
            choice3 = new Choice().id('choice_3').body('answer #3');

        interaction1.addChoice(choice1).addChoice(choice2).addChoice(choice3);

        var newBody = '<div>' + interaction1.placeholder() + '</div>',
            item1 = new Item().setElement(interaction1, newBody);

        //ensure that we cannot instanciate another element with existing serial:
        throws(function(){

            new Item(item1.serial);

        }, 'a QTI Element with the same serial already exists ' + item1.serial, 'catched expected');

        //ensure that we cannot instanciate another element with existing serial:
        throws(function(){

            new Choice(choice3.serial);

        }, 'a QTI Element with the same serial already exists ' + choice3.serial, 'catched expected');

        //unset element
        item1.unset();

        var item1b = new Item(item1.serial),
            choice3b = new Choice(choice3.serial);

        ok(item1b.is('assessmentItem'), 'item recreated after unset');
        ok(choice3b.is('choice'), 'choice recreated after unset');

    });
});


