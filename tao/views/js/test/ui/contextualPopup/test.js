define(['jquery', 'ui/contextualPopup'], function($, contextualPopup){

    'use strict';
    
    QUnit.module('init popup');
    
    QUnit.test('with content string', function(){

        var $container = $('#main-container');
        var popup1 = contextualPopup($container.find('.center1'), $container, {content : 'content 1'});
        
        QUnit.assert.equal(popup1.getPopup().length, 1, 'popup created');
        QUnit.assert.ok(popup1.getPopup().is(':visible'), 'popup initially visible');
        QUnit.assert.ok(popup1.getPopup().hasClass('bottom'), 'positioned on the bottom');
        QUnit.assert.equal(popup1.getPopup().find('.done').length, 0, 'done button absent');
        QUnit.assert.equal(popup1.getPopup().find('.cancel').length, 0, 'cancel button absent');
        
        popup1.destroy();
    });
    
    QUnit.test('with jquery element', function(){
        
        var $container = $('#main-container');
        var $content2 = $('<ul><li>element 1</li><li>element 2</li><li>element 3</li></ul');
        var popup2 = contextualPopup($container.find('.center2'), $container, {content : $content2});
        
        QUnit.assert.equal(popup2.getPopup().length, 1, 'popup created');
        QUnit.assert.ok(popup2.getPopup().is(':visible'), 'popup initially visible');
        QUnit.assert.ok(popup2.getPopup().hasClass('bottom'), 'positioned on the bottom');
        QUnit.assert.equal(popup2.getPopup().find('.done').length, 0, 'done button absent');
        QUnit.assert.equal(popup2.getPopup().find('.cancel').length, 0, 'cancel button absent');
        QUnit.assert.equal(popup2.getPopup().find('li').length, 3, 'html content');
        
        popup2.destroy();
    });
    
    QUnit.test('with controls', function(){
        
        var $container = $('#main-container');
        var popup3 = contextualPopup($container.find('.center3'), $container, {content : 'content 3', controls : {done : true, cancel : true}});
        
        QUnit.assert.equal(popup3.getPopup().length, 1, 'popup created');
        QUnit.assert.ok(popup3.getPopup().is(':visible'), 'popup initially visible');
        QUnit.assert.ok(popup3.getPopup().hasClass('bottom'), 'positioned on the bottom');
        QUnit.assert.equal(popup3.getPopup().find('.done').length, 1, 'done button added');
        QUnit.assert.equal(popup3.getPopup().find('.cancel').length, 1, 'cancel button added');
        
        popup3.destroy();
    });
    
    QUnit.test('with positioning on top', function(){
        
        var $container = $('#main-container');
        var popup4 = contextualPopup($container.find('.center4'), $container, {content: 'content 4', position:'top'});
        
        QUnit.assert.equal(popup4.getPopup().length, 1, 'popup created');
        QUnit.assert.ok(popup4.getPopup().is(':visible'), 'popup initially visible');
        QUnit.assert.ok(popup4.getPopup().hasClass('top'), 'positioned on top');
        
        popup4.destroy();
    });
    
    QUnit.module('api');
    
    QUnit.test('show/hide/isVisible', function(){
        
        var $container = $('#main-container');
        var popup1 = contextualPopup($container.find('.center1'), $container, {content : 'content 1'});
        
        QUnit.assert.equal(popup1.getPopup().length, 1, 'popup created');
        QUnit.assert.ok(popup1.getPopup().is(':visible'), 'popup initially visible');
        QUnit.assert.ok(popup1.isVisible(), 'popup initially visible');
        popup1.hide();
        QUnit.assert.ok(!popup1.getPopup().is(':visible'), 'popup is hidden');
        QUnit.assert.ok(!popup1.isVisible(), 'popup is hidden');
        popup1.show();
        QUnit.assert.ok(popup1.getPopup().is(':visible'), 'popup is visible again');
        QUnit.assert.ok(popup1.isVisible(), 'popup is visible again');
    });
    
    QUnit.test('setContent', function(){
        
        var $container = $('#main-container');
        var popup1 = contextualPopup($container.find('.center1'), $container, {content : 'content 1'});
        QUnit.assert.equal(popup1.getPopup().children('.popup-content').html(), 'content 1', 'intial content');
        
        popup1.setContent('content A');
        QUnit.assert.equal(popup1.getPopup().children('.popup-content').html(), 'content A', 'updated content');
        
        var $contentB = $('<p class="some-paragraph">contentB</p>');
        popup1.setContent($contentB);
        QUnit.assert.equal(popup1.getPopup().children('.popup-content').children()[0], $contentB[0], 'updated content');
        
        popup1.destroy();
    });
    
    QUnit.test('done', function(){
        
        //done button hides and trigger event done
        QUnit.expect(4);
        
        var $container = $('#main-container');
        var popup1 = contextualPopup($container.find('.center1'), $container, {content : 'content 1', controls:{done:true}});
        $container.off('.contextual-popup').on('done.contextual-popup', function(){
            QUnit.assert.ok(true, 'triggered done');
        }).on('hide.contextual-popup', function(){
            QUnit.assert.ok(true, 'triggered hide');
        });
        
        //done programmatically
        popup1.done();
        
        //redisplay it
        popup1.show();
        
        //done by clicking on the button
        popup1.getPopup().find('.btn.done').click();
        
        popup1.destroy();
    });

    QUnit.test('callbacks', function(){

        QUnit.expect(8);

        var $container = $('#main-container');
        var popup1 = contextualPopup(
            $container.find('.center1'),
            $container,
            {
                content : 'content 1',
                controls : {
                    done : true,
                    cancel : true
                },
                callbacks : {
                    beforeDone : function () {
                        return true;
                    },
                    beforeCancel : function () {
                        return true;
                    }
                }
            }
        );

        $container.off('.contextual-popup')
            .on('done.contextual-popup', function(){
                QUnit.assert.ok(true, 'triggered done');
            }).on('cancel.contextual-popup', function(){
                QUnit.assert.ok(true, 'triggered cancel');
            });

        //redisplay it
        popup1.show();
        QUnit.assert.ok(popup1.isVisible());

        //done by clicking on the button
        popup1.getPopup().find('.btn.done').click();
        QUnit.assert.ok(!popup1.isVisible());

        //redisplay it
        popup1.show();
        QUnit.assert.ok(popup1.isVisible());

        //done by clicking on the button
        popup1.getPopup().find('.btn.cancel').click();
        QUnit.assert.ok(!popup1.isVisible());

        popup1.destroy();


        var popup2 = contextualPopup(
            $container.find('.center1'),
            $container,
            {
                content : 'content 1',
                controls : {
                    done : true,
                    cancel : true
                },
                callbacks : {
                    beforeDone : function () {
                        return false;
                    },
                    beforeCancel : function () {
                        return false;
                    }
                }
            }
        );

        popup2.show();

        //should not be hidden
        popup2.getPopup().find('.btn.done').click();
        QUnit.assert.ok(popup2.isVisible());

        //should not be hidden
        popup2.getPopup().find('.btn.cancel').click();
        QUnit.assert.ok(popup2.isVisible());

        popup2.destroy();
    });

    QUnit.test('cancel', function(){
        
        //cancel button hides and trigger event done
        QUnit.expect(4);
        
        var $container = $('#main-container');
        var popup1 = contextualPopup($container.find('.center1'), $container, {content : 'content 1', controls:{cancel:true}});
        $container.off('.contextual-popup').on('cancel.contextual-popup', function(){
            QUnit.assert.ok(true, 'triggered cancel');
        }).on('hide.contextual-popup', function(){
            QUnit.assert.ok(true, 'triggered hide');
        });
        
        //cancel programmatically
        popup1.cancel();
        
        //redisplay it
        popup1.show();
        
        //done by clicking on the button
        popup1.getPopup().find('.btn.cancel').click();
        
        popup1.destroy();
    });
    
    QUnit.module('visual test');
    
    QUnit.test('check', function(){
        
        QUnit.expect(0);
        
        var $container = $('#visual-test');
        var popup1 = contextualPopup($container.find('.center1'), $container, {content : 'content 1'});
        var $content2 = $('<ul><li>element 1</li><li>element 2</li><li>element 3</li></ul');
        var popup2 = contextualPopup($container.find('.center2'), $container, {content : $content2});
        var popup3 = contextualPopup($container.find('.center3'), $container, {content : 'content 3', controls : {done : true, cancel : true}});
        var popup4 = contextualPopup($container.find('.center4'), $container, {content: 'content 4', position:'top'});
    });
    
});