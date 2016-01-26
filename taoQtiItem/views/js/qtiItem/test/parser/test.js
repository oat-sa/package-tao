define([
    'jquery',
    'lodash',
    'taoQtiItem/qtiItem/helper/simpleParser',
    'text!taoQtiItem/test/samples/qtiv2p1/extended_text_rubric/qti.xml',
    'taoQtiItem/qtiItem/core/Loader',
    'taoQtiItem/qtiItem/core/Container',
    'taoQtiItem/qtiXmlRenderer/renderers/Renderer'
], function($, _, simpleParser, sampleXML, Loader, Container, XmlRenderer){

    QUnit.test('parse inline sample', function(){
        
        QUnit.stop();
        
        var $rubricBlockXml = $(sampleXML).find('rubricBlock');
        var mathNs = 'm';//for 'http://www.w3.org/1998/Math/MathML'
        var data = simpleParser.parse($rubricBlockXml, {
            ns : {
                math : mathNs
            }
        });

        QUnit.ok(data.body.body, 'has body');
        QUnit.equal(_.size(data.body.elements), 4, 'elements ok');

        var loader = new Loader();
        loader.loadRequiredClasses(data, function(){

            var container = new Container();
            this.loadContainer(container, data.body);

            var xmlRenderer = new XmlRenderer({shuffleChoices : false});
            xmlRenderer.load(function(){
                
                QUnit.start();
                
                var xml = container.render(this);

                var $container = $('<prompt>').html(xml);
                var containerData = simpleParser.parse($container, {
                    ns : {
                        math : mathNs
                    }
                });
                var containerBis = new Container();
                loader.loadContainer(containerBis, containerData.body);
                
                QUnit.equal(xml.length, containerBis.render(this).length);
            });
        });
    });

});