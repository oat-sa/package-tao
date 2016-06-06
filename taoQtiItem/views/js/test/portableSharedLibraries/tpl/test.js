define([
    'jquery',
    'lodash',
    'OAT/util/tpl'
], function($, _, tplMgr){

    var containerId = 'interaction-container';

    QUnit.test('api', 2, function(assert){

        var tpl,
            $container = $('#' + containerId);

        assert.ok(typeof tplMgr === 'function', 'tpl manageris a function');

        tpl = tplMgr($container);

        assert.ok(typeof tpl.render === 'function', 'tpl.render() is a function');
    });

    QUnit.test('exists', 2, function(assert){
        var $container = $('#' + containerId);
        var tpl = tplMgr($container);
        assert.ok(tpl.exists('list'), 'list tpl found');
        assert.ok(tpl.exists('page'), 'list tpl found');
    });

    QUnit.test('render', 8, function(assert){
        var $container = $('#' + containerId);
        var tpl = tplMgr($container);

        $container.append(tpl.render('page', {content : '<p class="paragraph">paragraph content</p>'}));
        assert.equal($container.children('.page').children('.paragraph').length, 1, 'rendered "page" element found');

        $container.append(tpl.render('list',
            {items :
                    [
                        {
                            id : 'element1',
                            title : 'title of item 1'
                        },
                        {
                            id : 'element2',
                            title : 'title of item 2'
                        },
                        {
                            id : 'element3',
                            title : 'title of item 3'
                        }
                    ]
            }));
        
        var $li = $container.children('.nav-list').children('.nav-item');
        assert.equal($li.length, 3, 'rendered "list" element found');
        assert.equal($($li[0]).children('a').data('nav-id'), 'element1', 'template data properly rendered');
        assert.equal($($li[0]).children('a').text(), 'title of item 1', 'template data properly rendered');
        assert.equal($($li[1]).children('a').data('nav-id'), 'element2', 'template data properly rendered');
        assert.equal($($li[1]).children('a').text(), 'title of item 2', 'template data properly rendered');
        assert.equal($($li[2]).children('a').data('nav-id'), 'element3', 'template data properly rendered');
        assert.equal($($li[2]).children('a').text(), 'title of item 3', 'template data properly rendered');
        
    });

});

