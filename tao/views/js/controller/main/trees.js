define(['jquery', 'lodash', 'helpers', 'generis.tree.browser', 'generis.actions', 'module'], 
function($, _, helpers, GenerisTreeBrowserClass, generisActions, module){
    return {
        start : function(){
            
            var sectionTreesData = module.config().sectionTreesData;
            if(sectionTreesData){
                for(var treeId in sectionTreesData){
                    var options = _.defaults(sectionTreesData[treeId],{
                        formContainer: helpers.getMainContainerSelector(),
                        actionId: treeId,
                        paginate: 30
                    });
                    var tree = new GenerisTreeBrowserClass('#tree-' + treeId, options.dataUrl, options);
                    generisActions.setMainTree(tree);
                }
            }
        }
    };
});


