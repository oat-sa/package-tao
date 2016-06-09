require([
    'jquery',
    'lodash',
    'taoQtiItem/qtiItem/core/Element',
    'taoQtiItem/qtiCreator/model/Item',
    'taoQtiItem/qtiCreator/model/helper/container',
    'taoQtiItem/qtiXmlRenderer/renderers/Renderer'
],
    function($, _, Element, Item, containerHelper, XmlRenderer){

        var CL = console.log;

        asyncTest('create interaction elements', function(){

            $(document).on('.qti-widget', function(e){
                CL('triggered event');
            });

            var item1 = new Item().body('hello').id('sample-item');
            ok(Element.isA(item1, 'assessmentItem'), 'item created');

            item1.createResponseProcessing();
            ok(Element.isA(item1.responseProcessing, 'responseProcessing'));

            item1.createElements('<h1>My QTI Item</h1><div>{{choiceInteraction:new}}</div></div>{{choiceInteraction:new}}</div>', function(newElts){

                start();

                //check created interactions:
                var interactions = this.getInteractions();
                equal(_.size(interactions), 2, '2 interactions created');
                equal(_.size(newElts), 2, '2 interactions created');

                //check auto generated responses:
                var responseA = this.getResponseDeclaration('RESPONSE');
                var responseB = this.getResponseDeclaration('RESPONSE_1');
                ok(Element.isA(responseA, 'responseDeclaration'), 'response A correctly auto generated');
                ok(Element.isA(responseB, 'responseDeclaration'), 'response B correctly auto generated');
                ok(Element.isA(responseB, 'variableDeclaration'), 'response B top class ok');

                //check response set/get():
                equal(responseA.getTemplate(), 'MATCH_CORRECT', 'template set');

                var rule = responseA.createFeedbackRule();
                var feedbackOutcome = rule.feedbackOutcome;
                ok(Element.isA(feedbackOutcome, 'outcomeDeclaration'), 'feedback outcome correctly auto generated');
                ok(Element.isA(feedbackOutcome, 'variableDeclaration'), 'feedback outcome B top class ok');
                equal(rule.condition, 'correct', 'condition correctly set');

                ok(Element.isA(rule.feedbackThen, 'modalFeedback'), 'feedback "then" correctly created');

                responseA.createFeedbackElse(rule);
                ok(Element.isA(rule.feedbackElse, 'modalFeedback'), 'feedback "else" correctly created');

                responseA.deleteFeedbackElse(rule);
                ok(rule.feedbackElse === null, 'feedback "else" deleted');

                responseA.setCondition(rule, 'gte', 2.5);
                equal(rule.condition, 'gte', 'condition correctly set');
                equal(rule.comparedValue, 2.5, 'condition correctly set');

                var renderer = new XmlRenderer();
                renderer.load(function(){
                    item1.setRenderer(this);
                    console.log(item1.render(), item1);
                }, item1.getUsedClasses());
            });

        });

        test('nested containers', function(){

            var item1 = new Item().body('hello').id('sample-item');
            ok(Element.isA(item1, 'assessmentItem'), 'item created');

            item1.createResponseProcessing();
            ok(Element.isA(item1.responseProcessing, 'responseProcessing'));

            stop();
            item1.createElements('<div>{{_container:new}}</div></div>{{_container:new}}</div></div>{{choiceInteraction:new}}</div>{{rubricBlock:new}}', function(newElts){

                start();
                console.log(this.getBody());

                var containers = this.getBody().getElements('_container');
                equal(_.size(containers), 2, 'containers created');

                var i = 0;
                _.each(containers, function(container){
                    if(i === 0){
                        
                        stop();
                        containerHelper.createElements(container, '<span>At vero eos et {{img:new}}accusamus</span>', function(newElts){
                            
                            start();
                            equal(_.size(newElts), 1, 'img created in subcontainer 1');
                            _.each(newElts, function(img){
                                img.attr('src', 'some/directory/picture.png');
                            });
                        });

                        i++;
                    }else{
                        
                        stop();
                        containerHelper.createElements(container, '<span>Et harum quidem {{inlineChoiceInteraction:new}} rerum facilis</span>', function(newElts){
                            
                            start();
                            equal(_.size(newElts), 1, 'inline choice interaction created in subcontainer 2');

                            //render it:
                            var renderer = new XmlRenderer();
                            renderer.load(function(){
                                item1.setRenderer(this);
//                                console.log(item1.render());
                            }, item1.getUsedClasses());
                        });
                    }
                });

            });
        });
    });