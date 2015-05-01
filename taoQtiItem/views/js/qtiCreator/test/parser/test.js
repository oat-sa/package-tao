require([
    'jquery',
    'lodash',
    'taoQtiItem/qtiCreator/helper/simpleParser',
    'text!taoQtiItem/../../test/samples/xml/qtiv2p1/rubricBlock/extended_text_rubric.xml',
    'taoQtiItem/qtiItem/core/Loader',
    'taoQtiItem/qtiItem/core/Container',
    'taoQtiItem/qtiXmlRenderer/renderers/Renderer'
], function($, _, simpleParser, sampleXML, Loader, Container, XmlRenderer){

    test('parse inline sample', function(){
        
        stop();
        
        var $rubricBlockXml = $(sampleXML).find('rubricBlock');
        var mathNs = 'm';//for 'http://www.w3.org/1998/Math/MathML'
        var data = simpleParser.parse($rubricBlockXml, {
            ns : {
                math : mathNs
            }
        });

        ok(data.body.body, 'has body');
        equal(_.size(data.body.elements), 4, 'elements ok');

        var loader = new Loader();
        loader.loadRequiredClasses(data, function(){

            var container = new Container();
            this.loadContainer(container, data.body);

            var xmlRenderer = new XmlRenderer({shuffleChoices : false});
            xmlRenderer.load(function(){
                
                start();
                
                var xml = container.render(this);

                var $container = $('<prompt>').html(xml);
                var containerData = simpleParser.parse($container, {
                    ns : {
                        math : mathNs
                    }
                });
                var containerBis = new Container();
                loader.loadContainer(containerBis, containerData.body);
                
                equal(xml, containerBis.render(this));
            });
        });
    });

});