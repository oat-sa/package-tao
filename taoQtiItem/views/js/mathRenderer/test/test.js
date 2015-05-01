require([
    'jquery',
    'taoQtiItem/mathRenderer/mathRenderer',
    'taoQtiItem/qtiItem/helper/util',
    'text!taoQtiItem/../../test/samples/xml/qtiv2p1/rubricBlock/extended_text_rubric.xml'
], function($, mathRenderer, util, sampleXML){

    test('parse inline sample', function(){

        expect(0);

        var $rubricBlockXml = $(sampleXML).find('rubricBlock'),
            html = util.removeMarkupNamespaces($rubricBlockXml.html()),
            $container = $('#element').html(html);

        mathRenderer.render($container, {});

    });

});