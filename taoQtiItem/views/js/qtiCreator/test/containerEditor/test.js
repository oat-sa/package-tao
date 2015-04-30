require([
    'lodash',
    'jquery',
    'taoQtiItem/qtiCreator/editor/containerEditor',
    'taoQtiItem/qtiCreator/helper/creatorRenderer',
    'text!taoQtiItem/../../test/samples/xml/qtiv2p1/rubricBlock/extended_text_rubric.xml'
], function(_, $, containerEditor, creatorRenderer, sampleXML){

    function configureCreatorRenderer(){

        var $interactionForm = $('<div>', {'id' : 'qtiCreator-form-interaction', 'class' : 'form-container', text : 'interaction form placeholder'});
        var $choiceForm = $('<div>', {'id' : 'qtiCreator-form-choice', 'class' : 'form-container', text : 'choice form placeholder'});
        var $responseForm = $('<div>', {'id' : 'qtiCreator-form-response', 'class' : 'form-container', text : 'response form placeholder'});
        var $bodyElementForm = $('<div>', {'id' : 'qtiCreator-form-body-element', 'class' : 'form-container', text : 'body element form placeholder'});
        $('#form-container')
            .append($interactionForm)
            .append($choiceForm)
            .append($responseForm)
            .append($bodyElementForm);
        
        creatorRenderer.get().setOptions({
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
    }

    test('parse inline sample', function(){

        expect(0);

        var $rubricBlockXml = $(sampleXML).find('rubricBlock'),
            $container = $('#element').html($rubricBlockXml.html());

        configureCreatorRenderer();
        
        $container.on('containerchange', function(e, html){
            console.log('change', html);
        });
        
        $container.on('editorready', function(){
            _.defer(function(){
                return;
                containerEditor.destroy($container);
            });
        });
        
        containerEditor.create($container, {});
    });

});