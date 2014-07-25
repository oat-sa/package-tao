test("Load Item", function(){

    ok(true, 'getting started');
    return;

    for(var id in allItems){
        if(id.indexOf('adaptive') > 0){
            //adaptive items are not supported yet
            continue;
        }

        ok(true, '********** Parsing Item ' + id + ' **********');
        var itemLoader = new ItemLoader();
        var item = itemLoader.load(allItems[id]);
        ok(item instanceof Qti.Item, id + ' is a qti item');
        ok(item instanceof Qti.Element, id + ' is a qti elment');
        ok(item.hasTrait('container'), id + ' implement container trait');
        ok(item.attr('identifier') === id, id + ' has correct id');
        ok(item.getInteractions().length, id + ' has ' + item.getInteractions().length + ' interaction');
//        CL(allItems[id], item.toArray());
//        break;
    }

    //clear list
    Qti.Element.instances = [];

});

var itemsToBeTested = [
//    'associate',
//    'choice',
//    'choice_multiple',
//    'composite',
//    'extendedText',
//    'extendedTextMultiple',
//    'gapMatch',
//    'gapMatch2',
//    'graphicAssociate',
//    'graphicGapfill',
//    'graphicOrder',
//    'hotspot',
//    'hottext',
//    'inlineChoice',
    'match',
//    'math',
//    'multi-input',
//    'order',
//    'selectPoint',
//    'textEntry',
//    'slider',
//    'mediaTest',
//    'mediaInteraction',
//    'item8math'//math + feedback modal
//    'item9feedback',
//    'media-prompt'
];

for(var id in allItems){

    if($.inArray(id, itemsToBeTested) < 0){
        //adaptive items are not supported yet
        continue;
    }
    var itemData = allItems[id];

    asyncTest("Render Item " + id, function(){

        var itemLoader = new ItemLoader();
        var item = itemLoader.load(itemData);
        CL(itemData, item);

        var renderingEngine = new Qti.DefaultRenderer();
        item.setRenderer(renderingEngine);
        ok(item.getRenderer() instanceof Qti.Renderer, 'item has renderer');

        var elts = item.getComposingElements();
        for(var serial in elts){
            ok(elts[serial].getRenderer() instanceof Qti.Renderer, serial + ' has renderer');
        }

        var divId = 'qtiItem-' + id;
        $("#qunit-fixture").after('<div id="' + divId + '"></div>');
        //$("#qunit-fixture").append('<div id="qtiItem"></div>');
        item.render({}, $('#' + divId));

        ok($('#' + item.attr('identifier')).length, 'rendered container found');
        var $container = $('#' + item.attr('identifier'));


        item.postRender();

        setTimeout(function(){
            start();
            var interactions = item.getInteractions();
            for(var serial in interactions){
                testInteraction($container, interactions[serial]);
            }
        }, 200);//quick hack to ensure that the item has time to be post rendered before actual testing


        //test modal feedbacks:
        $container.append('<div id="modalFeedbacks"></div>');
        for(var serial in item.modalFeedbacks){
            $('#modalFeedbacks').append('<div id="' + serial + '"></div>');
            var feedback = item.modalFeedbacks[serial];
            feedback.render({}, $('#' + serial));
            feedback.postRender({
                callback : function(){
                    CL('closed : ' + serial);
                }
            });
        }

        //clear list
        Qti.Element.instances = [];

    });

}

function testInteraction($container, interaction){

    ok(interaction instanceof Qti.Interaction, 'testing interaction ' + interaction.qtiTag);
    var type = interaction.qtiTag.charAt(0).toUpperCase() + interaction.qtiTag.slice(1);
    if(Tester && Tester[type]){
        var tester = new Tester[type]($container, interaction);
        tester.testChoices();
        tester.testResponse();
    }

}