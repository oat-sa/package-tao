define([
    'jquery',
    'lodash',
    'OAT/util/asset'
], function($, _, assetMgr){

    var containerId = 'interaction-container';
    
    QUnit.test('api', function(assert){
        
        var asset,
            $container = $('#'+containerId);
            
        assert.ok(typeof assetMgr === 'function', 'asset helper is a function');
        
        asset = assetMgr($container);
        
        assert.ok(typeof asset.get === 'function', 'asset.get() is a function');
        assert.ok(typeof asset.getAll === 'function', 'asset.getAll() is a function');
    });
    
    QUnit.test('exists', function(assert){
        var $container = $('#'+containerId);
        var asset = assetMgr($container);
        assert.equal(_.size(asset.getAll()), 8, 'all assets identified');
        assert.ok(asset.exists('asset/add.png'), 'asset url found');
    });
    
    QUnit.test('get', function(assert){
        var $container = $('#'+containerId);
        var asset = assetMgr($container);
        assert.equal(_.size(asset.getAll()), 8, 'all assets identified');
        assert.equal(asset.get('asset/add.png'), 'some/path/to/asset/icon_add.png', 'asset url found');
    });
    
});

