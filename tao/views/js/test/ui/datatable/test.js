define(['jquery', 'ui/datatable'], function($){
    
    
    module('DataTable Test');
   
    test('plugin', function(){
       expect(1);
       ok(typeof $.fn.datatable === 'function', 'The datatable plugin is registered');
    });
   
    asyncTest('Initialization', function(){
        expect(2);
        
        var $elt = $('#container-1');
        ok($elt.length === 1, 'Test the fixture is available');
        
        
        $elt.on('create.datatable', function(){
            ok($elt.find('.datatable').length === 1, 'the layout has been inserted');
            start();
        });
        $elt.datatable({
            url : 'js/test/ui/datatable/data.json'
        });
    });

    asyncTest('Model loading', function(){
        expect(6);
        
        var $elt = $('#container-1');
        ok($elt.length === 1, 'Test the fixture is available');
        
        
        $elt.on('create.datatable', function(){
            ok($elt.find('.datatable').length === 1, 'the layout has been inserted');
            ok($elt.find('.datatable thead th').length === 7, 'the table contains 7 heads elements (id included)');
            equal($elt.find('.datatable thead th:eq(1)').text(), 'Login', 'the login label is created');
            equal($elt.find('.datatable thead th:eq(2)').text(), 'Name', 'the name label is created');
            equal($elt.find('.datatable thead th:eq(1)').data('sort-by'), 'login', 'the login col is sortable');
            start();
        });
        $elt.datatable({
            url : 'js/test/ui/datatable/data.json',
            'model' : [{
                id : 'login',
                label : 'Login',
                sortable : true
            },{
                id : 'name',
                label : 'Name',
                sortable : true
            },{
                id : 'email',
                label : 'Email',
                sortable : true
            },{
                id : 'role',
                label :'Roles',
                sortable : false
            },{
                id : 'dataLg',
                label : 'Data Language',
                sortable : true
            },{
                id: 'guiLg',
                label : 'Interface Language',
                sortable : true
            }]
        });
    });

    asyncTest('Pagination disabled', function(){
        expect(6);
        
        var $elt = $('#container-1');
        ok($elt.length === 1, 'Test the fixture is available');
        
        
        $elt.on('create.datatable', function(){
            ok($elt.find('.datatable').length === 1, 'the layout has been inserted');
            ok($elt.find('.datatable-backward').length === 2, 'there is 2 backward buttons');
            ok($elt.find('.datatable-forward').length === 2, 'there is 2 forward buttons');
            ok($elt.find('.datatable-backward:first').prop('disabled'), 'the backward button is disabled');
            ok($elt.find('.datatable-forward:last').prop('disabled'), 'the forward button is disabled');
            start();
        });
        $elt.datatable({
            url : 'js/test/ui/datatable/data.json',
            'model' : [{
                id : 'login',
                label : 'Login',
                sortable : true
            },{
                id : 'name',
                label : 'Name',
                sortable : true
            },{
                id : 'email',
                label : 'Email',
                sortable : true
            },{
                id : 'role',
                label :'Roles',
                sortable : false
            },{
                id : 'dataLg',
                label : 'Data Language',
                sortable : true
            },{
                id: 'guiLg',
                label : 'Interface Language',
                sortable : true
            }]
        });
    });

    asyncTest('Pagination enabled', function(){
        expect(7);
        
        var $elt = $('#container-1');
        ok($elt.length === 1, 'Test the fixture is available');
        
        
        $elt.on('create.datatable', function(){
            ok($elt.find('.datatable').length === 1, 'the layout has been inserted');
            ok($elt.find('.datatable-backward').length === 2, 'there is 2 backward buttons');
            ok($elt.find('.datatable-forward').length === 2, 'there is 2 forward buttons');
            ok($elt.find('.datatable-forward:first').prop('disabled') === false, 'the forward button is enabled');
            ok($elt.find('.datatable-forward:last').prop('disabled') === false, 'the forward button is disabled');
            ok($elt.find('.datatable-backward:first').prop('disabled'), 'the backward button is disabled (on the 1st page)');
            start();
        });
        $elt.datatable({
            url : 'js/test/ui/datatable/largedata.json',
            'model' : [{
                id : 'login',
                label : 'Login',
                sortable : true
            },{
                id : 'password',
                label : 'Pass',
                sortable : true
            },{
                id : 'title',
                label : 'Title',
                sortable : true
            },{
                id : 'firstname',
                label : 'First',
                sortable : true
            },{
                id : 'lastname',
                label :'Last',
                sortable : true
            },{
                id : 'gender',
                label : 'Gender',
                sortable : true
            },{
                id: 'email',
                label : 'Email',
                sortable : true
            },{
                id: 'picture',
                label : 'picture',
                sortable : true
            },{
                id: 'address',
                label : 'Address',
                sortable : true
            }]
        });
    });
});


