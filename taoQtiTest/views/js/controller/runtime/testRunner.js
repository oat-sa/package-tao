define(['jquery', 'jqueryui', 'lodash', 'spin', 'serviceApi/ServiceApi', 'serviceApi/UserInfoService', 'serviceApi/StateStorage', 'iframeResizer', 'iframeNotifier', 'i18n', 'mathJax', 'jquery.trunc' ], 
    function($, $ui, _, Spinner, ServiceApi, UserInfoService, StateStorage, iframeResizer, iframeNotifier, __, MathJax){

	    var timerIds = [];
	    var currentTimes = [];
	    var lastDates = [];
		var timeDiffs = [];
		var waitingTime = 0;
	
	    var TestRunner = {
	    // Constants
	    'TEST_STATE_INITIAL': 0,
	    'TEST_STATE_INTERACTING': 1,
	    'TEST_STATE_MODAL_FEEDBACK': 2,
	    'TEST_STATE_SUSPENDED': 3,
	    'TEST_STATE_CLOSED': 4,
	    'TEST_NAVIGATION_LINEAR': 0,
	    'TEST_NAVIGATION_NONLINEAR': 1,
	    'TEST_ITEM_STATE_INTERACTING': 1,
	        
		beforeTransition : function(callback) {
		    // Ask the top window to start the loader. 
            iframeNotifier.parent('loading');
            
            // Disable buttons.
		    this.disableGui();
	
	        $('#qti-item, #qti-info, #qti-rubrics, #qti-timers').css('display', 'none');	        
	
	        // Wait at least waitingTime ms for a better user experience.
	        if(typeof callback === 'function'){
	            setTimeout(callback, waitingTime);
	        }
		},
		
		afterTransition : function() {
		    this.enableGui();
    	    
    	    //ask the top window to stop the loader 
    	    iframeNotifier.parent('unloading');
		},
	
		moveForward: function() {
		    this.disableGui();
		    
		    var that = this;
		    this.itemServiceApi.kill(function(signal) {
		        that.actionCall('moveForward');
		    });  
		},
	
		moveBackward : function() {
		    this.disableGui();
		    
		    var that = this;
		    this.itemServiceApi.kill(function(signal) {
                that.actionCall('moveBackward');
            });  
		},
	
		skip : function() {
		    this.disableGui();
		    
			this.actionCall('skip');
		},
		
		timeout: function() {
		    this.disableGui();
		    
			this.assessmentTestContext.isTimeout = true;
			this.updateTimer();
			this.actionCall('timeout');
		},
	
		comment : function() {
			$('#qti-comment > textarea').val(__('Your comment...'));
		    $('#qti-comment').css('display', 'block');
		    $('#qti-comment > button').css('display', 'inline');
		},
		
		closeComment : function() {
		    $('#qti-comment').css('display', 'none');
		},
		
		emptyComment : function() {
		    $('#qti-comment > textarea').val('');
		},
		
		storeComment: function() {
		    var self = this;
		    $.ajax({
		            url: self.assessmentTestContext.commentUrl,
		            cache: false,
		            async: true,
		            type: 'POST',
		            data: { comment: $('#qti-comment > textarea').val() },
		            success: function(assessmentTestContext, textStatus, jqXhr) {
		                self.closeComment();
		            }
		    });
		},
	
		update : function(assessmentTestContext) {
			var self = this;
			$('#qti-item').remove();
			
			var $runner = $('#runner');
			$runner.css('height', 'auto');
			
			this.assessmentTestContext = assessmentTestContext;
			this.itemServiceApi = eval(assessmentTestContext.itemServiceApiCall);
			
			this.updateContext();
			this.updateProgress();
			this.updateNavigation();
			this.updateInformation();
			this.updateRubrics();
			this.updateTools();
			this.updateTimer();
			
			$itemFrame = $('<iframe id="qti-item" frameborder="0"/>');
			$itemFrame.appendTo('#qti-content');
			iframeResizer.autoHeight($itemFrame, 'body');
			
			if (navigator.userAgent.match(/(iPad|iPhone|iPod)/g) ? true : false == true) {
			    $('#qti-content').css('overflow-y', 'scroll');
			}
			
			if (this.assessmentTestContext.itemSessionState === this.TEST_ITEM_STATE_INTERACTING && self.assessmentTestContext.isTimeout === false) {
			    $(document).on('serviceloaded', function() {
			        self.afterTransition();
                    self.adjustFrame();
                    $itemFrame.show();
			    });
			    
			    // Inject API into the frame.
			    this.itemServiceApi.loadInto($itemFrame[0], function(){
			        // We now rely on the 'serviceloaded' event.
			    });
			}
			else {
				// e.g. no more attempts or timeout! Simply consider the transition is finished,
				// but do not load the item.
				self.afterTransition();
			}
		},
	
		updateInformation : function() {
            $('#qti-info').remove();            
            
            if (this.assessmentTestContext.isTimeout === true) {
            	$('<div id="qti-info" class="info"></div>').prependTo('#qti-content');
            	$('#qti-info').html(__('Maximum time limit reached for item "%s".').replace('%s', this.assessmentTestContext.itemIdentifier));
            }
            else if (this.assessmentTestContext.itemSessionState !== this.TEST_ITEM_STATE_INTERACTING) {
            	$('<div id="qti-info" class="info"></div>').prependTo('#qti-content');
            	$('#qti-info').html(__('No more attempts allowed for item "%s".').replace('%s', this.assessmentTestContext.itemIdentifier));
            }
		},
		
		updateTools : function updateTools() {
		    if (this.assessmentTestContext.allowComment === true) {
	            $('#comment').css('display', 'inline');
		    } 
		    else {
	            $('#comment').css('display', 'none');
		    }
		    
		    if (this.assessmentTestContext.allowSkipping === true) {
		        if (this.assessmentTestContext.isLast === false) {
		            $('#skip').css('display', 'inline');
		            $('#skip-end').css('display', 'none');
		        }
		        else {
		            $('#skip-end').css('display', 'inline');
		            $('#skip').css('display', 'none');
		        }
		    }
		    else {
		    	$('#skip').css('display', 'none');
		    	$('#skip-end').css('display', 'none');
		    }
		},
		
		updateTimer : function() {
			var self = this;
			$('#qti-timers').remove();
			
			for (var i = 0; i < timerIds.length; i++) {
				clearTimeout(timerIds[i]);
			}
		    
		    timerIds = [];
		    currentTimes = [];
		    lastDates = [];
			timeDiffs = [];
			
			if (self.assessmentTestContext.isTimeout == false && self.assessmentTestContext.itemSessionState == self.TEST_ITEM_STATE_INTERACTING) {

			    if (this.assessmentTestContext.timeConstraints.length > 0) {
			
			    	// Insert QTI Timers container.
			    	$('<div id="qti-timers"></div>').prependTo('#qti-content');
			    	// self.formatTime(cst.seconds)
			        for (var i = 0; i < this.assessmentTestContext.timeConstraints.length; i++) {
			        	
			        	var cst = this.assessmentTestContext.timeConstraints[i];
			        	
			        	if (cst.allowLateSubmission == false) {
			        	 // Set up a timer for this constraint.
	                        $('<div class="qti-timer"><span class="icon-time"></span> ' + cst.source + ' - ' + self.formatTime(cst.seconds) + '</div>').appendTo('#qti-timers');
	                        
	                        // Set up a timer and update it with setInterval.
	                        currentTimes[i] = cst.seconds;
	                        lastDates[i] = new Date();
	                        timeDiffs[i] = 0;
	                        timerIndex = i;
	                        source = cst.source;
	                        
	                        // ~*~*~ ❙==[||||)0__    <----- SUPER CLOSURE !
	                        var superClosure = function(timerIndex, source) {
	                            timerIds[timerIndex] = setInterval(function() {
	                                
	                                timeDiffs[timerIndex] += (new Date()).getTime() - lastDates[timerIndex].getTime();
	                
	                                if (timeDiffs[timerIndex] >= 1000) {
	                                    var seconds = timeDiffs[timerIndex] / 1000;
	                                    currentTimes[timerIndex] -= seconds;
	                                    timeDiffs[timerIndex] = 0;
	                                }
	                
	                                if (currentTimes[timerIndex] <= 0) {
	                                    // The timer expired...
	                                    $('#qti-timers > .qti-timer').eq(timerIndex).html(self.formatTime(Math.round(currentTimes[timerIndex])));
	                                    currentTimes[timerIndex] = 0;
	                                    clearInterval(timerIds[timerIndex]);
	                                    
	                                     // Hide item to prevent any further interaction with the candidate.
                                        $('#qti-item').css('display', 'none');
                                        self.timeout();
	                                }
	                                else {
	                                    // Not timed-out...
	                                    $('#qti-timers > .qti-timer').eq(timerIndex).html('<span class="icon-time"></span> ' + source + ' - ' + self.formatTime(Math.round(currentTimes[timerIndex])));
	                                    lastDates[timerIndex] = new Date();
	                                }
	                
	                            }, 1000);
	                        }
	                        
	                        superClosure(timerIndex, source);    
			        	}
			        }
			        
			        $('#qti-timers').css('display', 'block');
			    }
			}
		},
	
		updateRubrics : function() {
		    $('#qti-rubrics').remove();
		
		    if (this.assessmentTestContext.rubrics.length > 0) {
                
                var $rubrics = $('<div id="qti-rubrics"></div>');

                for (var i = 0; i < this.assessmentTestContext.rubrics.length; i++) {
                        $rubrics.append(this.assessmentTestContext.rubrics[i]);
                }

                // modify the <a> tags in order to be sure it
                // opens in another window.
                $rubrics.find('a').bind('click keypress', function() {
                        window.open(this.href);
                        return false;
                });

                $rubrics.prependTo('#qti-content');

                if(MathJax){
                    MathJax.Hub.Queue(["Typeset", MathJax.Hub], $('#qti-rubrics')[0]);
                }
		            
		    }
		},
	
		updateNavigation: function() {
		    if (this.assessmentTestContext.navigationMode === this.TEST_NAVIGATION_LINEAR) {
		    	// LINEAR
	    		$('#move-backward').css('display', 'none');
	    		$('#move-forward').css('display', (this.assessmentTestContext.isLast === true) ? 'none' : 'inline');
	    		$('#move-end').css('display', (this.assessmentTestContext.isLast === true) ? 'inline' : 'none');
		    }
		    else {
		    	// NONLINEAR
		    	$('#qti-actions').css('display', 'block');
		    	$('#move-forward').css('display', (this.assessmentTestContext.isLast === true) ? 'none' : 'inline');
		    	$('#move-end').css('display', (this.assessmentTestContext.isLast === true) ? 'inline' : 'none');
		    	$('#move-backward').css('display', (this.assessmentTestContext.canMoveBackward === true) ? 'inline' : 'none');
		    }
		},
		
		updateProgress: function() {
		    
		    var considerProgress = this.assessmentTestContext.considerProgress;
		    
		    $('#qti-test-progress').css('visibility', (considerProgress === true) ? 'visible' : 'hidden');
		    
		    if (considerProgress === true) {
		        var ratio = Math.floor(this.assessmentTestContext['numberCompleted'] / this.assessmentTestContext['numberItems'] * 100);
	            var label = __('Test completed at %d%%').replace('%d', ratio).replace('%%', '%');
	            $('#qti-progress-label').text(label);
	            $('#qti-progressbar').progressbar({
	                value: ratio
	            });
		    }
		},
		
		updateContext: function() {
		    
		    var testTitle = this.assessmentTestContext.testTitle;
		    var testPartId = this.assessmentTestContext.testPartId;
		    var sectionTitle = this.assessmentTestContext.sectionTitle;
		    
		    $('#qti-test-title').text(testTitle);
		    
		    try {
		        $('#qti-test-title, #qti-test-position').badonkatrunc('destroy');
		    }
		    catch (e) {
		        // Very first call, the badonkatrunc wrapper was not there.
		        // Continue normally.
		    }
		    
		    $('#qti-test-position').empty().append('<span id="qti-section-title">' + sectionTitle + '</span>');
		    $('#qti-test-title, #qti-test-position').badonkatrunc().css('visibility', 'visible');
		},
		
		adjustFrame: function() {
		    
		    var actionsHeight = $('#qti-actions').outerHeight();
		    var windowHeight = window.innerHeight ? window.innerHeight : $(window).height();
		    var navigationHeight = $('#qti-navigation').outerHeight();
		    var newContentHeight = windowHeight - actionsHeight - navigationHeight;
		    
		    var $content = $('#qti-content');
		    $content.height(newContentHeight - parseInt($content.css('paddingTop')) - parseInt($content.css('paddingBottom')));
		},
		
		disableGui: function() {
		    $('#qti-navigation button').addClass('disabled');
		},
		
		enableGui: function() {
		    $('#qti-navigation button').removeClass('disabled');
		},
	
		formatTime: function(totalSeconds) {
		    var sec_num = totalSeconds;
		    var hours   = Math.floor(sec_num / 3600);
		    var minutes = Math    .floor((sec_num - (hours * 3600)) / 60);
		    var seconds = sec_num - (hours * 3600) - (minutes * 60);
		
		    if (hours   < 10) {hours   = "0" + hours;}
		    if (minutes < 10) {minutes = "0" + minutes;}
		    if (seconds < 10) {seconds = "0" + seconds;}
		
		    var time    = hours + ':' + minutes + ':' + seconds;
		
		    return "\u00b1 " + time;
		},
		
		actionCall: function(action) {
			var self = this;
			this.beforeTransition(function() {
				$.ajax({
					url: self.assessmentTestContext[action + 'Url'],
					cache: false,
					async: true,
					dataType: 'json',
					success: function(assessmentTestContext, textStatus, jqXhr) {
						if (assessmentTestContext.state === self.TEST_STATE_CLOSED) {
							self.serviceApi.finish();
						}
						else {
							self.update(assessmentTestContext);
						}
					}
				});
			});
		}
	};

	return {
	    start : function(assessmentTestContext){
	    	window.onServiceApiReady = function onServiceApiReady(serviceApi) {
	            TestRunner.serviceApi = serviceApi;
	
	           // If the assessment test session is in CLOSED state,
	           // we give the control to the delivery engine by calling
	           // finish.
	           if (assessmentTestContext.state === TestRunner.TEST_STATE_CLOSED) {
                   serviceApi.finish();
	           }
	           else {
                   TestRunner.update(assessmentTestContext);
	           }
	        };
	    	
	        TestRunner.beforeTransition();
	        TestRunner.assessmentTestContext = assessmentTestContext;
	
	        $('#skip, #skip-end').click(function(){
	            if (!$(this).hasClass('disabled')) {
	                TestRunner.skip();
	            }
	        });
	        
	        $('#move-forward, #move-end').click(function(){
	            if (!$(this).hasClass('disabled')) {
	                TestRunner.moveForward();
	            }
	        });
	        
	        $('#move-backward').click(function(){
	            if (!$(this).hasClass('disabled')) {
	                TestRunner.moveBackward();
	            }
	        });
	        
	        $('#comment').click(function(){
	            if (!$(this).hasClass('disabled')) {
	                TestRunner.comment();
	            }
	        });
	        
	        $('#qti-comment-cancel').click(function(){
	                TestRunner.closeComment();
	        });
	        
	        $('#qti-comment-send').click(function(){
	            TestRunner.storeComment();
	        });
	        
	        $('#qti-comment > textarea').click(function(){
	            TestRunner.emptyComment();
	        });
	        
	        $(window).bind('resize', function() {
	            TestRunner.adjustFrame();
	            $('#qti-test-title, #qti-test-position').badonkatrunc();
	        });
	
	        iframeNotifier.parent('serviceready');
	        
	        $(document).bind('loading', function() {
	            iframeNotifier.parent('loading');
	        });
	        
	        $(document).bind('unloading', function() {
	            iframeNotifier.parent('unloading');
	        });
	    }
	};
});