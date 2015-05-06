define(['jquery', 'lodash', 'taoQtiItem/qtiCreator/helper/qtiElements'], function($, _, QtiElements){

    var CL = console.log;

    var allKeys = _.keys(QtiElements.classes);

    test('getParentClasses', function(){
        
        expect(0);return;
        
        var qtiElts = allKeys;//['gap', 'hottext', 'blockInteraction', 'inlineInteraction', 'choiceInteraction'];
        for(var i in qtiElts){
            var parents = QtiElements.getParentClasses(qtiElts[i], true);
            ok(_.isArray(parents), 'is array');
            ok(parents.length, 'not empty');
            CL('parents', qtiElts[i], parents);
        }

    });

    test('getChildClasses', function(){
        
        expect(0);return;
        
        var qtiElts = allKeys;//['block', 'inline'];
        for(var i in qtiElts){
            var qtiElt = qtiElts[i];
            var containables = QtiElements.getChildClasses(qtiElt, true, 'xhtml');
            ok(_.isArray(containables));
            ok(containables.length, qtiElt + ' has xhtml children');

            containables = QtiElements.getChildClasses(qtiElt, true, 'qti');
            ok(_.isArray(containables));
            ok(containables.length, qtiElt + ' has qti children');
        }

    });

    test('getAllowedContainers', function(){
        
//        var qtiElts = allKeys;//['img', 'gap', 'hottext', 'blockInteraction', 'inlineInteraction', 'rubricBlock', 'math', 'object'];
        var qtiElts = ['img', 'gap', 'hottext', 'blockInteraction', 'inlineInteraction', 'rubricBlock', 'math', 'object'];
        for(var i in qtiElts){
            var containables = QtiElements.getAllowedContainers(qtiElts[i]);
            ok(_.isArray(containables), 'is array');
            ok(containables.length, 'not empty');
            CL(qtiElts[i], 'can be contained in', containables);
        }

    });

    test('getAllowedContents', function(){

//        var qtiElts0 = allKeys;//['atomicBlock', 'itemBody', 'prompt', 'div', 'rubricBlock'];
        var qtiElts0 = ['atomicBlock', 'itemBody', 'prompt', 'div', 'rubricBlock', 'p'];
        for(var i in qtiElts0){
            var containables = QtiElements.getAllowedContents(qtiElts0[i]);
            ok(_.isArray(containables), 'is array');
            ok(containables.length, 'not empty');
            CL(qtiElts0[i], 'contains', containables);
        }
        
        return;
        
        var qtiElts1 =allKeys;// ['blockStatic', 'blockInteraction', 'inlineInteraction', 'math'];
        for(var i in qtiElts1){
            var containables = QtiElements.getAllowedContents(qtiElts1[i]);
            ok(_.isArray(containables), 'is array');
            ok(!containables.length, 'is empty');
            CL(qtiElts1[i], 'contains', containables);
        }

    });

    test('getAllowedContents recursive', function(){

        var qtiElts2 = allKeys;//['atomicBlock', 'itemBody', 'prompt', 'div', 'rubricBlock'];
        for(var i in qtiElts2){
            var containables = QtiElements.getAllowedContents(qtiElts2[i], false);
            ok(_.isArray(containables), 'is array');
            ok(containables.length, 'not empty');
            CL(qtiElts2[i], 'contains recursively', containables);
        }
    });

    test('isBlock/isInline', function(){
        
        expect(0);return;
        
        var blocks = allKeys;//['choiceInteraction', 'div', 'rubricBlock', 'math'];
        _.each(blocks, function(qtiClass){
            ok(QtiElements.isBlock(qtiClass), qtiClass + ' is a block');
        });

        var inlines = allKeys;//['inlineChoiceInteraction', 'img', 'math'];
        _.each(inlines, function(qtiClass){
            ok(QtiElements.isInline(qtiClass), qtiClass + ' is an inline');
        });
    });
    
    test('setup for ck dtd', function(){
        
        expect(0);return;
        
        var qtiElts = allKeys;//['block', 'inline'];
        for(var i in qtiElts){
            var qtiElt = qtiElts[i];
        	  if(!QtiElements.classes[qtiElt].xhtml) {
        	  	continue;
        	  }
            var containables = QtiElements.getChildClasses(qtiElt, false, 'xhtml');
        	  console.log(qtiElt, containables)
            ok(_.isArray(containables));
            ok(containables.length, qtiElt + ' has xhtml children');

//            containables = QtiElements.getChildClasses(qtiElt, true, 'qti');
//            ok(_.isArray(containables));
//            ok(containables.length, qtiElt + ' has qti children');
        }

    });
});


