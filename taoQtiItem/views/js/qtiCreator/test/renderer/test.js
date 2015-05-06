define([
    'jquery',
    'taoQtiItem/qtiItem/core/Loader',
    'taoQtiItem/qtiItem/core/Element',
    'taoQtiItem/qtiCreator/model/qtiClasses',
    'taoQtiItem/qtiCreator/helper/creatorRenderer',
    'taoQtiItem/qtiCreator/helper/devTools',
    'json!taoQtiItem/qtiItem/../../../test/samples/json/ALL.json'
], function($, Loader, Element, qtiClasses, creatorRenderer, devTools, data){

    var Test = {
        testRender : function(itemIdentifier, attributes){

            if(data[itemIdentifier]){

                test('render', function(){

                    var loader = new Loader().setClassesLocation(qtiClasses);

                    stop();//wait for the next start()

                    loader.loadItemData(data[itemIdentifier].full, function(item){
                        
                        ok(Element.isA(item, 'assessmentItem'), itemIdentifier + ' item loaded');
                        
                        //count interaction number:
                        var interactions = item.getInteractions();
                        ok(interactions.length, 'has ' + interactions.length + ' interaction(s)');

                        //test only the last interaction:
                        var interaction = interactions.pop();
                        interaction.attr(attributes);//overwrite attributes for test purpose:

                        //append item placeholder and render it:
                        var $placeholder = $('<div>');
                        $('#item-editor-scroll-area').append($placeholder);

                        var $interactionForm = $('<div>', {'id' : 'qtiCreator-form-interaction', 'class': 'form-container', text : 'interaction form placeholder'});
                        var $choiceForm = $('<div>', {'id' : 'qtiCreator-form-choice', 'class': 'form-container', text : 'choice form placeholder'});
                        var $responseForm = $('<div>', {'id' : 'qtiCreator-form-response', 'class': 'form-container', text : 'response form placeholder'});
                        var $bodyElementForm = $('<div>', {'id' : 'qtiCreator-form-body-element', 'class': 'form-container', text : 'body element form placeholder'});
                        $('#form-container')
                            .append($interactionForm)
                            .append($choiceForm)
                            .append($responseForm)
                            .append($bodyElementForm);

                        creatorRenderer.setOptions({
                            baseUrl : '/taoQtiItem/test/samples/test_base_www/',
                            lang : 'en-US',
                            uri : '#test_item',
                            interactionOptionForm : $interactionForm,
                            choiceOptionForm : $choiceForm,
                            responseOptionForm : $responseForm,
                            bodyElementOptionForm : $bodyElementForm,
                            itemOptionForm : $(),
                            textOptionForm : $()
                        });

                        creatorRenderer.get().load(function(){

                            start();

                            item.setRenderer(this);
                            item.render({}, $placeholder);

                            //check item container:
                            ok(item.getContainer().length, 'rendered container found');

                            item.postRender({uri:'dummy#uri'});

                        }, this.getLoadedClasses());
                        
                        devTools.listenStateChange();
                        devTools.liveXmlPreview(item, $('#xml-container'));

                    });

                });

            }else{
                throw new Error('item sample not found : ' + itemIdentifier);
            }

        }
    };

    return Test;
});