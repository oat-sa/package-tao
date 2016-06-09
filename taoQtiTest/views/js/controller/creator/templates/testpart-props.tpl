<div id="testpart-props-{{identifier}}" class="testpart-props props clearfix">
    <h3>{{identifier}}</h3>

    <form autocomplete="off">

<!-- assessmentTest/testPart/identifier -->
        <div class="grid-row">
            <div class="col-5">
                <label for="testpart-identifier">{{__ 'Identifier'}} <abbr title="{{__ 'Required field'}}">*</abbr></label>
            </div>
            <div class="col-6">
                <input type="text" name="testpart-identifier" data-bind="identifier" data-validate="$notEmpty; $idFormat; $testIdAvailable(original={{identifier}});" />
            </div>
            <div class="col-1 help">
                <span class="icon-help" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
                <div class="tooltip-content">
                {{__ 'The test part identifier.'}}
                </div>
            </div>
        </div>

<!-- assessmentTest/testPart/navigationMode -->
        <div class="grid-row pseudo-label-box">
            <div class="col-5">
               {{__ 'Navigation'}} <abbr title="{{__ 'Required field'}}">*</abbr>
            </div>
            <div class="col-6">
                <label>
                    <input type="radio" name="testpart-navigation-mode" value="0" checked="checked" data-bind="navigationMode" data-bind-encoder="number" />
                    <span class="icon-radio"></span>
                    {{__ 'Linear'}}
                </label>
                <label>
                    <input type="radio" name="testpart-navigation-mode" value="1"  />
                    <span class="icon-radio"></span>
                    {{__ 'Non Linear'}}
                </label>
            </div>
            <div class="col-1 help">
                <span class="icon-help" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
                <div class="tooltip-content">
                {{__ 'The navigation mode determines the general paths that the candidate may take. A linear mode restricts the candidate to attempt each item in turn. Non Linear removes this restriction.'}}
                </div>
            </div>
        </div>

<!-- assessmentTest/testPart/submissionMode -->
        <div class="grid-row pseudo-label-box">
            <div class="col-5">
                {{__ 'Submission'}} <abbr title="{{__ 'Required field'}}">*</abbr>
            </div>
            <div class="col-6">
                <label>
                    <input type="radio" name="testpart-submission-mode" value="0" checked="checked" data-bind="submissionMode" data-bind-encoder="number" />
                    <span class="icon-radio"></span>
                    {{__ 'Individual'}}
                </label>
                <label>
                    <input type="radio" name="testpart-submission-mode" value="1"  />
                    <span class="icon-radio"></span>
                    {{__ 'Simultaneous'}}
                </label>
            </div>
            <div class="col-1 help">
                <span class="icon-help" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
                <div class="tooltip-content">
                {{__ "The submission mode determines when the candidate's responses are submitted for response processing. A testPart in individual mode requires the candidate to submit their responses on an item-by-item basis. In simultaneous mode the candidate's responses are all submitted together at the end of the testPart."}}
                </div>
            </div>
        </div>

        <h4 class="toggler closed" data-toggle="~ .testpart-item-session-control">{{__ 'Item Session Control'}}</h4>


<!-- assessmentTest/testPart/itemSessionControl -->
        <div class="testpart-item-session-control toggled">

<!-- assessmentTest/testPart/itemSessionControl/maxAttempts -->
            <div class="grid-row">
                <div class="col-5">
                    <label for="testpart-max-attempts">{{__ 'Max Attempts'}}</label>
                </div>
                <div class="col-6">
                    <input name="testpart-max-attempts" type="text" data-increment="1" data-min="0" value="1" data-bind="itemSessionControl.maxAttempts" data-bind-encoder="number" />
                </div>
                <div class="col-1 help">
                    <span class="icon-help" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
                    <div class="tooltip-content">
                    {{__ 'Controls the maximum number of attempts allowed. 0 means unlimited.'}}
                    </div>
                </div>
            </div>

<!-- assessmentTest/testPart/itemSessionControl/showFeedback -->
            <div class="grid-row pseudo-label-box">
                <div class="col-5">
                    <label for="testpart-show-feedback">{{__ 'Show Feedback'}}</label>
                </div>
                <div class="col-6">
                    <label>
                        <input type="checkbox" name="testpart-show-feedback" value="true" data-bind="itemSessionControl.showFeedback" data-bind-encoder="boolean" />
                        <span class="icon-checkbox" />
                    </label>
                </div>
                <div class="col-1 help">
                    <span class="icon-help" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
                    <div class="tooltip-content">
                    {{__ 'This constraint affects the visibility of feedback after the end of the last attempt.'}}
                    </div>
                </div>
            </div>

{{!-- Property not yet available in delivery
<!-- assessmentTest/testPart/itemSessionControl/allowReview -->
            <div class="grid-row pseudo-label-box">
                <div class="col-5">
                    <label for="testpart-show-allow-review">{{__ 'Allow Review'}}</label>
                </div>
                <div class="col-6">
                    <label>
                        <input type="checkbox" name="testpart-allow-review" value="true" checked="checked"  data-bind="itemSessionControl.allowReview" data-bind-encoder="boolean" />
                        <span class="icon-checkbox" />
                    </label>
                </div>
                <div class="col-1 help">
                    <span class="icon-help" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
                    <div class="tooltip-content">
                    {{__ 'Allow the candidate to review his answers.'}}
                    </div>
                </div>
            </div>
--}}

{{!-- Property not yet available in delivery
<!-- assessmentTest/testPart/itemSessionControl/showSolution -->
            <div class="grid-row pseudo-label-box">
                <div class="col-5">
                    <label for="testpart-show-solution">{{__ 'Show Solution'}}</label>
                </div>
                <div class="col-6">
                    <label>
                        <input type="checkbox" name="testpart-show-solution" value="true"  data-bind="itemSessionControl.showSolution" data-bind-encoder="boolean"  />
                        <span class="icon-checkbox" />
                    </label>
                </div>
                <div class="col-1 help">
                    <span class="icon-help" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
                    <div class="tooltip-content">
                    {{__ 'Show the solution once the answer is submitted.'}}
                    </div>
                </div>
            </div>
--}}

<!-- assessmentTest/testPart/itemSessionControl/allowComment -->
            <div class="grid-row pseudo-label-box">
                <div class="col-5">
                    <label for="testpart-allow-comment">{{__ 'Allow Comment'}}</label>
                </div>
                <div class="col-6">
                    <label>
                        <input type="checkbox" name="testpart-allow-comment" value="true" data-bind="itemSessionControl.allowComment" data-bind-encoder="boolean" />
                        <span class="icon-checkbox" />
                    </label>
                </div>
                <div class="col-1 help">
                    <span class="icon-help" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
                    <div class="tooltip-content">
                    {{__ 'This constraint controls whether or not the candidate is allowed to provide a comment on the item during the session. Comments are not part of the assessed responses.'}}
                    </div>
                </div>
            </div>

<!-- assessmentTest/testPart/itemSessionControl/allowSkipping -->
            <div class="grid-row pseudo-label-box">
                <div class="col-5">
                    <label for="testpart-allow-skipping">{{__ 'Allow Skipping'}}</label>
                </div>
                <div class="col-6">
                    <label>
                        <input type="checkbox" name="testpart-allow-skipping" value="true" checked="checked"  data-bind="itemSessionControl.allowSkipping" data-bind-encoder="boolean"   />
                        <span class="icon-checkbox" />
                    </label>
                </div>
                <div class="col-1 help">
                    <span class="icon-help" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
                    <div class="tooltip-content">
                    {{__ 'If the candidate can skip the item, without submitting a response.'}}
                    </div>
                </div>
            </div>

{{!-- Property not yet available in delivery
<!-- assessmentTest/testPart/itemSessionControl/validateResponses -->
            <div class="grid-row pseudo-label-box">
                <div class="col-5">
                    <label for="testpart-validate-responses">{{__ 'Validate Responses'}}</label>
                </div>
                <div class="col-6">
                    <label>
                        <input type="checkbox" name="testpart-validate-responses" value="true"  data-bind="itemSessionControl.validateResponses" data-bind-encoder="boolean"  />
                        <span class="icon-checkbox" />
                    </label>
                </div>
            </div>
            <div class="col-1 help">
                <span class="icon-help" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
                <div class="tooltip-content">
                {{__ "The candidate is not allowed to submit wrong responses."}}
                </div>
            </div>
--}}
        </div>

        <h4 class="toggler closed" data-toggle="~ .testpart-time-limits">{{__ 'Time Limits'}}</h4>

<!-- assessmentTest/testPart/timeLimits/minTime -->
        <div class="testpart-time-limits toggled">

{{!-- Property not yet available in delivery
<!-- assessmentTest/testPart/timeLimits/minTime -->
            <div class="grid-row">
                <div class="col-5">
                    <label for="testpart-min-time">{{__ 'Minimum Duration'}}</label>
                </div>
                <div class="col-6">
                    <input type="text" name="testpart-min-time" value="00:00:00" data-duration="HH:mm:ss" data-bind="timeLimits.minTime" data-bind-encoder="time" />
                </div>
                <div class="col-1 help">
                    <span class="icon-help" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
                    <div class="tooltip-content">
                    {{__ 'Minimum duration for this test part.'}}
                    </div>
                </div>
            </div>
--}}

<!-- assessmentTest/testPart/timeLimits/maxTime -->
            <div class="grid-row">
                <div class="col-5">
                    <label for="testpart-max-time">{{__ 'Maximum Duration'}}</label>
                </div>
                <div class="col-6">
                    <input type="text" name="max-time" value="00:00:00" data-duration="HH:mm:ss" data-bind="timeLimits.maxTime" data-bind-encoder="time" />
                </div>
                <div class="col-1 help">
                    <span class="icon-help" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
                    <div class="tooltip-content">
                    {{__ 'Maximum duration for this test part.'}}
                    </div>
                </div>
            </div>

<!-- assessmentTest/testPart/timeLimits/allowLateSubmission -->
            <div class="grid-row pseudo-label-box">
                <div class="col-5">
                    <label for="testpart-allow-late-submission">{{__ 'Late submission allowed'}}</label>
                </div>
                <div class="col-6">
                    <label>
                        <input type="checkbox" name="section-allow-late-submission" value="true" data-bind="timeLimits.allowLateSubmission" data-bind-encoder="boolean" />
                        <span class="icon-checkbox" />
                    </label>
                </div>
                <div class="col-1 help">
                    <span class="icon-help" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
                    <div class="tooltip-content">
                    {{__ "Whether a candidate's response that is beyond the maximum duration of the test part should still be accepted."}}
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
