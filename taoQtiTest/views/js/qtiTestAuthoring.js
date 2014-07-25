define(['jquery', 'generis.tree.select', 'jquery.timePicker'], function($, GenerisTreeSelectClass){
    
    //define the default options
    var defaults =  {
        sequence : [],          //todo check if we really need sequence and labels...
        labels : [],    
        saveUrl : '',
        itemsTree: {
            itemsUrl : '',
            serverParameters : {}
        }
    };
    
    /**
     * QtiTestAuthoring user interaction component
     * @type {Object}
     */
    var QtiTestAuthoring = {
        
        /**
         * Initialize the  component
         * @param {jQueryElement} $container - the jQuery element that contains the authoring component (all events are bound to it)
         * @param {Object} [options] - override the default options
         * @param {Array} [options.sequence] - the default item sequence URIs
         * @param {Object} [options.labels] - the labels of the items as uri : label
         * @param {String} [option.saveUrl] - where to post the test data
         * @param {String} [option.itemsTree.itemsUrl] - the url where the item tree will retrieve items data 
         * @param {Object} [option.itemsTree.serverParameters] - additionnal parameters for the tree retrieving
         */
        init : function($container, options){
            var self = this;
            this.options = $.extend({}, defaults, options);
            this._$container = $container;
            
            this._initElements();
            
            this._setUpItemsTree();
            this._setUpItemSequence();
            this._setUpTimeLimits();
            
            $("#saver-action-item-sequence").click(function(e){
                e.preventDefault();
                self.save();
            });
            
            /**
             * @event QtiTestAuthoring#create
             */
            this._$container.trigger('create.qtitestauthoring');
        },
                
        /**
         * Initialize the jQuery elements
         * @private
         */
        _initElements : function(){
            this._$itemSequence = $("#item-sequence", this._$container);
            this._$orderingInfo = this._$itemSequence.prev('.elt-info');
            this._$shuffleInput = $("input[name='shuffle']", this._$container);
        },
                
        /**
         * Set up tte items tree
         * @private
         */
        _setUpItemsTree : function(){
            var self = this;
            var treeOptions = this.options.itemsTree;
            var sequence = self.options.sequence;
            
            new GenerisTreeSelectClass('#item-tree', treeOptions.itemsUrl,{
                    actionId: 'item',
                    saveUrl: self.options.saveUrl,
                    checkedNodes : sequence,
                    paginate:	10,
                    serverParameters: treeOptions.serverParameters,
                    saveCallback: function (data){
                    	var newSequence = jQuery.parseJSON(data["instances"]);
                        for (attr in data) {
                            if (/^instance_/.test(attr)) {
                                newSequence[parseInt(attr.replace('instance_', ''), 10)] = data[attr];
                            }
                        }
                        self.updateItemSequence(newSequence);
                    },
                    callback: {
                        checkPaginate: function(NODE, TREE_OBJ) {
                            //Check the unchecked that must be checked... olè!
                            this.check(sequence);
                        }
                    }
            });
        },
        
        /**
         * Set up tte items sequence
         * @private
         */
        _setUpItemSequence : function(){
            var self = this;
            
            this._$itemSequence.sortable({
                axis: 'y',
                opacity: 0.6,
                placeholder: 'ui-state-error',
                tolerance: 'pointer',
                update: function(){
                    var newSequence = $(this).sortable('toArray');
                    var i = 0;
                    for (i = 0; i < newSequence.length; i++) {
                        newSequence[i] = newSequence[i].replace('item_', '');
                    }
                    self.updateItemSequence(newSequence);
                }
            });
            
            this._$shuffleInput.click(function(){
                self._toggleOrdering();
            });
            self._toggleOrdering();
        },
        
        /**
         * Enable/disable ordering
         * @private
         */
        _toggleOrdering : function(){
            var $items = this._$itemSequence.find('li');
            if(this._isShuffling()){
                this._$itemSequence.sortable('disable');
                $items.css('padding-left', '4px')
                        .off('mousedown mouseup')
                        .find('.ui-icon').hide();
            } else {
                this._$itemSequence.sortable('enable');
                $items.on('mousedown', function(){
                    $(this).css('cursor', 'move');
                }).on('mouseup', function(){
                    $(this).css('cursor', 'pointer');
                }).css('padding-left', '25px')
                  .find('.ui-icon').show();
            }
            this._toggleOrderingInfo();
        },
        
        /**
         * Show/hide the info box on the sequence ordering widget
         * @private
         */
        _toggleOrderingInfo : function(){
            if (this._$itemSequence.find('li').length > 0 && !this._isShuffling()){
                this._$orderingInfo.show();
            } else {
                this._$orderingInfo.hide();
            }
        },
          
        /**
         * Check if the shuffle option is checked 
         * @private
         * @returns {Boolean} true if checked
         */
        _isShuffling : function(){
            return this._$shuffleInput.is(':checked');
        },
        
         /**
         * Set up the time limits widgets
         * @private
         */
        _setUpTimeLimits : function(){
            $('.time').timepicker({
                timeFormat: 'HH:mm:ss',
                showHour: true,
                showMinute : true,
                showSecond : true,
                showButtonPanel: false
            });
        },
        
        /**
         * Save the test
         */
        save: function(){
            var self = this;
            var sequence = this.getItemSequence();
            var toSend = {};
            var formInput;
            var index = 0;
            var formData = $('#qti-test-container > form').serializeArray();
            for(index in formData){
                var formInput = formData[index];
                toSend[formInput.name] = formInput.value; 
            }

            for(index in sequence){
                toSend['instance_'+index] = sequence[index];
            }
            
            toSend.classUri = $("input[name=classUri]").val();
            
            //do a server call
            $.ajax({
                url: self.options.saveUrl,
                type: "POST",
                data: toSend,
                dataType: 'json',
                success: function(response){
                    
                    /**
                     * @event QtiTestAuthoring#saved
                     * @param {boolea} saved - if the data are saved 
                     */
                    self._$container.trigger('saved.qtitestauthoring', [response.saved]);
                },
                complete: function(){
                    helpers.loaded();
                }
            });
        },
        
        /**
         * Get the item sequence from the widget
         * @returns {Array} the items' uris
         */
        getItemSequence : function(){
            var sequence = [];
            this._$itemSequence.find('li').each(function(index, elt){
                sequence.push($(elt).attr('id').replace('item_', ''));
            });
            
            //sync
            this.options.sequence = sequence;
            
            return sequence;
        },
        
        /**
         * Update the test's items 
         * @param {Array} sequence - the new item sequence
         */
        updateItemSequence : function(sequence){
            this._$itemSequence.html(this._itemListHtml(sequence));
            
            //sync
            this.options.sequence = sequence;
            this._toggleOrderingInfo();
            
           /**
            * @event QtiTestAuthoring#itemsupdate
            * @param {Array} sequence - the new items sequence
            */
            this._$container.trigger('itemsupdate.qtitestauthoring', [sequence]);
        },
                
        /**
         * Build the HTML list for the item sequence widget
         * @param {Array} items - the items' uris
         * @returns {String} the html
         */
        _itemListHtml : function(items){
            var html = '', itemId, i, index = 0;
            for (i in items) {
                itemId = items[i];
                index = parseInt(i, 10) + 1;
                html += "<li class='ui-state-default' id='" + itemId + "' >";
                html += "<span class='ui-icon ui-icon-arrowthick-2-n-s' /><span class='ui-icon ui-icon-grip-dotted-vertical' />";
                html += index + ". " + this.options.labels['item_' + itemId];
                html += "</li>";
            }
            return html;
        }
    };
    
    //expose the component
    return QtiTestAuthoring;
});

