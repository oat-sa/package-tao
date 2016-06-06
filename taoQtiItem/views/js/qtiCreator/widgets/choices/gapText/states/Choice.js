define([
    'jquery',
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/choices/states/Choice',
    'taoQtiItem/qtiCreator/widgets/choices/simpleAssociableChoice/states/Choice',
    'taoQtiItem/qtiItem/core/Element',
    'lodash'
], function($, stateFactory, Choice, SimpleAssociableChoice, Element, _){

    var GapTextStateChoice = stateFactory.extend(Choice, function(){

        var _widget = this.widget;

        //listener to other siblings choice mode
        _widget.beforeStateInit(function(e, element, state){

            if(Element.isA(element, 'choice') && _widget.interaction.getBody().getElement(element.serial)){//@todo hottext an

                if(state.name === 'choice' && element.serial !== _widget.serial){
                    _widget.changeState('question');
                }

            }

        }, 'otherActive');

        this.buildEditor();

    }, function(){

        this.destroyEditor();

        this.widget.offEvents('otherActive');
    });

    GapTextStateChoice.prototype.initForm = function(){
        SimpleAssociableChoice.prototype.initForm.call(this);
    };

    GapTextStateChoice.prototype.buildEditor = function(){

        var _widget = this.widget,
            $editableContainer = _widget.$container,
            $tlb = $editableContainer.find('.mini-tlb'),
            $editable;

        $tlb.detach();

        $editableContainer.wrapInner($('<div>', {'class' : 'inner-wrapper'}));
        $editableContainer.append($tlb);
        $editable = $editableContainer.find('.inner-wrapper');

        $editable.attr('contentEditable', true);

        //focus then reset content to have the cursor at the end:
        _focus($editable);

        $editable.on('keyup.qti-widget', _.throttle(function(){

            //update model
            _widget.element.val(_.escape($(this).text()));

        }, 200)).on('keypress.qti-widget', function(e){

            if(e.which === 13){
                e.preventDefault();
                $(this).blur();
                _widget.changeState('question');
            }

        });
    };

    GapTextStateChoice.prototype.destroyEditor = function(){

        var $container = this.widget.$container;

        $container.find('td').removeAttr('contentEditable');
        $container.children('td:first').off('keyup.qti-widget');
    };

    var _focus = function($el){

        var range, sel, el = $el[0];

        el.focus();
        if(window.getSelection !== undefined && document.createRange !== undefined){

            range = document.createRange();
            range.selectNodeContents(el);
            range.collapse(false);

            sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(range);

        }else if(document.body.createTextRange !== undefined){

            range = document.body.createTextRange();
            range.moveToElementText(el);
            range.collapse(false);
            range.select();
        }

    };
    return GapTextStateChoice;
});
