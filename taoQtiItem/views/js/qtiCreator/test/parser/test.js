require([
    'jquery',
    'lodash',
    'taoQtiItem/qtiCreator/helper/simpleParser',
    'text!taoQtiItem/../../test/samples/xml/qtiv2p1/rubricBlock/extended_text_rubric.xml'
],
function($, _, simpleParser, sampleXML){

    test('parse inline sample', function(){
        
        var $rubricBlockXml = $(sampleXML).find('rubricBlock');
        var mathNs = 'm';//for 'http://www.w3.org/1998/Math/MathML'
        var data = simpleParser.parse($rubricBlockXml, {
            ns:{
                math:mathNs
            }
        });
        
        ok(data.body.body, 'has body');
        equal(_.size(data.body.elements), 4, 'elements ok');
    });

});