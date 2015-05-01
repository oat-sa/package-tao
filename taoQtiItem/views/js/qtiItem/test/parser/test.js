require(['taoQtiItem/qtiItem/core/Parser', 'text!taoQtiItem/qtiItem/../../../test/samples/xml/qtiv2p1/associate.xml'], function(Parser, xml){

    var CL = console.log;

    test('loadXML', function(){
        expect(0);
        
        var parser = new Parser();
        parser.loadXML(xml);
        var qti = parser.getDOM();
        
        CL(qti, $(qti), $(qti).find('itemBody'));
    });

});


