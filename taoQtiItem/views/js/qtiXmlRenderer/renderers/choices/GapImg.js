define(['tpl!taoQtiItem/qtiXmlRenderer/tpl/choices/gapImg'], function(tpl){
    return {
        qtiClass : 'gapImg',
        template : tpl,
        getData:function(gapImg, data){
            data.renderedObject = gapImg.object.render(this);
            return data;
        }
    };
});
