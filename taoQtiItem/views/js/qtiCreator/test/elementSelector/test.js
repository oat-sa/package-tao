require([
    'lodash',
    'jquery',
    'taoQtiItem/qtiCreator/editor/elementSelector/selector',
    'json!taoQtiItem/qtiCreator/test/elementSelector/interactions'
], function(_, $, selector, interactions){
    
    QUnit.test('init dialog', function(){

        QUnit.expect(5);

        var $container = $('#item-editor-panel');
        selector.create($('#center1'), $container, interactions);
        selector.create($('#center2'), $container, interactions);

        $container.on('selected.element-selector', function(e, qtiClass, $trigger){
            if(qtiClass === 'hotspotInteraction'){
                QUnit.assert.ok($('#center1 .element-list li[data-qti-class=hotspotInteraction]').hasClass('active'),'hotspotInteraction selected');
            }else if(qtiClass === 'choiceInteraction'){
                QUnit.assert.ok($('#center1 .element-list li[data-qti-class=choiceInteraction]').hasClass('active'),'choiceInteraction selected');
                QUnit.assert.ok(!$('#center1 .element-list li[data-qti-class=hotspotInteraction]').hasClass('active'),'hotspotInteraction unselected');
            }else if(qtiClass === '_container'){
                QUnit.assert.ok($('#center1 .element-list li[data-qti-class=_container]').hasClass('active'),'_container selected');
                QUnit.assert.ok(!$('#center1 .element-list li[data-qti-class=choiceInteraction]').hasClass('active'),'choiceInteraction unselected');
            }
        });
        
        $container.find('#center1 .element-list li[data-qti-class=hotspotInteraction]').click();
        selector.activateElement($('#center1'), 'choiceInteraction');
        selector.activateElement($('#center1'), '_container');
        
        selector.activatePanel($container, 'Text Block');
    });
    
    QUnit.test('done/cancel button', function(){
        
        QUnit.expect(4);
        
        var $container = $('#item-editor-panel');
        var $anchor = $('#center3');
        var mySelector = selector.create($anchor, $container, interactions);
        
        $anchor.on('done.element-selector', function(){
            QUnit.assert.ok(true, 'done clicked');
            mySelector.show();
        }).on('cancel.element-selector', function(){
            QUnit.assert.ok(true, 'canceled clicked');
            mySelector.show();
        });
        
        mySelector.done();
        mySelector.cancel();
        $anchor.find('.done').click();
        $anchor.find('.cancel').click();
        
    });
});