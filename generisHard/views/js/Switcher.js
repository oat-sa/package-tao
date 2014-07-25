
define(['jquery', 'context', 'i18n'], function($, context, __){
    'use strict';

    //create a grid, and update it after each hardification:
    function Switcher(tableElementId, userOptions){

            this.options = {
                    onStart: function(){},
                    onStartDecompile: function(){},
                    onStartEmpty: function(){},
                    beforeComplete: function(){},
                    onComplete: function(){},
                    onCompleteDecompile: function(){}
            };

            if (userOptions){
                    this.options = $.extend(this.options, userOptions);
            }

            this.$grid = $('#'+tableElementId);
            this.theData = [];
            this.currentIndex = -1;
            this.forcedStart = false;
            this.decompile = false;

            Switcher.instances[tableElementId] = this;
    }

    Switcher.instances = [];

    Switcher.prototype.getActionUrl = function(action){
            var url = context.root_url;
            url += 'generisHard/Optimize/' + action;

            return url;
    };

    Switcher.prototype.init = function(){

            var __this = this;
            var actionUrl = __this.getActionUrl('optimizeClasses');

            $.ajax({
                    type: "POST",
                    url: actionUrl,
                    data: {},
                    dataType: 'json',
                    success: function(r){
                            if (r.length == 0){
                                    if(__this.options.onStartEmpty) __this.options.onStartEmpty(__this);
                                    return false;
                            }

                            var gridOptions = {
                                            datatype: "local", 
                                            hidegrid : false,
                                            colNames: [ __('Classes'), __('Status'), __('Instances')], 
                                            colModel: [ 
                                                       {name: 'class', index: 'class', sortable: false},
                                                       {name: 'status', index: 'status', align: 'center', sortable: false},
                                                       {name: 'count', index: 'count', align: 'center', sortable: false}
                                                       ], 
                                                       height: 'auto', 
                                                       autowidth: true,
                                                       width:(parseInt(__this.$grid.width()) - 2),
                                                       sortname: 'status', 
                                                       sortorder: "asc", 
                                                       caption: __("Available Classes"),

                            };

                            //build jqGrid:
                            require(['require', 'jquery', 'grid/tao.grid'], function(req, $) {
                                    __this.$grid.jqGrid(gridOptions);

                                    //insert rows:
                                    var i = 0;
                                    for (var uri in r){
                                            var row = r[uri];
                                            var rClass = row['class'];
                                            var rClassUri = row['classUri'];
                                            var rStatus = (row['status'] === 'compiled') ? __('Production') : __('Design') ;
                                            var rCount = row['count'];
                                            var rStatusTag = row['status'];
                                            __this.setRowData(i, {'class': rClass, 'status': rStatus, 'count': rCount, 'classUri': rClassUri, 'statusTag': rStatusTag});
                                            i++;
                                    }
                            });

                    }
            });

            return true;
    };

    Switcher.prototype.startCompilation = function(){
                    this.decompile = false;
                    this.currentIndex = 0;

            for (var j = 0; j < this.theData.length; j++){
                            if (this.currentIndex < 0 && this.theData[j].statusTag !== 'compiled'){
                                            this.currentIndex = j;
                            }
                    }

                    if (this.options.onStart){
                        this.options.onStart(this);
                    }

            this.nextStep();
    };

    Switcher.prototype.startDecompilation = function(){
                    this.currentIndex = 0;
                    this.decompile = true;

            if (this.options.onStartDecompile){
                    this.options.onStartDecompile(this);
            }

            for (var j = 0; j < this.theData.length; j++){
                            if (this.currentIndex < 0 && this.theData[j].statusTag !== 'decompiled'){
                                            this.currentIndex = j;
                            }
                    }

            this.nextStep();
    };

    Switcher.prototype.setRowData = function(rowId, data){

            if (typeof(this.theData[rowId]) !== 'undefined'){
                    this.$grid.jqGrid('setRowData', rowId, data);
            }
            else{
                    this.$grid.jqGrid('addRowData', rowId, data);
            }

            this.theData[rowId] = data;
    };

    Switcher.prototype.setCellData = function(rowId, colName, data){
            this.$grid.jqGrid('setCell', rowId, colName, data);
            this.theData[rowId][colName] = data;
    };

    Switcher.prototype.addResultData = function(rowId, data){
            this.theData[rowId].compilationResults = null;
            this.theData[rowId].compilationResults = data;
    };

    Switcher.prototype.getRowIdByUri = function(classUri){
            var returnValue = -1;

            for (var rowId in this.theData){

                    if (this.theData[rowId].classUri === classUri){
                            returnValue = rowId;
                            break;
                    }
            }

            return returnValue;
    };

    Switcher.prototype.nextStep = function(){
            if (this.currentIndex < this.theData.length){
                    if (this.decompile === true && this.theData[this.currentIndex].statusTag !== 'decompiled'){
                            this.decompileClass(this.theData[this.currentIndex].classUri);
                    }
                    else if (this.decompile === false && this.theData[this.currentIndex].statusTag !== 'compiled'){
                            this.compileClass(this.theData[this.currentIndex].classUri);   
                    }
            }
            else{
                    this.end();
            }
    };

    Switcher.prototype.compileClass = function(classUri){
            var __this = this;
            var rowId = this.getRowIdByUri(classUri);

            this.setCellData(rowId, 'status', __('Switching to Production...'));

            $.ajax({
                    type: "POST",
                    url: __this.getActionUrl('compileClass'),
                    data: {classUri : classUri, options: ''},
                    dataType: "json",
                    success: function(r){
                            __this.addResultData(rowId, r);

                            if (r.success){
                                   //update grid
                                    var selfCount = r.count;
                                    var relatedCount = 0;
                                    for(var relatedClassName in r.relatedClasses){
                                            relatedCount += parseInt(r.relatedClasses[relatedClassName]);
                                    }
                                    var count = ' (' + (selfCount + relatedCount) + ' ' + __('instances') + ')';
                                    __this.setCellData(rowId, 'status', __('Production') + count);
                                    __this.setCellData(rowId, 'statusTag', 'compiled');

                            }
                            else{
                                    __this.setCellData(rowId, 'status', __('Failed'));
                            }

                            __this.currentIndex ++;
                            __this.nextStep();
                    }
            });
    };

    Switcher.prototype.decompileClass = function(classUri){
            var __this = this;
            var rowId = this.getRowIdByUri(classUri);

            this.setCellData(rowId, 'status', __('Switching to Design...'));

            $.ajax({
                    type: "POST",
                    url: __this.getActionUrl('decompileClass'),
                    data: {classUri : classUri, options: ''},
                    dataType: "json",
                    success: function(r){
                    __this.addResultData(rowId, r);

                    if (r.success){
                            //update grid
                            var selfCount = r.count;
                            var relatedCount = 0;
                            for(var relatedClassName in r.relatedClasses){
                                    relatedCount += parseInt(r.relatedClasses[relatedClassName]);
                            }
                            var count = ' (' + (selfCount + relatedCount) + ' ' + __('instances') + ')';
                            __this.setCellData(rowId, 'status', __('Design') + count);
                            __this.setCellData(rowId, 'statusTag', 'decompiled');
                    }
                    else{
                            __this.setCellData(rowId, 'status', __('Failed'));
                    }

                    __this.currentIndex ++;
                    __this.nextStep();
                }
            });
    };

    Switcher.prototype.end = function(){

            var __this = this;
            if (this.options.beforeComplete){
                    this.options.beforeComplete(this);
            }

            if (__this.decompile){
                    if (__this.options.onCompleteDecompile){
                            __this.options.onCompleteDecompile(this);
                    }
            }
            else{
                  //send the ending request: index the properties:
                    $.ajax({
                            type: "POST",
                            url: __this.getActionUrl('createPropertyIndex'),
                            data: {},
                            dataType: "json",
                            success: function (r){
                                    if(__this.options.onComplete){
                                            __this.options.onComplete(this, r.success);
                                    }
                            }
                    });  
            }        
    };
    
    return Switcher;
});