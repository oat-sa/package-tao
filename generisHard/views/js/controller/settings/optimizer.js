define(['jquery', 'i18n', 'generisHard/Switcher'], function($, __, Switcher){
    return {
        start: function(){
            var $compilationGrid =  $('#compilation-grid-container');
            var $compilationResutlts = $('#compilation-grid-results');
            var $compileButton = $('#compileButton');
            var $decompileButton = $('#decompileButton');
            
            var options = {
                    onStart:function(){
                        $compilationGrid.show();
                    },
                    onStartEmpty:function(){
                        $compilationResutlts.html(__('There are no classes available for optimization.')).show();
                    },
                    onStartDecompile:function(){
                        $compilationGrid.show();
                    },
                    beforeComplete: function(){
                        $compilationResutlts.html(__('Rebuilding indexes, it may take a while.')).show();
                    },
                    onComplete:function(aSwitcher, success){
                            if(success){
                                $compilationResutlts.html(__('Switch to Production Mode completed.')).show();
                            } else{
                                $compilationResutlts.html(__('Cannot successfully build the optimized table indexes')).show();
                            }
                    },
                    onCompleteDecompile:function(){
                        $compilationResutlts.html(__('Switch to Design Mode completed')).show();
                        $compileButton.show();
                        $decompileButton.show();
                    }
            };

            var mySwitcher = new Switcher('compilation-grid', options);
            mySwitcher.init();

            $compileButton.click(function(){
                if(confirm(__('All classes in Design Mode will switch to Production Mode. Please confirm.'))){
                    mySwitcher.startCompilation();
                }
            });

            $decompileButton.click(function(){
                if(confirm(__('All classes in Production Mode will switch to Design Mode. Please confirm.'))){
                    mySwitcher.startDecompilation();
                    $compilationResutlts.hide();
                }
            });
        }
    };
});


