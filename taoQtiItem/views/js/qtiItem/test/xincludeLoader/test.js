define([
    'lodash',
    'taoQtiItem/qtiItem/helper/xincludeLoader',
    'taoQtiItem/qtiItem/core/Include'
], function(_, xincludeLoader, Include){

    QUnit.test('loading success', function(){
        
        QUnit.stop();
        
        var baseUrl = 'taoQtiItem/test/samples/qtiv2p1/associate_include/';
        var xinclude = new Include();
        xinclude.attr('href','stimulus.xml');
        
        xincludeLoader.load(xinclude, baseUrl, function(xi, data){
            
            QUnit.start();
            QUnit.ok(data.body.body, 'has body');
            QUnit.equal(_.size(data.body.elements), 2, 'elment img loaded');
            QUnit.equal(xi.qtiClass, 'include', 'qtiClass ok');
            
        });
        
    });
    QUnit.test('loading failure', function(){
        
        QUnit.stop();
        
        var baseUrl = 'taoQtiItem/test/samples/qtiv2p1/associate_include/';
        var xinclude = new Include();
        xinclude.attr('href','stimulus0.xml');
        
        xincludeLoader.load(xinclude, baseUrl, function(xi, data){
            
            QUnit.start();
            QUnit.equal(data, false, 'loading failure detected');
            
        });
        
    });

});