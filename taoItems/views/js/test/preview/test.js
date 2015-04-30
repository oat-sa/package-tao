define([
    'jquery',
    'taoItems/preview/preview'
], function($, preview){

    var containerId = 'item-container';

    QUnit.module('Preview  API');
   
    QUnit.test('preview module', function(assert){
        assert.ok(typeof preview !== 'undefined', 'The modue exports something');
        assert.ok(typeof preview === 'object', 'The modue exports an object');
        assert.ok(typeof preview.init === 'function', 'The modue exposes an init method');
    });

    QUnit.module('Preview Init', {
        teardown : function(){
            $('body .preview-overlay').remove();
            $('.select2-hidden-accessible').remove();
        }
    });
  
    QUnit.test('preview with a wrong item URI', function(assert){
        assert.throws(function(){
            preview.init(null);
        }, TypeError, 'Wrong uri given');
    });

    QUnit.asyncTest('preview with a valid item URI', function(assert){
        QUnit.expect(3);
        
        preview.init('http://foo.bar');
         
        setTimeout(function(){
           
            assert.equal($('body .preview-overlay').length, 1, 'The preview creates and overlay element in the body');
           
            assert.ok($('body #preview-console').length > 0, 'the preview creates a preview-console elt');
 
            assert.ok($('body .preview-overlay .preview-canvas').length > 0, 'the preview creates a preview-console elt');
            QUnit.start();
        }, 500);
          
    });

    QUnit.asyncTest('preview with a window that resize', function(assert){
        QUnit.expect(2);
        
        preview.init('http://foo.bar');
         
        setTimeout(function(){
            assert.equal($('body .preview-overlay').length, 1, 'The preview creates and overlay element in the body');
                  
            var width = $('body .preview-overlay').width();
 
            setTimeout(function(){

               //$(window).trigger('resize', [800, 600]);
                 window.resizeBy(-50, -50);
                var newWidth = $('body .preview-overlay').width();
        
                assert.ok(newWidth === width, 'size of the overlay changes after a resize of the window');
                QUnit.start();
            }, 500);


        }, 500);
          
    });
});

