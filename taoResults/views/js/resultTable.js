define(['jquery', 'i18n', 'helpers'], function($, __, helpers){
    
    var resultTable = {

        /**
         * 
         * implements the grid display for the resultTable
         * Note that it relies on document attributes like document.dataUrl set up by the php _url in the tpl 
         * If the _url becomes available in javascript we shall change this.
         */
        /**
         * Initiate or refresh the '#result-table-grid grid with data from _url('data') and using the columns document.columns/document.models current selection of data
         */
        showGrid : function () {
            $('#result-table-grid').jqGrid('GridUnload');
            var myGrid = $("#result-table-grid").jqGrid({
                url: document.dataUrl+'?filterData=lastSubmitted',
                postData: {'filter': document.JsonFilter, 'columns':document.columns},
                mtype: "post",
                datatype: "json",
                colNames: document.columns.values,
                colModel: document.models,
                rowNum:20,
                height:'auto',
                width: (parseInt($("#result-table-grid").width())-20),
                rowNum:20,
                rowList:[5,10,30],
                pager: '#pagera1',
                sortName: 'id',
                viewrecords: true,
                rownumbers: true,
                sortable: false,
                gridview : true,
                caption: __("Delivery results"),
                onCellSelect: function(rowid,iCol,cellcontent, e) {helpers.openTab(__('Delivery Result'), document.getActionViewResultUrl+'?uri='+escape(rowid));}
            });
            jQuery("#result-table-grid").jqGrid('navGrid','#pagera1', {edit:false,add:false,del:false,search:false,refresh:false});
            //jQuery("#result-table-grid").jqGrid('navButtonAdd',"#pagera1", {caption:"Column chooser",title:"Column chooser", buttonicon :'ui-icon-gear',onClickButton:function(){columnChooser();}});
            //jQuery("#result-table-grid").jqGrid('filterToolbar');
        },

        /**
         * Initiate the grid and shows it using a startup document.column and a document.models containing the ResultOfSubject property
         */
       initiateGrid : function (){
           var self = this;
            $.getJSON(document.getActionSubjectColumnUrl, document.JsonFilter, function (data) {self.setColumns(data.columns)});
        },

         /*
        * Triggers the jquery jqGrid functionnality allowing to select columns to be displayed
        */
        columnChooser : function (){
            $("#result-table-grid").jqGrid('columnChooser', {"title": "Select column"});
        },

        /**
         * Update document.columns/document.models current selection of data with columns
         */
        setColumns : function (columns) {
            var self = this;
            //columns = identifyColumns(columns);
            //console.log(columns);
            for (var key in columns) {
                    //used for the inner function set in the formatter callback, do not remvoe, the value gets computed at callback time
                     var currentColumn = columns[key];
                     document.columns.push(columns[key]);
                     columns[key].cellattr = function(rowId, tv, rawObject, cm, rdata){return "data-uri=void, data-type=void";};
                     columns[key].formatter = function(value, options, rData){return self.layoutData(value,currentColumn); };
                     document.models.push(columns[key]);
             }
             this.showGrid();
        },

        
        /**
         * Update document.columns/document.models current selection of data with columns
         */
        removeColumns : function (columns) {

                //loops on the columns parameter and find out if document.columns contains this column already.
                for (var key in columns) {
                        for (var dockey in document.columns) {
                                if (
                                        (document.columns[dockey].contextId == columns[key].contextId)
                                        &
                                        (document.columns[dockey].variableIdentifier == columns[key].variableIdentifier)
                                    )
                                    {
                                     document.columns.splice(dockey,1);
                                    }


                                for (var modelkey in document.models) {
                                if (
                                        (document.models[modelkey].contextId == columns[key].contextId)
                                        &
                                        (document.models[modelkey].variableIdentifier == columns[key].variableIdentifier)
                                    )
                                    {
                                     document.models.splice(modelkey,1);
                                    }
                                }

                        }
               }
               this.showGrid();
        },

        /**
         * Bind the click event to the function function and toggle the buttons class between fromButton toButton
         * @param {type} buttonID
         */
        bindTogglingButtons : function(fromButton, tobuttonID, actionCallUrl, operationType){
            var self = this;
            $(fromButton).click(function(e) {
                e.preventDefault();
                if ($(fromButton).hasClass("ui-state-default")) {
                $(fromButton).addClass("ui-state-disabled").removeClass("ui-state-default");
                $(tobuttonID).addClass("ui-state-default").removeClass("ui-state-disabled");
                $.getJSON( actionCallUrl
                        , document.JsonFilterSelection
                        , function (data) {
                            switch (operationType){
                            case "remove":self.removeColumns(data.columns);break;
                            case "add":self.setColumns(data.columns);break;
                            default:self.removeColumns(data.columns);break;
                            }

                        }
                );
                }
            });
        },

        /**
        * Transforms plain data into html data based on the property or based on the type of data sent out by the server (loose)
        */
        layoutData : function (data, column){
            //Simple Properties 
            if (column.type == "tao_models_classes_table_PropertyColumn"){
                switch (column.prop){
                    case document.resultOfSubjectConstant: {return  "<span class=highlight>"+data+"</span>";}
                    default:return data;
                    }
                }
                //Grade properties
                else if ((column.type == "taoResults_models_classes_table_GradeColumn")){
                return  this.layoutResponseArray(data);
                }
                //Actual responses properties
                else if ((column.type == "taoResults_models_classes_table_ResponseColumn")){
                return this.layoutResponseArray(data);
            }
        },


            /***
             *
        Deprecated
             *
             */
            /**
             * This functions transforms the data  from a response variable and add basic formatting tags.
             * It should be refactored using some explicit information about the data types found in such variables
             * @param {string} data
             * @returns {string}
             */
            layoutResponse : function(data){
                var formattedData = "";
                //the data may be not valid json, in this case there is a silent fail and the data is returned.
                try{
                var jsData = $.parseJSON(data);
                if (jsData instanceof Array) {
                    formattedData = '<OL >';
                    for (var key in jsData){
                        formattedData += '<li >';
                        formattedData += jsData[key];
                         formattedData += "</li>";
                        }
                     formattedData += "</OL>";
                } else {
                    formattedData = data;
                    }
                }
                catch(err){formattedData = data;}
                return formattedData;
            },

            //Multiple Entries with epoch
            layoutResponseArray : function(data){
                //console.log(data);
                return data;
               var formattedData = "";

               if (data.length> 0){
                   for (var key in data) {
                           if (data[key].length==2){ //[value, epoch]
                           var observedData = data[key][0];
                           var observedDataEpoch = data[key][1];
                           var $timeAffix = "";
                           if (observedDataEpoch!= '') {
                               $timeAffix ="<span class=epoch>("+observedDataEpoch+")</span>";
                           }
                           formattedData +="<div>"+this.layoutResponse(observedData)+ " "+$timeAffix+"</div>";
                           }
                           if (data[key].length==1){
                           observedData = data[key][0];
                           formattedData +="<div>"+this.layoutResponse(observedData)+"</div>";
                           }
                   }
               }
               //alert('0');
               return formattedData;
	}
    };
    
    return resultTable;
});