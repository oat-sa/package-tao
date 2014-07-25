define(['jquery', 'lodash', 'taoQtiItem/qtiCreator/helper/gridUnits', 'taoQtiItem/qtiCreator/core/gridEditor'], function($, _, gridUnits){

    var C = console;
    var $item = $('#item-grid');

    test('create and destroy', function(){
        //create
        $item.gridEditor();
        ok(typeof $item.data('qti-grid-options') === 'object' && !_.isEmpty($item.data('qti-grid-options')));

        //recreate
        $item.gridEditor({});
        ok(typeof $item.data('qti-grid-options') === 'object' && !_.isEmpty($item.data('qti-grid-options')));

        //destroy:
        $item.gridEditor('destroy');
        ok(!$item.data('qti-grid-options') && _.isEmpty($item.data('qti-grid-options')));
    });

    test('add insertables', function(){
        ok(!$('.qti-interaction').hasClass('ui-draggable'));
//        $item.gridEditor().gridEditor('addInsertables', $('.qti-interaction'));
        $item.gridEditor().gridEditor('addInsertables', $('#add_choice_interaction'));
        ok($('.qti-interaction').hasClass('ui-draggable'));
    });

    test('distribute units', function(){
        
        expect(0);
        return;
        var distributed;
        
        distributed = gridUnits.distribute([{
                elt : '#elt1',
                units : 4,
                min:4
            }, {
                elt : '#elt2',
                units : 5
            }, {
                elt : '#elt3',
                units : 3
            }
        ], 4);
        C.dir(distributed);
        
        distributed = gridUnits.distribute([{
                elt : '#elt1',
                units : 4
            }, {
                elt : '#elt2',
                units : 2
            }
        ], 4);
        C.dir(distributed);
        
        distributed = gridUnits.distribute([{
                elt : '#elt1',
                units : 12
            }
        ], 4);
        C.dir(distributed);
        
        distributed = gridUnits.distribute([{
                elt : '#elt1',
                units : 5
            }, {
                elt : '#elt2',
                units : 6
            }
        ], 4);
        C.dir(distributed);
    });
    
    test('redistribute units', function(){
        
        expect(0);
        
        var redistributed = gridUnits.redistribute([{
                elt : '#elt1',
                units : 4
            }, {
                elt : '#elt2',
                units : 5
            }
        ], 12);
        C.dir(redistributed);
        
        redistributed = gridUnits.redistribute([{
                elt : '#elt1',
                units : 3
            }, {
                elt : '#elt2',
                units : 1
            },{
                elt : '#elt2',
                units : 3
            }
        ], 10);
        C.dir(redistributed);
        
        
        redistributed = gridUnits.redistribute([{
                elt : '#elt1',
                units : 2
            }, {
                elt : '#elt2',
                units : 2
            },{
                elt : '#elt2',
                units : 4
            }
        ], 10);
        C.dir(redistributed);
    });
    
    test('create resizables', function(){
        expect(0);
        $item.gridEditor().gridEditor('resizable');
    });
    
    test('create movables', function(){
        expect(0);
        $item.gridEditor().gridEditor('createMovables', $('.item-editor-widget'));
    });
    
    test('data binding', function(){
        expect(0);
    });
    
});