define(['jquery', 'core/databinder'], function($, DataBinder){
    
    var model;
    
    module('2 ways data binding', {
        setup: function() {
            model = undefined;
            model = {
                "title": "testTitle",
                "testParts": [{
                        "navigationMode": 0,
                        "submissionMode": 0,
                        "assessmentSections": [{
                                "title": "assessmentSectionTitle",
                                "sectionParts": [{
                                        "href": "http:\/\/tao26.localdomain\/bertao.rdf#i138356893658013",
                                        "identifier": "i138356893658013"
                                    }, {
                                        "href": "http:\/\/tao26.localdomain\/bertao.rdf#i138356893796686",
                                        "identifier": "i138356893796686"
                                    }, {
                                        "href": "http:\/\/tao26.localdomain\/bertao.rdf#i138356893768139",
                                        "identifier": "i138356893768139"
                                    }, {
                                        "href": "http:\/\/tao26.localdomain\/bertao.rdf#i1383568938403212",
                                        "identifier": "i1383568938403212"
                                    }]
                            }]
                    }]
            };
        }
    });
   
    test('Simple assignment', function(){
        expect(5);
        
        var $container = $('#container-1');
        ok($container.length === 1, 'Test the fixture is available');
        
        equal($('h1', $container).text(), '', 'h1 is empty');
        equal($('h2', $container).text(), '', 'h2 is empty');
        
        new DataBinder($container, model).bind();
        
        equal($('h1', $container).text(), model.title, 'h1 has value assigned');
        equal($('h2', $container).text(), model.testParts[0].assessmentSections[0].sectionParts[1].href, 'h2 has value assigned');
    });
      
    test('Simple value change', function(){
        expect(3);
        
        var $container = $('#container-1');
        var $title = $('h1', $container);
        ok($container.length === 1, 'Test the fixture is available');
        
        new DataBinder($container, model).bind();
        
        equal($title.text(), model.title, 'h1 has value assigned');
        
        $title.text('new title').trigger('change');
        
        equal(model.title, 'new title', 'model has been updated');
    });
    
    test('Simple value remove', function(){
        expect(3);
        
        var $container = $('#container-1');
        var $title = $('h1', $container);
        ok($container.length === 1, 'Test the fixture is available');
        
        new DataBinder($container, model).bind();
        
        equal($title.text(), model.title, 'h1 has value assigned');
        
        $title.trigger('delete');
        
        strictEqual(model.title, undefined, 'model title has been removed');
    });
 
    test('Array assignment', function(){
        expect(4);
        
        var $container = $('#container-2');
        var $sectionParts = $('ul', $container);
        ok($container.length === 1, 'Test the fixture is available');
        
        new DataBinder($container, model).bind();
        
        strictEqual($sectionParts.find('li').length, model.testParts[0].assessmentSections[0].sectionParts.length, 'the same number of nodes has been inserted');
        equal($sectionParts.find('li:first').text(), model.testParts[0].assessmentSections[0].sectionParts[0].href, 'the first item contains the value');
        equal($sectionParts.find('li:last').text(), model.testParts[0].assessmentSections[0].sectionParts[3].href, 'the last item contains the value');
    });
    
    test('Array value change', function(){
        expect(3);
        
        var $container = $('#container-2');
        var $sectionParts = $('ul', $container);
        ok($container.length === 1, 'Test the fixture is available');
        
        new DataBinder($container, model).bind();
        
        var $firstPart = $sectionParts.find('li:first');
        equal($firstPart.text(), model.testParts[0].assessmentSections[0].sectionParts[0].href, 'the first item contains the value');
        
        $firstPart.text('new reference').trigger('change');
        
        equal(model.testParts[0].assessmentSections[0].sectionParts[0].href, 'new reference', 'array model has been updated');
        
    });
    
    test('Array value re-order', function(){
        expect(7);
        
        var $container = $('#container-3');
        var $sectionParts = $('ul', $container);
        ok($container.length === 1, 'Test the fixture is available');
        
        new DataBinder($container, model).bind();
        
        var $firstPart = $sectionParts.find('li:nth-child(1)');
        var $thirdPart = $sectionParts.find('li:nth-child(3)');
        equal($firstPart.find('[data-bind="href"]').text(), model.testParts[0].assessmentSections[0].sectionParts[0].href, 'the first item contains the value');
        equal($thirdPart.find('[data-bind="href"]').text(), model.testParts[0].assessmentSections[0].sectionParts[2].href, 'the third item contains the value');
        
        //reorder from 0123 to 0213
        $firstPart.after($thirdPart);
        $sectionParts.trigger('change');
        
        equal(model.testParts[0].assessmentSections[0].sectionParts[1].href, "http:\/\/tao26.localdomain\/bertao.rdf#i138356893768139", 'the model value order has been updated');
        equal(model.testParts[0].assessmentSections[0].sectionParts[2].href, "http:\/\/tao26.localdomain\/bertao.rdf#i138356893796686", 'the model value order has been updated');
        
        $sectionParts.find('li:nth-child(2)').find('[data-bind="href"]').text('toto').trigger('change');
        equal(model.testParts[0].assessmentSections[0].sectionParts[1].href, "toto", 'the model value order has been updated');
        equal(model.testParts[0].assessmentSections[0].sectionParts[1].index, "1", 'the model value index has been updated');
    });
    
     test('Array value remove', function(){
        expect(8);
        
        var $container = $('#container-3');
        var $sectionParts = $('ul', $container);
        ok($container.length === 1, 'Test the fixture is available');
        
        new DataBinder($container, model).bind();
        
        var $firstPart = $sectionParts.find('li:nth-child(1)');
        
        strictEqual(model.testParts[0].assessmentSections[0].sectionParts.length, 4, 'model length has been updated');
        
        $firstPart.trigger('delete').remove();
        
        strictEqual(model.testParts[0].assessmentSections[0].sectionParts.length, 3, 'model length has been updated');
        strictEqual(model.testParts[0].assessmentSections[0].sectionParts[0].index, 0, 'model element has been removed');
        strictEqual($sectionParts.find('li:nth-child(1)').data('bind-index'), '0', 'the node index is up to date');
        equal($sectionParts.find('li:nth-child(1) [data-bind="href"]').text(), "http:\/\/tao26.localdomain\/bertao.rdf#i138356893796686", 'the model value order has been updated');
    
         //test rebinding after removal
         $sectionParts.find('li:nth-child(1)  [data-bind="href"]').text('http://new.url').trigger('change');
         
         equal($sectionParts.find('li:nth-child(1) [data-bind="href"]').text(), 'http://new.url', 'the model value has changed');
         equal(model.testParts[0].assessmentSections[0].sectionParts[0].href, 'http://new.url', 'the model value has changed');
     });
    
    test('Array value add', function(){
        expect(4);
        
        var $container = $('#container-4');
        var $sectionParts = $('ul', $container);
        ok($container.length === 1, 'Test the fixture is available');
        
        new DataBinder($container, model).bind();
        
        var $newSection = $("<li><span data-bind='identifier'>sectionpart-5</span><span data-bind='href'>http://new.rdf#test</span></li>");
        
        $sectionParts.append($newSection).trigger('add');
        
        strictEqual(model.testParts[0].assessmentSections[0].sectionParts.length, 5, 'model length has been updated');
        equal(model.testParts[0].assessmentSections[0].sectionParts[4].identifier, 'sectionpart-5', 'model element has been added');
        equal(model.testParts[0].assessmentSections[0].sectionParts[4].href, 'http://new.rdf#test', 'model element has been added');
    });
    
     test('Array value filter', function(){
        expect(2);
        
        model.testParts[0].assessmentSections[0].sectionParts[0]['qti-type'] = 'assessmentItemRef';
        model.testParts[0].assessmentSections[0].sectionParts[1]['qti-type'] = 'assessmentSectionRef';
        model.testParts[0].assessmentSections[0].sectionParts[2]['qti-type'] = 'assessmentItemRef';
        model.testParts[0].assessmentSections[0].sectionParts[3]['qti-type'] = 'assessmentSectionRef';
        
        var $container = $('#container-5');
        var $sectionParts = $('ul', $container);
        ok($container.length === 1, 'Test the fixture is available');
        
        new DataBinder($container, model, {
            filters : {
                    'isItemRef' : function(value){
                        return value['qti-type'] && value['qti-type'] === 'assessmentItemRef';
                    }
            }
        }).bind();
        
        strictEqual($sectionParts.find('li').length, 2, 'only filtered values has been assigned');
    });
    
    test('Rm binding', function(){
        expect(5);
        
        var $container = $('#container-6');
        ok($container.length === 1, 'Test the fixture is available');
        
        new DataBinder($container, model).bind();
        
        equal(model.title, 'testTitle', 'The title attribute is present');
        $container.find('input:first-child').trigger('change');
        ok(model.title === undefined, 'The title attribute has been removed');
        
        equal(model.testParts[0].assessmentSections[0].sectionParts.length, 4, 'The section parts contains 4 elements');
        $container.find('input:last-child').trigger('change');
        equal(model.testParts[0].assessmentSections[0].sectionParts.length, 3, 'A section parts has been remvoved');
    });
    
});


