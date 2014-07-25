require([
    'jquery',
    'taoQtiItem/qtiItem/core/Element',
    'taoQtiItem/qtiItem/core/Item',
    'taoQtiItem/qtiItem/core/interactions/OrderInteraction',
    'taoQtiItem/qtiItem/core/choices/SimpleChoice',
    'taoQtiItem/qtiCommonRenderer/renderers/Renderer'
],
    function($, Element, Item, OrderInteraction, SimpleChoice, Renderer){

        var CL = console.log;

        asyncTest('render partial', function(){

            var item1 = new Item().body('hello').id('partial-rendering');

            var $placeholder = $('<div>', {'id' : 'qtiItem-' + item1.attr('identifier')});
            var $form = $('<div>', {'id' : 'qtiCreator-form', text : 'form place holder'});
            var $title = $('<h2>', {'text' : 'identifier : ' + item1.attr('identifier')});
            $("#qunit-fixture").after($form).after($placeholder.before($title));
            ok(Element.isA(item1, 'assessmentItem'));

            var interaction1 = new OrderInteraction().attr({'identifier' : 'interaction_1', 'maxChoices' : 1});
            interaction1.prompt.body('Answer this!');
            var choice1 = new SimpleChoice().id('choice_1').body('answer #1');
            var choice2 = new SimpleChoice().id('choice_2').body('answer #2');
            var choice3 = new SimpleChoice().id('choice_3').body('answer #3');
            interaction1.addChoice(choice1).addChoice(choice2).addChoice(choice3);

            var newBody = item1.body() + interaction1.placeholder();
            item1.setElement(interaction1, newBody);

            //render it:
            var renderer = new Renderer({
                baseUrl:'/taoQtiItem/test/samples/test_base_www/'
            });

            renderer.load(function(){
                
                start();
                
                item1.setRenderer(renderer);
                item1.render({}, $placeholder);
                item1.postRender();

                CL(item1);

                $(document).on('reloadRequest', function(e, data){
                    data.element.setRenderer(renderer);
                    data.element.render({}, data.$container);
                    data.element.postRender();
                });
                
            });


        });
    });