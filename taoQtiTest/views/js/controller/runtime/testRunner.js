/*
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 */

define([
    'jquery',
    'lodash',
    'i18n',
    'module',
    'taoQtiTest/testRunner/actionBarTools',
    'taoQtiTest/testRunner/testReview',
    'taoQtiTest/testRunner/progressUpdater',
    'taoQtiTest/testRunner/testMetaData',
    'serviceApi/ServiceApi',
    'serviceApi/UserInfoService',
    'serviceApi/StateStorage',
    'iframeNotifier',
    'mathJax',
    'ui/feedback',
    'ui/deleter',
    'moment',
    'ui/modal',
    'ui/progressbar'
],
function (
    $,
    _,
    __,
    module,
    actionBarTools,
    testReview,
    progressUpdater,
    testMetaDataFactory,
    ServiceApi,
    UserInfoService,
    StateStorage,
    iframeNotifier,
    MathJax,
    feedback,
    deleter,
    moment,
    modal
) {

    'use strict';

    var timerIds = [],
        currentTimes = [],
        lastDates = [],
        timeDiffs = [],
        waitingTime = 0,
        $timers,
        $controls,
        timerIndex,
        testMetaData,
        sessionStateService,
        $doc = $(document),
        optionNextSection = 'x-tao-option-nextSection',
        optionNextSectionWarning = 'x-tao-option-nextSectionWarning',
        optionReviewScreen = 'x-tao-option-reviewScreen',
        TestRunner = {
            // Constants
            'TEST_STATE_INITIAL': 0,
            'TEST_STATE_INTERACTING': 1,
            'TEST_STATE_MODAL_FEEDBACK': 2,
            'TEST_STATE_SUSPENDED': 3,
            'TEST_STATE_CLOSED': 4,
            'TEST_NAVIGATION_LINEAR': 0,
            'TEST_NAVIGATION_NONLINEAR': 1,
            'TEST_ITEM_STATE_INTERACTING': 1,

            /**
             * Prepare a transition to another item
             * @param {Function} [callback]
             */
            beforeTransition: function (callback) {
                // Ask the top window to start the loader.
                iframeNotifier.parent('loading');

                // Disable buttons.
                this.disableGui();

                $controls.$itemFrame.hide();
                $controls.$rubricBlocks.hide();
                $controls.$timerWrapper.hide();

                // Wait at least waitingTime ms for a better user experience.
                if (typeof callback === 'function') {
                    setTimeout(callback, waitingTime);
                }
            },

            /**
             * Complete a transition to another item
             */
            afterTransition: function () {
                this.enableGui();

                //ask the top window to stop the loader
                iframeNotifier.parent('unloading');
                testMetaData.addData({
                    'ITEM' : {'ITEM_START_TIME_CLIENT' : Date.now() / 1000}
                });
            },

            /**
             * Jumps to a particular item in the test
             * @param {Number} position The position of the item within the test
             */
            jump: function(position) {
                var self = this,
                    action = 'jump',
                    params = {position: position};
                this.disableGui();

                if( this.isJumpOutOfSection(position)  && this.isCurrentItemActive() && this.isTimedSection() ){
                    this.exitTimedSection(action, params);
                } else {
                    this.killItemSession(function() {
                        self.actionCall(action, params);
                    });
                }
            },

            /**
             * Push to server how long user seen that item before to track duration
             * @param {Number} duration
             */
            keepItemTimed: function(duration){
                if (duration) {
                    var self = this,
                        action = 'keepItemTimed',
                        params = {duration: duration};
                    self.actionCall(action, params);
                }
            },

            /**
             * Marks an item for later review
             * @param {Boolean} flag The state of the flag
             * @param {Number} position The position of the item within the test
             */
            markForReview: function(flag, position) {
                var self = this;

                // Ask the top window to start the loader.
                iframeNotifier.parent('loading');

                // Disable buttons.
                this.disableGui();

                $.ajax({
                    url: self.testContext.markForReviewUrl,
                    cache: false,
                    async: true,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        flag: flag,
                        position: position
                    },
                    success: function(data) {
                        // update the item flagged state
                        if (self.testReview) {
                            self.testReview.setItemFlag(position, flag);
                            self.testReview.updateNumberFlagged(self.testContext, position, flag);
                            if (self.testContext.itemPosition === position) {
                                self.testContext.itemFlagged = flag;
                            }
                            self.updateTools(self.testContext);
                        }

                        // Enable buttons.
                        self.enableGui();

                        //ask the top window to stop the loader
                        iframeNotifier.parent('unloading');
                    }
                });
            },

            /**
             * Move to the next available item
             */
            moveForward: function () {
                var self = this,
                    action = 'moveForward';

                this.disableGui();

                if( (( this.testContext.numberItemsSection - this.testContext.itemPositionSection - 1) == 0) && this.isCurrentItemActive()){
                    if( this.isTimedSection() && !this.testContext.isTimeout){
                        this.exitTimedSection(action);
                    } else {
                        this.exitSection(action);
                    }
                } else {
                    this.killItemSession(function () {
                        self.actionCall(action);
                    });
                }
            },

            /**
             * Move to the previous available item
             */
            moveBackward: function () {
                var self = this,
                    action = 'moveBackward';

                this.disableGui();

                if( (this.testContext.itemPositionSection == 0) && this.isCurrentItemActive() && this.isTimedSection() ){
                    this.exitTimedSection(action);
                } else {
                this.killItemSession(function () {
                        self.actionCall(action);
                    });
                }
            },

            /**
             * Checks if a position is out of the current section
             * @param {Number} jumpPosition
             * @returns {Boolean}
             */
            isJumpOutOfSection: function(jumpPosition){
                var items = this.getCurrentSectionItems(),
                    isJumpToOtherSection = true,
                    isValidPosition = (jumpPosition >= 0) && ( jumpPosition < this.testContext.numberItems );

                if( isValidPosition){
                    for(var i in items ) {
                        if (!items.hasOwnProperty(i)) {
                            continue;
                        }
                        if( items[i].position == jumpPosition ){
                            isJumpToOtherSection = false;
                            break;
                        }
                    }
                } else {
                    isJumpToOtherSection = false;
                }

                return isJumpToOtherSection;
            },

            /**
             * Exit from the current section. Set the exit code.de
             * @param {String} action
             * @param {Object} params
             * @param {Number} [exitCode]
             */
            exitSection: function(action, params, exitCode){
                var self = this;
                testMetaData.addData({"SECTION" : {"SECTION_EXIT_CODE" : exitCode || testMetaData.SECTION_EXIT_CODE.COMPLETED_NORMALLY}});
                self.killItemSession(function () {
                    self.actionCall(action, params);
                });
            },

            /**
             * Tries to exit a timed section. Display a confirm message.
             * @param {String} action
             * @param {Object} params
             */
            exitTimedSection: function(action, params){
                var self = this;
                var qtiRunner = this.getQtiRunner();

                if (qtiRunner) {
                    qtiRunner.updateItemApi();
                }

                this.displayExitMessage(
                    __('After you complete the section it would be impossible to return to this section to make changes. Are you sure you want to end the section?'),
                    function() {
                        self.exitSection(action, params);
                    },
                    'testSection'
                );

                this.enableGui();
            },

            /**
             * Tries to leave the current section and go to the next
             */
            nextSection: function(){
                var self = this;
                var qtiRunner = this.getQtiRunner();
                var doNextSection = function() {
                    self.exitSection('nextSection', null, testMetaData.SECTION_EXIT_CODE.QUIT);
                };

                if (qtiRunner) {
                    qtiRunner.updateItemApi();
                }

                if (this.hasOption(optionNextSectionWarning)) {
                    this.displayExitMessage(
                        __('After you complete the section it would be impossible to return to this section to make changes. Are you sure you want to end the section?'),
                        doNextSection,
                        'testSection'
                    );
                } else {
                    doNextSection();
                }

                this.enableGui();
            },

            /**
             * Gets the current progression within a particular scope
             * @param {String} [scope]
             * @returns {Object}
             */
            getProgression: function(scope) {
                var scopeSuffixMap = {
                    test : '',
                    testPart : 'Part',
                    testSection : 'Section'
                };
                var scopeSuffix = scope && scopeSuffixMap[scope] || '';

                return {
                    total : this.testContext['numberItems' + scopeSuffix] || 0,
                    answered : this.testContext['numberCompleted' + scopeSuffix] || 0,
                    viewed : this.testContext['numberPresented' + scopeSuffix] || 0,
                    flagged : this.testContext['numberFlagged' + scopeSuffix] || 0
                };
            },

            /**
             * Displays an exit message for a particular scope
             * @param {String} message
             * @param {Function} [action]
             * @param {String} [scope]
             * @returns {jQuery} Returns the message box
             */
            displayExitMessage: function(message, action, scope) {
                var self = this;
                var $confirmBox = $('.exit-modal-feedback');
                var progression = this.getProgression(scope);
                var unansweredCount = (progression.total - progression.answered);
                var flaggedCount = progression.flagged;

                if (unansweredCount && this.isCurrentItemAnswered()) {
                    unansweredCount--;
                }

                if (flaggedCount && unansweredCount) {
                    message = __('You have %s unanswered question(s) and have %s item(s) marked for review.',
                        unansweredCount.toString(),
                        flaggedCount.toString()
                    ) + ' ' + message;
                } else {
                    if (flaggedCount) {
                        message = __('You have %s item(s) marked for review.', flaggedCount.toString()) + ' ' + message;
                    }

                    if (unansweredCount) {
                        message = __('You have %s unanswered question(s).', unansweredCount.toString()) + ' ' + message;
                    }
                }

                $confirmBox.find('.message').html(message);
                $confirmBox.modal({ width: 500 });

                $confirmBox.find('.js-exit-cancel, .modal-close').off('click').on('click', function () {
                    $confirmBox.modal('close');
                });

                $confirmBox.find('.js-exit-confirm').off('click').on('click', function () {
                    $confirmBox.modal('close');
                    if (_.isFunction(action)) {
                        action.call(self);
                    }
                });

                return $confirmBox;
            },

            /**
             * Kill current item section and execute callback function given as first parameter.
             * Item end execution time will be stored in metadata object to be sent to the server.
             * @param {function} callback
             */
            killItemSession : function (callback) {
                testMetaData.addData({
                    'ITEM' : {
                        'ITEM_END_TIME_CLIENT' : Date.now() / 1000,
                        'ITEM_TIMEZONE' : moment().utcOffset(moment().utcOffset()).format('Z')
                    }
                });
                if (typeof callback !== 'function') {
                    callback = _.noop;
                }
                this.itemServiceApi.kill(callback);
            },

            /**
             * Checks if the current item is active
             * @returns {Boolean}
             */
            isCurrentItemActive: function(){
                return (this.testContext.itemSessionState !=4);
            },

            /**
             * Tells is the current item has been answered or not
             * The item is considered answered when at least one response has been set to not empty {base : null}
             *
             * @returns {Boolean}
             */
            isCurrentItemAnswered: function(){
                var answered = false;
                _.each(this.getCurrentItemState(), function(state){
                    if(state && _.isObject(state.response) && state.response.base !== null){
                        answered = true;//at least one response is not null so consider the item answered
                        return false;
                    }
                });
                return answered;
            },

            /**
             * Checks if a particular option is enabled for the current item
             * @param {String} option
             * @returns {Boolean}
             */
            hasOption: function(option) {
                return _.indexOf(this.testContext.categories, option) >= 0;
            },

            /**
             * Gets access to the qtiRunner instance
             * @returns {Object}
             */
            getQtiRunner: function(){
                var itemFrame = document.getElementById('qti-item');
                var itemWindow = itemFrame && itemFrame.contentWindow;
                var itemContainerFrame = itemWindow && itemWindow.document.getElementById('item-container');
                var itemContainerWindow = itemContainerFrame && itemContainerFrame.contentWindow;
                return itemContainerWindow && itemContainerWindow.qtiRunner;
            },

            /**
             * Checks if the current section is timed
             * @returns {Boolean}
             */
            isTimedSection: function(){
                var timeConstraints = this.testContext.timeConstraints,
                    isTimedSection = false;
                for( var index in timeConstraints ){
                    if(timeConstraints.hasOwnProperty(index) &&
                        timeConstraints[index].qtiClassName === 'assessmentSection' ){
                        isTimedSection = true;
                    }
                }

                return isTimedSection;
            },

            /**
             * Gets the list of items owned by the current section
             * @returns {Array}
             */
            getCurrentSectionItems: function(){
                var partId  = this.testContext.testPartId,
                    navMap  = this.testContext.navigatorMap,
                    sectionItems;

                for( var partIndex in navMap ){
                    if( !navMap.hasOwnProperty(partIndex)){
                        continue;
                    }
                    if( navMap[partIndex].id !== partId ){
                        continue;
                    }

                    for(var sectionIndex in navMap[partIndex].sections){
                        if( !navMap[partIndex].sections.hasOwnProperty(sectionIndex)){
                            continue;
                        }
                        if( navMap[partIndex].sections[sectionIndex].active === true ){
                            sectionItems = navMap[partIndex].sections[sectionIndex].items;
                            break;
                        }
                    }
                }

                return sectionItems;
            },

            /**
             * Skips the current item
             */
            skip: function () {
                this.disableGui();
                this.actionCall('skip');
            },

            /**
             * Handles the timeout state
             */
            timeout: function () {
                var self = this;
                this.disableGui();
                this.testContext.isTimeout = true;
                this.updateTimer();

                this.killItemSession(function () {
                    var confirmBox = $('.timeout-modal-feedback'),
                        testContext = self.testContext,
                        confirmBtn = confirmBox.find('.js-timeout-confirm, .modal-close');

                    if (testContext.numberCompletedSection === testContext.numberItemsSection) {
                        testMetaData.addData({"SECTION" : {"SECTION_EXIT_CODE" : testMetaData.SECTION_EXIT_CODE.COMPLETE_TIMEOUT}});
                    } else {
                        testMetaData.addData({"SECTION" : {"SECTION_EXIT_CODE" : testMetaData.SECTION_EXIT_CODE.TIMEOUT}});
                    }

                    self.enableGui();
                    confirmBox.modal({width: 500});
                    confirmBtn.off('click').on('click', function () {
                        confirmBox.modal('close');
                        self.actionCall('timeout');
                    });
                });
            },

            /**
             * Sets the assessment test context object
             * @param {Object} testContext
             */
            setTestContext: function(testContext) {
                this.testContext = testContext;
                this.itemServiceApi = eval(testContext.itemServiceApiCall);
                this.itemServiceApi.setHasBeenPaused(testContext.hasBeenPaused);
            },


            /**
             * Handles Metadata initialization
             */
            initMetadata: function (){
                testMetaData = testMetaDataFactory({
                    testServiceCallId: this.itemServiceApi.serviceCallId
                });
            },

            /**
             * Retrieve service responsible for broken session tracking
             * @returns {*}
             */
            getSessionStateService: function () {
                if (!sessionStateService) {
                    sessionStateService = this.testContext.sessionStateService({accuracy: 1000});
                }
                return sessionStateService;
            },

            /**
             * Updates the GUI
             * @param {Object} testContext
             */
            update: function (testContext) {
                var self = this;
                $controls.$itemFrame.remove();

                var $runner = $('#runner');
                $runner.css('height', 'auto');

                this.getSessionStateService().restart();

                this.setTestContext(testContext);
                this.updateContext();
                this.updateProgress();
                this.updateNavigation();
                this.updateTestReview();
                this.updateInformation();
                this.updateRubrics();
                this.updateTools(testContext);
                this.updateTimer();
                this.updateExitButton();
                this.resetCurrentItemState();
                this.initMetadata();

                $controls.$itemFrame = $('<iframe id="qti-item" frameborder="0" scrollbars="no"/>');
                $controls.$itemFrame.appendTo($controls.$contentBox);

                if (this.testContext.itemSessionState === this.TEST_ITEM_STATE_INTERACTING && self.testContext.isTimeout === false) {
                    $doc.off('.testRunner').on('serviceloaded.testRunner', function () {
                        self.afterTransition();
                        self.adjustFrame();
                        $controls.$itemFrame.css({visibility: 'visible'});
                    });

                    // Inject API into the frame.
                    this.itemServiceApi.loadInto($controls.$itemFrame[0], function () {
                        // We now rely on the 'serviceloaded' event.
                    });
                }
                else {
                    // e.g. no more attempts or timeout! Simply consider the transition is finished,
                    // but do not load the item.
                    self.afterTransition();
                }
            },

            /**
             * Displays feedback on the current state of the test
             */
            updateInformation: function () {

                if (this.testContext.isTimeout === true) {
                    feedback().error(__('Time limit reached for item "%s".', this.testContext.itemIdentifier));
                }
                else if (this.testContext.itemSessionState !== this.TEST_ITEM_STATE_INTERACTING) {
                    feedback().error(__('No more attempts allowed for item "%s".', this.testContext.itemIdentifier));
                }
            },

            /**
             * Updates the displayed tools
             * @param {Object} testContext
             */
            updateTools: function updateTools(testContext) {
                var showSkip = false;
                var showSkipEnd = false;
                var showNextSection = !!testContext.nextSection && (this.hasOption(optionNextSection) || this.hasOption(optionNextSectionWarning));

                if (this.testContext.allowSkipping === true) {
                    if (this.testContext.isLast === false) {
                        showSkip = true;
                    } else {
                        showSkipEnd = true;
                    }
                }

                $controls.$skip.toggle(showSkip);
                $controls.$skipEnd.toggle(showSkipEnd);
                $controls.$nextSection.toggle(showNextSection);

                actionBarTools.render('.tools-box-list', testContext, TestRunner);
            },

            /**
             * Displays a timer
             * @param {Object} cst
             * @returns {*|jQuery|HTMLElement}
             */
            createTimer: function(cst) {
                var $timer = $('<div>', {'class': 'qti-timer qti-timer__type-' + cst.qtiClassName }),
                    $label = $('<div>', {'class': 'qti-timer_label truncate', text: cst.label }),
                    $time  = $('<div>', {'class': 'qti-timer_time', text: this.formatTime(cst.seconds) });

                $timer.append($label);
                $timer.append($time);
                return $timer;
            },

            /**
             * Updates the timers
             */
            updateTimer: function () {
                var self = this;
                var hasTimers;
                $controls.$timerWrapper.empty();

                for (var i = 0; i < timerIds.length; i++) {
                    clearTimeout(timerIds[i]);
                }

                timerIds = [];
                currentTimes = [];
                lastDates = [];
                timeDiffs = [];

                if (self.testContext.isTimeout === false &&
                    self.testContext.itemSessionState === self.TEST_ITEM_STATE_INTERACTING) {

                    hasTimers = !!this.testContext.timeConstraints.length;
                    $controls.$topActionBar.toggleClass('has-timers', hasTimers);

                    if (hasTimers) {

                        // Insert QTI Timers container.
                        // self.formatTime(cst.seconds)
                        for (i = 0; i < this.testContext.timeConstraints.length; i++) {

                            var cst = this.testContext.timeConstraints[i];

                            if (cst.allowLateSubmission === false) {

                                // Set up a timer for this constraint
                                $controls.$timerWrapper.append(self.createTimer(cst));

                                // Set up a timer and update it with setInterval.
                                currentTimes[i] = cst.seconds;
                                lastDates[i] = new Date();
                                timeDiffs[i] = 0;
                                timerIndex = i;

                                cst.warningTime = Number.NEGATIVE_INFINITY;

                                if (self.testContext.timerWarning && self.testContext.timerWarning[cst.qtiClassName]) {
                                    cst.warningTime = parseInt(self.testContext.timerWarning[cst.qtiClassName], 10);
                                }
                                (function (timerIndex, cst) {
                                    timerIds[timerIndex] = setInterval(function () {

                                        timeDiffs[timerIndex] += (new Date()).getTime() - lastDates[timerIndex].getTime();

                                        if (timeDiffs[timerIndex] >= 1000) {
                                            var seconds = timeDiffs[timerIndex] / 1000;
                                            currentTimes[timerIndex] -= seconds;
                                            timeDiffs[timerIndex] = 0;
                                        }

                                        $timers.eq(timerIndex)
                                            .html(self.formatTime(Math.round(currentTimes[timerIndex])));

                                        if (currentTimes[timerIndex] <= 0) {
                                            // The timer expired...
                                            currentTimes[timerIndex] = 0;
                                            clearInterval(timerIds[timerIndex]);

                                            // Hide item to prevent any further interaction with the candidate.
                                            $controls.$itemFrame.hide();
                                            self.timeout();
                                        } else {
                                            lastDates[timerIndex] = new Date();
                                        }

                                        if (_.isFinite(cst.warningTime) && currentTimes[timerIndex] <= cst.warningTime) {
                                            self.timeWarning(cst);
                                        }

                                    }, 1000);
                                }(timerIndex, cst));
                            }
                        }

                        $timers = $controls.$timerWrapper.find('.qti-timer .qti-timer_time');
                        $controls.$timerWrapper.show();
                    }
                }
            },

            /**
             * Mark appropriate timer by warning colors and show feedback message
             *
             * @param {object} cst - Time constraint
             * @param {integer} cst.warningTime - Warning time in seconds.
             * @param {integer} cst.qtiClassName - Class name of qti instance for which the timer is set (assessmentItemRef | assessmentSection | testPart).
             * @param {integer} cst.seconds - Initial timer value.
             * @returns {undefined}
             */
            timeWarning: function (cst) {
                var message = '';
                $controls.$timerWrapper.find('.qti-timer__type-' + cst.qtiClassName).addClass('qti-timer__warning');

                // Initial time more than warning time in config
                if (cst.seconds > cst.warningTime) {
                    message = moment.duration(cst.warningTime, "seconds").humanize();
                    feedback().warning(__("Warning – You have %s remaining to complete the test.", message));
                }

                cst.warningTime = Number.NEGATIVE_INFINITY;
            },

            /**
             * Displays or hides the rubric block
             */
            updateRubrics: function () {
                $controls.$rubricBlocks.remove();

                if (this.testContext.rubrics.length > 0) {

                    $controls.$rubricBlocks = $('<div id="qti-rubrics"/>');

                    for (var i = 0; i < this.testContext.rubrics.length; i++) {
                        $controls.$rubricBlocks.append(this.testContext.rubrics[i]);
                    }

                    // modify the <a> tags in order to be sure it
                    // opens in another window.
                    $controls.$rubricBlocks.find('a').bind('click keypress', function () {
                        window.open(this.href);
                        return false;
                    });

                    $controls.$rubricBlocks.prependTo($controls.$contentBox);

                    if (MathJax) {
                        MathJax.Hub.Queue(["Typeset", MathJax.Hub], $controls.$rubricBlocks[0]);
                    }

                }
            },

            /**
             * Updates the list of navigation buttons (previous, next, skip, etc.)
             */
            updateNavigation: function () {
                $controls.$exit.show();

                if(this.testContext.isLast === true) {
                    $controls.$moveForward.hide();
                    $controls.$moveEnd.show();
                }
                else {
                    $controls.$moveForward.show();
                    $controls.$moveEnd.hide();
                }
                if (this.testContext.navigationMode === this.TEST_NAVIGATION_LINEAR) {
                    // LINEAR
                    $controls.$moveBackward.hide();
                }
                else {
                    // NONLINEAR
                    $controls.$controls.show();
                    if(this.testContext.canMoveBackward === true) {
                        $controls.$moveBackward.show();
                    }
                    else {
                        $controls.$moveBackward.hide();
                    }
                }
            },

            /**
             * Updates the test taker review screen
             */
            updateTestReview: function() {
                var considerProgress = this.testContext.considerProgress === true;

                if (this.testReview) {
                    this.testReview.toggle(considerProgress && this.hasOption(optionReviewScreen));
                    this.testReview.update(this.testContext);
                }
            },

            /**
             * Updates the progress bar
             */
            updateProgress: function () {
                var considerProgress = this.testContext.considerProgress === true;

                $controls.$progressBox.css('visibility', considerProgress ? 'visible' : 'hidden');

                if (considerProgress) {
                    this.progressUpdater.update(this.testContext);
                }
            },

            /**
             * Updates the test informations
             */
            updateContext: function () {

                $controls.$title.text(this.testContext.testTitle);

                // Visibility of section?
                var sectionText = (this.testContext.isDeepestSectionVisible === true) ? (' - ' + this.testContext.sectionTitle) : '';

                $controls.$position.text(sectionText);
                $controls.$titleGroup.show();
            },

            /**
             * Displays the right exit button
             */
            updateExitButton : function(){

                $controls.$logout.toggleClass('hidden', !this.testContext.logoutButton);
                $controls.$exit.toggleClass('hidden', !this.testContext.exitButton);
            },

            /**
             * Ensures the frame has the right size
             */
            adjustFrame: function () {
                var rubricHeight = $controls.$rubricBlocks.outerHeight(true) || 0;
                var frameContentHeight;
                var finalHeight = $(window).innerHeight() - $controls.$topActionBar.outerHeight() - $controls.$bottomActionBar.outerHeight();
                var itemFrame = $controls.$itemFrame.get(0);
                $controls.$contentBox.height(finalHeight);
                if($controls.$sideBars.length){
                    $controls.$sideBars.each(function() {
                        var $sideBar = $(this);
                        $sideBar.height(finalHeight - $sideBar.outerHeight() + $sideBar.height());
                    });
                }

                if(itemFrame && itemFrame.contentWindow){
                    frameContentHeight = $controls.$itemFrame.contents().outerHeight(true);

                    if (frameContentHeight < finalHeight) {
                        if (rubricHeight) {
                            frameContentHeight = Math.max(frameContentHeight, finalHeight - rubricHeight);
                        } else {
                            frameContentHeight = finalHeight;
                        }
                    }
                    if (itemFrame.contentWindow.$) {
                        itemFrame.contentWindow.$('body').trigger('setheight', [frameContentHeight]);
                    }
                    $controls.$itemFrame.height(frameContentHeight);
                }
            },

            /**
             * Locks the GUI
             */
            disableGui: function () {
                $controls.$naviButtons.addClass('disabled');
                if (this.testReview) {
                    this.testReview.disable();
                }
            },

            /**
             * Unlocks the GUI
             */
            enableGui: function () {
                $controls.$naviButtons.removeClass('disabled');
                if (this.testReview) {
                    this.testReview.enable();
                }
            },

            /**
             * Hides the GUI
             */
            hideGui: function () {
                $controls.$naviButtons.addClass('hidden');
                if (this.testReview) {
                    this.testReview.hide();
                }
            },

            /**
             * Shows the GUI
             */
            showGui: function () {
                $controls.$naviButtons.removeClass('hidden');
                if (this.testReview) {
                    this.testReview.show();
                }
            },

            /**
             * Formats a timer
             * @param {Number} totalSeconds
             * @returns {String}
             */
            formatTime: function (totalSeconds) {
                var sec_num = totalSeconds;
                var hours = Math.floor(sec_num / 3600);
                var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
                var seconds = sec_num - (hours * 3600) - (minutes * 60);

                if (hours < 10) {
                    hours = "0" + hours;
                }
                if (minutes < 10) {
                    minutes = "0" + minutes;
                }
                if (seconds < 10) {
                    seconds = "0" + seconds;
                }

                var time = hours + ':' + minutes + ':' + seconds;

                return time;
            },

            /**
             * Processes an error
             * @param {Object} error
             */
            processError : function processError(error) {
                var self = this;

                // keep disabled
                this.hideGui();
                this.beforeTransition();

                // ask the parent to display a message
                iframeNotifier.parent('messagealert', {
                    message : error.message,
                    action : function() {
                        if (testMetaData) {
                            testMetaData.clearData();
                        }
                        if (error.state === self.TEST_STATE_CLOSED) {
                            // test is closed, finish it
                            self.serviceApi.finish();
                        } else {
                            // test is still open, just exit to the index
                            self.serviceApi.exit();
                        }
                    }
                });
            },

            /**
             * Call action specified in testContext. A postfix <i>Url</i> will be added to the action name.
             * To specify actions see {@link https://github.com/oat-sa/extension-tao-testqti/blob/master/helpers/class.TestRunnerUtils.php}
             * @param {String} action - Action name
             * @param {Object} [extraParams] - Additional parameters to be sent to the server
             * @returns {undefined}
             */
            actionCall: function (action, extraParams) {
                var self = this,
                    params = {metaData: testMetaData ? testMetaData.getData() : {}};

                if (extraParams) {
                    params = _.assign(params, extraParams);
                }
                this.beforeTransition(function () {
                    $.ajax({
                        url: self.testContext[action + 'Url'],
                        cache: false,
                        data: params,
                        async: true,
                        dataType: 'json',
                        success: function (testContext) {
                            testMetaData.clearData();

                            if (!testContext.success) {
                                self.processError(testContext);
                            }
                            else if (testContext.state === self.TEST_STATE_CLOSED) {
                                self.serviceApi.finish();
                            }
                            else {
                                self.update(testContext);
                            }
                        }
                    });
                });
            },

            /**
             * Exit from test (after confirmation). All answered questions will be submitted.
             *
             * @returns {undefined}
             */
            exit: function () {
                var self = this;
                testMetaData.addData({
                    "TEST" : {"TEST_EXIT_CODE" : testMetaData.TEST_EXIT_CODE.INCOMPLETE},
                    "SECTION" : {"SECTION_EXIT_CODE" : testMetaData.SECTION_EXIT_CODE.QUIT}
                });
                this.displayExitMessage(
                    __('Are you sure you want to end the test?'),
                    function() {
                    self.killItemSession(function () {
                        self.actionCall('endTestSession');
                        testMetaData.clearData();
                    });
                    },
                    this.testReview ? this.testContext.reviewScope : null
                );
            },

            /**
             * Set the state of the current item in the test runner
             *
             * @param {string} id
             * @param {object} state
             */
            setCurrentItemState : function(id, state){
                if(id){
                    this.currentItemState[id] = state;
                }
            },

            /**
             * Reset the state of the current item in the test runner
             */
            resetCurrentItemState : function(){
                this.currentItemState = {};
            },

            /**
             * Get the state of the current item as stored in the test runner
             * @returns {Object}
             */
            getCurrentItemState : function(){
                return this.currentItemState;
            }
        };

        var config = module.config();
        if (config) {
            actionBarTools.register(config.qtiTools);
        }

        return {
            start: function (testContext) {

                $controls = {
                    // navigation
                    $moveForward: $('[data-control="move-forward"]'),
                    $moveEnd: $('[data-control="move-end"]'),
                    $moveBackward: $('[data-control="move-backward"]'),
                    $nextSection: $('[data-control="next-section"]'),
                    $skip: $('[data-control="skip"]'),
                    $skipEnd: $('[data-control="skip-end"]'),
                    $exit: $(window.parent.document).find('[data-control="exit"]'),
                    $logout: $(window.parent.document).find('[data-control="logout"]'),
                    $naviButtons: $('.bottom-action-bar .action'),
                    $skipButtons: $('.navi-box .skip'),
                    $forwardButtons: $('.navi-box .forward'),

                    // progress bar
                    $progressBar: $('[data-control="progress-bar"]'),
                    $progressLabel: $('[data-control="progress-label"]'),
                    $progressBox: $('.progress-box'),

                    // title
                    $title:  $('[data-control="qti-test-title"]'),
                    $position:  $('[data-control="qti-test-position"]'),

                    // timers
                    $timerWrapper:  $('[data-control="qti-timers"]'),

                    // other zones
                    $contentPanel: $('.content-panel'),
                    $controls: $('.qti-controls'),
                    $itemFrame: $('#qti-item'),
                    $rubricBlocks: $('#qti-rubrics'),
                    $contentBox: $('#qti-content'),
                    $sideBars: $('.test-sidebar'),
                    $topActionBar: $('.horizontal-action-bar.top-action-bar'),
                    $bottomActionBar: $('.horizontal-action-bar.bottom-action-bar')
                };

                // title
                $controls.$titleGroup = $controls.$title.add($controls.$position);

                $doc.ajaxError(function (event, jqxhr) {
                    if (jqxhr.status === 403) {
                        iframeNotifier.parent('serviceforbidden');
                    }
                });

                window.onServiceApiReady = function onServiceApiReady(serviceApi) {
                    TestRunner.serviceApi = serviceApi;

                    if (!testContext.success) {
                        TestRunner.processError(testContext);
                    }

                    // If the assessment test session is in CLOSED state,
                    // we give the control to the delivery engine by calling finish.
                    else if (testContext.state === TestRunner.TEST_STATE_CLOSED) {
                        serviceApi.finish();
                        testMetaData.clearData();
                    }
                    else {

                        if (TestRunner.getSessionStateService().getDuration()) {
                            TestRunner.setTestContext(testContext);
                            TestRunner.initMetadata();

                            TestRunner.keepItemTimed(TestRunner.getSessionStateService().getDuration());
                            TestRunner.getSessionStateService().restart();
                        } else {
                            TestRunner.update(testContext);
                        }
                    }
                };

                TestRunner.beforeTransition();
                TestRunner.testContext = testContext;

                $controls.$skipButtons.click(function () {
                    if (!$(this).hasClass('disabled')) {
                        TestRunner.skip();
                    }
                });

                $controls.$forwardButtons.click(function () {
                    if (!$(this).hasClass('disabled')) {
                        TestRunner.moveForward();
                    }
                });

                $controls.$moveBackward.click(function () {
                    if (!$(this).hasClass('disabled')) {
                        TestRunner.moveBackward();
                    }
                });

                $controls.$nextSection.click(function () {
                    if (!$(this).hasClass('disabled')) {
                        TestRunner.nextSection();
                    }
                });

                $controls.$exit.click(function (e) {
                    e.preventDefault();
                    TestRunner.exit();
                });

                $(window).on('resize', _.throttle(function () {
                    TestRunner.adjustFrame();
                    $controls.$titleGroup.show();
                }, 250));

                $doc.on('loading', function () {
                    iframeNotifier.parent('loading');
                });


                $doc.on('unloading', function () {
                    iframeNotifier.parent('unloading');
                });

                TestRunner.progressUpdater = progressUpdater($controls.$progressBar, $controls.$progressLabel);

                if (testContext.reviewScreen) {
                    TestRunner.testReview = testReview($controls.$contentPanel, {
                        region: testContext.reviewRegion || 'left',
                        hidden: !TestRunner.hasOption(optionReviewScreen),
                        reviewScope: testContext.reviewScope,
                        preventsUnseen: !!testContext.reviewPreventsUnseen,
                        canCollapse: !!testContext.reviewCanCollapse
                    }).on('jump', function(event, position) {
                        TestRunner.jump(position);
                    }).on('mark', function(event, flag, position) {
                        TestRunner.markForReview(flag, position);
                    });
                    $controls.$sideBars = $('.test-sidebar');
                }

                TestRunner.updateProgress();
                TestRunner.updateTestReview();

                iframeNotifier.parent('serviceready');


                TestRunner.adjustFrame();

                $controls.$topActionBar.add($controls.$bottomActionBar).animate({ opacity: 1 }, 600);

                deleter($('#feedback-box'));
                modal($('body'));

                //listen to state change in the current item
                $(document).on('responsechange', function(e, responseId, response){
                    if(responseId && response){
                        TestRunner.setCurrentItemState(responseId, {response:response});
                    }
                }).on('stateready', function(e, id, state){
                    if(id && state){
                        TestRunner.setCurrentItemState(id, state);
                    }
                }).on('heightchange', function(e, height) {
                    $controls.$itemFrame.height(height);
                });

            }
        };
    });
