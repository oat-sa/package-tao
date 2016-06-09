define([
    'lodash',
    'taoQtiItem/qtiItem/helper/EventMgr'
], function(_, EventMgr){
    
    test('on', function(){
        var eventMgr = new EventMgr();
        eventMgr.on('eventA', _.noop);
        equal(eventMgr.get('eventA').length, 1);
        
        eventMgr.on('eventA.ns1', _.noop);
        equal(eventMgr.get('eventA').length, 2);
    });
    
    test('trigger', function(){
        
        var eventMgr = new EventMgr();
        var varA = 'some value';
        var varB = true;
        
        eventMgr.on('eventA', function(){
            ok(true, 'eventA');
            equal(arguments[0], varA);
            equal(arguments[1], varB);
        });
        eventMgr.on('eventA.ns1', function(){
            ok(true, 'eventA.ns1');
            equal(arguments[0], varA);
            equal(arguments[1], varB);
        });
        
        eventMgr.trigger('eventA', [varA, varB]);
    });
    
    test('off', function(){
        
        var eventMgr = new EventMgr();
        eventMgr.on('eventA', _.noop);
        equal(eventMgr.get('eventA').length, 1);
        
        eventMgr.off('eventA');
        equal(eventMgr.get('eventA').length, 0);
        
        eventMgr.on('eventA', _.noop);
        eventMgr.on('eventA.ns1', _.noop);
        equal(eventMgr.get('eventA').length, 2);
        
        eventMgr.off('eventA');
        equal(eventMgr.get('eventA').length, 0);
        
    });
});


