require([
    'jquery',
    'lodash',
    'taoQtiItem/qtiCreator/model/qtiClasses',
    'taoQtiItem/qtiCreator/renderers/Renderer',
    'taoQtiItem/qtiXmlRenderer/renderers/Renderer',
    'taoQtiItem/qtiCreator/helper/simpleParser',
    'text!taoQtiItem/../../test/samples/xml/qtiv2p1/rubricBlock/extended_text_rubric.xml'
],
function($, _, qtiClasses, CreatorRenderer, XmlRenderer, simpleParser, sampleXML){

    asyncTest('load creator', function(){

        var $container = $('#element'),
        $form = $('#form');

        var $rubricBlockXml = $(sampleXML).find('rubricBlock');
        var mathNs = 'm';//for 'http://www.w3.org/1998/Math/MathML'
        
        //parse xml
        var data = simpleParser.parse($rubricBlockXml, {
            ns : {
                math : mathNs
            },
            model : qtiClasses,
            loaded : function(rubricBlock){

                //render creator:
                var creatorRenderer = new CreatorRenderer({
                    shuffleChoices : false,
                    baseUrl : '/taoQtiItem/test/samples/test_base_www/',
                    interactionOptionForm : $(),
                    choiceOptionForm : $(),
                    responseOptionForm : $(),
                    bodyElementOptionForm : $form,
                    itemOptionForm : $(),
                    textOptionForm : $()
                });

                creatorRenderer.load(function(){

                    rubricBlock.setRenderer(this);
                    $container.append(rubricBlock.render());
                    var widget = rubricBlock.postRender({});
                    widget.on('containerBodyChange', function(){
                        console.log('changed!');
                    }, true);
                    
                }, this.getLoadedClasses());

                //save xml
                new XmlRenderer({shuffleChoices : false}).load(function(){
                    start();
                    ok(rubricBlock.render(this));
                });

            }
        });

        ok(data.body.body, 'has body');
        equal(_.size(data.body.elements), 4, 'elements ok');

    });

});