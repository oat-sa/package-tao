define([
    'lodash',
    'jquery',
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/helpers/deletingState',
    'taoQtiItem/qtiCreator/helper/gridUnits',
    'taoQtiItem/qtiCreator/editor/gridEditor/helper',
    'taoQtiItem/qtiCreator/editor/gridEditor/content'
], function(_, $, stateFactory, deletingHelper, gridUnits, gridHelper, contentHelper){

    var DeletingState = stateFactory.create('deleting', function(){

        var element = this.widget.element;

        //array to store new col untis
        this.refactoredUnits = [];
        this.updateBody = false;

        //reference to the dom element(s) to be remove on delete
        this.$elementToRemove = this.getElementToRemove();
        
        this.hideWidget();

        this.showMessage(element);

        element.data('deleting', true);

        //trigger resizing
        if(this.updateBody){
            
            //store reference to the item and its container:
            this.item = element.getRelatedItem();
            this.$item = this.item.data('widget').$container.find('.qti-itemBody');

            //call for resize action
            this.$item.trigger('resize.qti-widget');
        }

    }, function(){

        this.showWidget();
        this.widget.element.data('deleting', false);
        $('body').off('.deleting');

        if(this.updateBody){
            this.$item.trigger('resize.qti-widget');
        }
    });

    /**
     * @todo move widget specific code to their respective location
     * 
     * @returns {jQuery} container
     */
    DeletingState.prototype.getElementToRemove = function(){

        var $container = this.widget.$container;
        
        //if is a choice widget:
        if($container.hasClass('qti-choice')){

            if($container.prop('tagName') === 'TH'){

                //matchInteraction:
                if($container.parent().parent().prop('tagName') === 'THEAD'){

                    //hide col
                    var $tbody = $container.closest('table.matrix').children('tbody');
                    var $tds = $tbody.children('tr').find('td:last');
                    return $container.add($tds);

                }else if($container.parent().parent().prop('tagName') === 'TBODY'){

                    //hide row
                    return $container.parent();
                }
            }else{
                return $container;
            }

        }

        /**
         * inline widget
         */
        if($container.hasClass('widget-inline')){
            return $container.add(this.widget.$original);
        }
        
        /**
         * block widget
         */
        var $col = $container.parent();
        
        //check sub-column condition
        var $subCol = $container.parent('.colrow');
        if($subCol.length){
            
            this.updateBody = true;
            
            var $colMulti = $subCol.parent();
            if($colMulti.find('.colrow').length === 1){
                //this is the only sub-column remaining, hide the entire col
                $col = $colMulti;
            }else{
                //hide the whole sub-column only :
                return $subCol;
            }
        }
        
        //check if we should hide the col only or the whole row
        var $row = $col.parent('.grid-row');
        if($row.length){
            this.updateBody = true;
            if($row.children().length === 1){
                //if it is the only col in the row, hide the entire row
                return $row;
            }else{
                //else, hide the current one ...
                return $col;
            }
        }else if($container.hasClass('grid-row')){
            //rubric block:
            this.updateBody = true;
            return $container;
        } 
                
        //other block widgets:
        if($container.hasClass('widget-block') || $container.hasClass('widget-blockInteraction')){
            return $container;
        }
    };

    DeletingState.prototype.hideWidget = function(){

        var $elt = this.$elementToRemove;

        if($elt.length){
            $elt.hide();
            if(_isCol($elt)){
                //it is a column : redistribute the units of the columdn to the others
                this.refactoredUnits = _redistributeUnits($elt);
            }
        }
    };

    var _isCol = function($col){
        var attrClass = $col.attr('class');
        return (attrClass && /col-([\d]+)/.test(attrClass));
    };

    var _redistributeUnits = function($col){

        var usedUnits = $col.data('units');
        var $otherCols = $col.siblings();
        var cols = [];

        $otherCols.each(function(){

            var $col = $(this),
                units = $col.data('units');

            cols.push({
                elt : $col,
                units : units
            });

            usedUnits += units;
        });

        cols = gridUnits.redistribute(cols);

        _.each(cols, function(col){
            col.elt.removeClass('col-' + col.units).addClass('col-' + col.refactoredUnits);
            gridHelper.setUnitsFromClass(col.elt);
        });

        //store results in the element for future ref?
        $col.data('redistributedUnits', cols);

        return cols;
    };

    DeletingState.prototype.showWidget = function(){

        var $elt = this.$elementToRemove;
        if($elt.length && $.contains(document, $elt[0])){

            $elt.show();

            if(_isCol($elt)){
                //restore the other units:
                _.each(this.refactoredUnits, function(col){
                    col.elt.removeClass('col-' + col.refactoredUnits).addClass('col-' + col.units);
                    gridHelper.setUnitsFromClass(col.elt);
                });
            }
        }
    };

    DeletingState.prototype.showMessage = function(){

        var _this = this,
            _widget = this.widget,
            $messageBox = deletingHelper.createInfoBox([_widget]);

        $messageBox.on('confirm.deleting', function(){

            _this.deleteElement();

        }).on('undo.deleting', function(){

            try{
                _widget.changeState('question');
            }catch(e){
                _widget.changeState('active');
            }
        });

        this.messageBox = $messageBox;
    };

    DeletingState.prototype.deleteElement = function(){

        this.refactoredUnits = [];

        this.$elementToRemove.remove();//remove html from the dom
        this.widget.destroy();//remove what remains of the widget (almost nothing), call this after element remove
        this.widget.element.remove();//remove from model

        if(this.updateBody){
            //need to update item body
            this.item.body(contentHelper.getContent(this.$item));
        }

    };

    return DeletingState;
});