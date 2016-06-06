define(['taoQtiItem/qtiItem/core/Loader', 'taoQtiItem/qtiItem/core/Element', 'json!taoQtiItem/qtiItem/../../../test/samples/json/ALL.json'], function(Loader, Element, data){

    test('loadItemData', function(){
        var loader = new Loader();
        for(var identifier in data){
            stop();
            loader.loadItemData(data[identifier].full, function(item){
                start();
                ok(Element.isA(item, 'assessmentItem'), identifier + ' item loaded');
                ok(item.attr('identifier'), identifier, identifier + ' has correct id');
                ok(item.getInteractions().length, identifier + ' has ' + item.getInteractions().length + ' interaction(s)');
            });

        }

    });

});