switcherClass.instances = [];

//create a grid, and update it after each hardification:
function switcherClass(tableElementId, userOptions){
        
        this.options = {
                onStart:function(){},
                onStartDecompile:function(){},
                onStartEmpty:function(){},
                beforeComplete:function(){},
                onComplete:function(){},
                onCompleteDecompile:function(){}
        };
        
        if(userOptions){
                this.options = $.extend(this.options, userOptions);
        }
        
        this.$grid = $('#'+tableElementId);
        this.theData = [];
        this.currentIndex = -1;
        this.forcedStart = false;
        this.decompile = false;
        
        switcherClass.instances[tableElementId] = this;
}

switcherClass.prototype.getActionUrl = function(action){
        var url = root_url;
        if(typeof(ctx_extension) != 'undefined'){
                url += '/' + ctx_extension + '/Settings/' + action;
        }else{
                url += '/tao/Settings/' + action;
        }
        
        return url;
}

switcherClass.prototype.init = function(forcedMode, decompile){
        
        var __this = this;
        var actionUrl = __this.getActionUrl('optimizeClasses');
        this.forcedStart = false;
        if(forcedMode){
                //check if there is already a compilation running:
                for(i in this.theData){
                        if(this.theData[i].status == __('compiling') || this.theData[i].status == __('decompiling')){
                                return false;
                        }
                }
                
                this.forcedStart = true;
        }else{
                //check if already initialized:
                if(this.theData.length){
                        return false;
                }
        }
        
        if(decompile){
                this.decompile = true;
                actionUrl = this.getActionUrl('decompileClasses');
        }else{
                this.decompile = false;//reset the decompile value to "false"
        }
        
        $.ajax({
                type: "POST",
                url: actionUrl,
                data: {},
                dataType: 'json',
                success: function(r){
                        
                        if(r.length == 0){
                                if(__this.options.onStartEmpty) __this.options.onStartEmpty(__this);
                                return false;
                        }
                        
                        var gridOptions = {
                                datatype: "local", 
                    			hidegrid : false,
                                colNames: [ __('Classes'), __('Status'), __('Action')], 
                                colModel: [ 
                                        {name:'class',index:'class',width:200},
                                        {name:'status',index:'status', align:"center",width:300}, 
                                        {name:'actions',index:'actions', align:"center",width:150,sortable: false}
                                ], 
                                rowNum:15, 
                                height: 'auto', 
                                autowidth: true,
                                width:(parseInt(__this.$grid.width()) - 2),
                                sortname: 'status', 
                                viewrecords: false, 
                                sortorder: "asc", 
                                caption: __("Optimizable Classes"),
                                subGrid: true,
                                subGridOptions:{
                                        plusicon: "ui-icon-triangle-1-e",
                                        minusicon: "ui-icon-triangle-1-s",
                                        openicon: "ui-icon-arrowreturn-1-e"
                                },
                                subGridModel:[
                                        {
                                                name: [__('related classes'), __('compiled instances')],
                                                width:[200, 200],
                                                align: ['left', 'center']
                                        }
                                ],
                                subGridRowExpanded: function(subgrid_id, row_id) {
                                        
                                        if(__this.theData[row_id].compilationResults != undefined){
                                                
                                                var localData = __this.theData[row_id].compilationResults.relatedClasses;
                                                
                                                var count = 0;
                                                for(val in localData){
                                                        count++;
                                                        break;
                                                }
                                                if(count==0) return false;
                                                
                                                var colNames = [__('Related Classes')];
                                                if(__this.decompile) {
                                                        colNames.push(__('Decompiled Instances'));
                                                }else{
                                                        colNames.push(__('Compiled Instances'));
                                                }
                                                var subgrid_table_id;
                                                subgrid_table_id = subgrid_id+"_t";
                                                $("#"+subgrid_id).html("<table id='"+subgrid_table_id+"' class='scroll'></table>");
                                                var $subGrid = $("#"+subgrid_table_id).jqGrid({
                                                        datatype: "local",
                                                        colNames: colNames,
                                                        colModel: [
                                                                {
                                                                        name:"class",
                                                                        index:"class",
                                                                        width:200,
                                                                        key:true
                                                                },
                                                                {
                                                                        name:"count",
                                                                        index:"count",
                                                                        align:"center",
                                                                        width:150
                                                                }
                                                        ],
                                                        width:350,
                                                        height: '100%',
                                                        rowNum:20,
                                                        sortname: 'class',
                                                        sortorder: 'asc'
                                                });

                                                var i=0;
                                                for(className in localData){
                                                        $subGrid.jqGrid('addRowData', i, {'class':className, 'count':localData[className]});
                                                        i++;
                                                }
                                                
                                                
                                                
                                                return true;
                                        }else{
                                                return false;
                                        }
                                }
                        };
                        
                        //buil jqGrid:
                        __this.$grid.jqGrid(gridOptions);
                        
                        //insert rows:
			for(var j=0; j<r.length; j++){
				__this.setRowData(j, r[j]);
                                if(__this.forcedStart || __this.decompile){
                                       __this.currentIndex = 0;  
                                }else{
                                      if(__this.currentIndex < 0 && __this.theData[j].status != __('compiled')){
                                              __this.currentIndex = j;
                                      }  
                                }
			}
                        
                        //start compilation:
                        if(__this.decompile){
                                __this.startDecompilation();
                        }else{
                                __this.startCompilation();
                        }

                }
        });
        
        return true;
}

switcherClass.prototype.startCompilation = function(){
        if(this.options.onStart){
                this.options.onStart(this);
        }
        this.$grid.hideCol('subgrid');
        if(this.currentIndex >= 0){
                this.nextStep();
        }
}

switcherClass.prototype.startDecompilation = function(){
        if(this.options.onStartDecompile){
                this.options.onStartDecompile(this);
        }
        this.$grid.hideCol('subgrid');
        if(this.currentIndex >= 0){
                this.nextStep();
        }
}

switcherClass.prototype.addRowData = function(rowId, data){
        this.$grid.jqGrid('addRowData', rowId, data);
        this.theData[rowId] = data;
}

switcherClass.prototype.setRowData = function(rowId, data){
       
        if(typeof(this.theData[rowId]) != 'undefined'){
                this.$grid.jqGrid('setRowData', rowId, data);
        }else{
                this.$grid.jqGrid('addRowData', rowId, data);
        }
        this.theData[rowId] = data;
}

switcherClass.prototype.setCellData = function(rowId, colName, data){
        this.$grid.jqGrid('setCell', rowId, colName, data);
        this.theData[rowId][colName] = data;
}

switcherClass.prototype.addResultData = function(rowId, data){
        this.theData[rowId].compilationResults = null;
        this.theData[rowId].compilationResults = data;
}

switcherClass.prototype.getRowIdByUri = function(classUri){
        var returnValue = -1;
        
        for(rowId in this.theData){
                
                if(this.theData[rowId].classUri == classUri){
                        returnValue = rowId;
                        break;
                }
        }
        
        return returnValue;
}

switcherClass.prototype.nextStep = function(){
        if(this.currentIndex < this.theData.length){
                if(this.decompile){
                        this.decompileClass(this.theData[this.currentIndex].classUri);
                }else{
                        this.compileClass(this.theData[this.currentIndex].classUri);   
                }
	}else{
		this.end();
	}
}

switcherClass.prototype.compileClass = function(classUri){
        var __this = this;
        var rowId = this.getRowIdByUri(classUri);
        
        this.setCellData(rowId, 'status', __('compiling'));
        
        $.ajax({
		type: "POST",
		url: __this.getActionUrl('compileClass'),
		data: {classUri : classUri, options: ''},
		dataType: "json",
		success: function(r){
                        __this.addResultData(rowId, r);
                        
                        if(r.success){
                               //update grid
                                var selfCount = r.count;
                                var relatedCount = 0;
                                for(relatedClassName in r.relatedClasses){
                                        relatedCount += parseInt(r.relatedClasses[relatedClassName]);
                                }
                                var count = ' (' + eval(selfCount+relatedCount) + ' ' + __('instances') + ': '+selfCount+' self / '+relatedCount+' related)';
                                __this.setCellData(rowId, 'status', __('compiled') + count);
                                
                                if(relatedCount){
                                        //enable subgrid
                                        __this.$grid.showCol('subgrid');
                                }
                        }else{
                                __this.setCellData(rowId, 'status', __('failed'));
                        }
                        
                        __this.currentIndex ++;
                        __this.nextStep();
                }
        });
}

switcherClass.prototype.decompileClass = function(classUri){
        var __this = this;
        var rowId = this.getRowIdByUri(classUri);
        
        this.setCellData(rowId, 'status', __('decompiling'));
        
        $.ajax({
		type: "POST",
		url: __this.getActionUrl('decompileClass'),
		data: {classUri : classUri, options: ''},
		dataType: "json",
		success: function(r){
                        __this.addResultData(rowId, r);
                        
                        if(r.success){
                                //update grid
                                var selfCount = r.count;
                                var relatedCount = 0;
                                for(relatedClassName in r.relatedClasses){
                                        relatedCount += parseInt(r.relatedClasses[relatedClassName]);
                                }
                                var count = ' (' + eval(selfCount+relatedCount) + ' ' + __('instances') + ': '+selfCount+' self / '+relatedCount+' related)';
                                __this.setCellData(rowId, 'status', __('decompiled') + count);
                                if(selfCount){
                                        //enable subgrid
                                        __this.$grid.showCol('subgrid');
                                }
                        }else{
                                __this.setCellData(rowId, 'status', __('failed'));
                        }
                        
                        __this.currentIndex ++;
                        __this.nextStep();
                }
        });
}

switcherClass.prototype.end = function(){
        
        var __this = this;
        if(this.options.beforeComplete){
                this.options.beforeComplete(this);
        }
        
        if(__this.decompile){
                if(__this.options.onCompleteDecompile){
                        __this.options.onCompleteDecompile(this);
                }
        }else{
              //send the ending request: index the properties:
                $.ajax({
                        type: "POST",
                        url: __this.getActionUrl('createPropertyIndex'),
                        data: {},
                        dataType: "json",
                        success: function(r){
                                if(__this.options.onComplete){
                                        __this.options.onComplete(this, r.success);
                                }
                        }
                });  
        }        
        
        
}
