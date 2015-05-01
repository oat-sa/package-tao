define(['taoQtiItem/qtiDefaultRenderer/widgets/Widget'], function(Widget){
    
    var MatchInteraction = Widget.extend({
        init : function(interaction){
            this._super(interaction);
            this.associations = [];
            this.options.matchMaxes = {};
            for(var i = 0; i < 2; i++){
                var set = interaction.getChoices(i);
                for(var serial in set){
                    var choice = set[serial];
                    this.options.matchMaxes[choice.attr('identifier')] = {
                        'matchMax' : parseInt(choice.attr('matchMax')),
                        'matchGroup' : [], //deprecated in qti 2.1
                        'current' : 0
                    }
                }
            }
        },
        render : function(){

            var ctx = this;

            //define the columns of the matrix from the last choice list
            $('#' + ctx.id + " .choice_list:last").addClass('choice_list_cols');
            var cols = new Array();
            $('#' + ctx.id + " .choice_list_cols li").each(function(){
                cols.push(this.id);
            });


            //define the rows of the matrix from the first choice list
            $('#' + ctx.id + " .choice_list:first").addClass('choice_list_rows');
            var rows = new Array();
            $('#' + ctx.id + " .choice_list_rows li").each(function(){
                rows.push(this.id);
            });

            //insert the node container (it will contain the nodes of the matrix)
            $('#' + ctx.id + " .choice_list_cols").after("<div class='match_node_container'></div>");

            //make the display adjustment
            $('#' + ctx.id + " .match_node_container").height(parseInt($('#' + ctx.id + " .choice_list_rows").height()));
            $('#' + ctx.id + " .match_node_container").css({
                'left' : $('#' + ctx.id + " .choice_list_rows").width()
            });

            var maxHeight = 25;
            $('#' + ctx.id + " .choice_list_cols li").each(function(){
                var height = parseInt($(this).height());
                if(height > maxHeight){
                    maxHeight = height;
                }
            });
            $('#' + ctx.id + " .choice_list_cols li").each(function(){
                var li = $(this);
                li.css('width', li.width() + 5);//tiny css adjustment for ie8
                var myDiv = $("<div />").css({
                    'width' : li.width(),
                    'height' : maxHeight + 'px',
                    'border' : li.css('border')
                });
                li.css('height', '25px');
                $(this).wrapInner(myDiv);
            });
            $('#' + ctx.id + " .prompt").css('margin-bottom', (maxHeight - 20) + 'px');

            //build all the nodes
            var i = 0;
            var currentHeight = 0;
            while(i < rows.length){
                var xnode = 'xnode_' + rows[i];
                var rowHeight = parseFloat($("#" + rows[i]).height());

                var j = 0;
                while(j < cols.length){
                    var ynode = 'ynode_' + cols[j];
                    var node_id = 'match_node_' + i + '_' + j;

                    //a node is a DIV with a ID made from X and Y indexes, and the classes of it's row and column
                    $('#' + ctx.id + " .match_node_container").append("<div id='" + node_id + "' class='match_node " + xnode + " " + ynode + "'>&nbsp;</div>");

                    //set the position and the size of the node
                    var left = 0;
                    if(j > 0){
                        var p = $("#" + 'match_node_' + i + '_' + (j - 1)).position();
                        left = parseInt(p.left) + parseInt($("#" + 'match_node_' + i + '_' + (j - 1)).width()) + (12);
                    }
                    var colWidth = parseFloat($("#" + cols[j]).width());
                    $('#' + ctx.id + " #" + node_id).css({
                        'top' : ((currentHeight) + (i * 2)) + 'px',
                        'left' : left + 'px',
                        'width' : colWidth + 'px',
                        'height' : rowHeight + 'px'
                    });
                    j++;
                }
                currentHeight += rowHeight;
                i++;
            }

            //match node on click
            $('#' + ctx.id + " .match_node").click(function(){
                ctx.selectNode($(this));
            });
        },
        setResponse : function(values){
            if(typeof(values) === 'object'){
                for(var i in values){
                    var value = values[i];
                    if(value['0'] && value['1']){
                        var row = value['0'];
                        var col = value['1'];
                        this.selectNode($('#' + this.id + " .match_node.xnode_" + row + ".ynode_" + col));
                    }
                }
            }
        },
        getResponse : function(){
            return this.resultCollector.match();
        },
        /**
         * Activate / deactivate nodes regarding:
         *  - the maxAssociations options that should'nt be exceeded
         *  - the matchMax option of the row and the column
         *  - the matchGroup option defining who can be associated with who (deprecated)
         * @param {jQuery} jElement
         */
        selectNode : function(jElement){
            var ctx = this;

            if(jElement.hasClass('tabActive')){
                ctx.deactivateNode(jElement);
            }
            else{
                if(this.associations.length < ctx.options['maxAssociations'] || ctx.options['maxAssociations'] == 0){

                    var nodeXY = this.getNodeXY(jElement);

                    //test the matchMax of the row choice
                    var rowMatch = ctx.options["matchMaxes"][nodeXY.xnode.id]['matchMax'];
                    if(rowMatch == 1){
                        $('#' + ctx.id + " ." + nodeXY.xnode['class']).each(function(){
                            ctx.deactivateNode($(this));
                        });
                    }
                    else if(rowMatch > 1){
                        var rowMatched = $('#' + ctx.id + " ." + nodeXY.xnode['class'] + ".tabActive").length;
                        if(rowMatched >= rowMatch){
                            jElement.effect("highlight", {color : '#B02222'}, 1000);
                            return false;
                        }
                    }

                    //test the matchMax of the column choice
                    var colMatch = ctx.options["matchMaxes"][nodeXY.ynode.id]['matchMax'];
                    if(colMatch == 1){
                        $("." + nodeXY.ynode['class']).each(function(){
                            ctx.deactivateNode($(this));
                        });
                    }
                    else if(colMatch > 1){
                        var colMatched = $('#' + ctx.id + " ." + nodeXY.ynode['class'] + ".tabActive").length;
                        if(colMatched >= colMatch){
                            jElement.effect("highlight", {color : '#B02222'}, 1000);

                            return false;
                        }
                    }

                    jElement.addClass('tabActive');
                    this.associations.push(jElement.attr('id'));
                }
            }
        },
        /**
         * Deactivate a node
         * 
         * @param {jQuery} jElement the matrix node under the jQuery format
         * @methodOf QTIWidget
         */
        deactivateNode : function(jElement){
            jElement.removeClass('tabActive');
            //note: Array.indexOf() is not implemented in IE:
            for(var i = 0; i < this.associations.length; i++){
                if(this.associations[i] == jElement.attr('id')){
                    this.associations.splice(i, 1);
                }
            }
        },
        /**
         * Exract the id from the rows and the columns from the node's classes
         * 
         * @param {jQuery} jElement the matrix node under the jQuery format
         * @returns {Object} with xnode an ynode id 
         * @methodOf QTIWidget
         */
        getNodeXY : function(jElement){

            var x = null;
            var y = null;

            var classes = jElement.attr('class').split(' ');
            for(i in classes){
                if(/^xnode_/.test(classes[i])){
                    x = {
                        'id' : classes[i].replace('xnode_', ''),
                        'class' : classes[i]
                    };
                }
                else if(/^ynode_/.test(classes[i])){
                    y = {
                        'id' : classes[i].replace('ynode_', ''),
                        'class' : classes[i]
                    };
                }
                if(x !== null && y !== null){
                    break;
                }
            }
            return {'xnode' : x, 'ynode' : y};
        }
    });
    
    return MatchInteraction;
});