<div id="itemref-props-{{identifier}}" class="itemref-props props clearfix">

    <h3>{{label}}</h3>

<!-- assessmentTest/testPart/assessmentSection/sectionPart/identifier -->
    <div class="grid-row">
        <div class="col-5">
            <label for="itemref-identifier">{{__ 'Identifier'}} <abbr title="{{__ 'Required field'}}">*</abbr></label>
        </div>
        <div class="col-6">
            <input type="text" name="itemref-identifier" data-bind="identifier" data-validate="$notEmpty; $testIdFormat; $testIdAvailable(original={{identifier}});" />
        </div>
        <div class="col-1 help">
            <span class="icon-help" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span> 
            <div class="tooltip-content">
            {{__ 'The identifier of the item reference.'}}
            </div>
        </div>
    </div>

<!-- assessmentTest/testPart/assessmentSection/sectionPart/href -->
    <div class="grid-row">
        <div class="col-5">
            <label for="itemref-href">{{__ 'Reference'}} <abbr title="{{__ 'Required field'}}">*</abbr></label>
        </div>
        <div class="col-6">
            <input type="text" name="itemref-href" data-bind="href" readonly="readonly" />
        </div>
        <div class="col-1 help">
            <span class="icon-help" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span> 
            <div class="tooltip-content">
            {{__ 'The reference.'}}
            </div>
        </div>
    </div>
    
<!-- assessmentTest/testPart/assessmentSection/sectionPart/category -->
    <div class="grid-row">
        <div class="col-5">
            <label for="itemref-category">{{__ 'Categories'}}</label>
        </div>
        <div class="col-6">
            <input type="text" name="itemref-category" data-bind="categories" data-bind-encoder="str2array" />
        </div>
        <div class="col-1 help">
            <span class="icon-help" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span> 
            <div class="tooltip-content">
            {{__ 'Items can optionally be assigned to one or more categories.'}}
            </div>
        </div>
    </div>

<!-- assessmentTest/testPart/assessmentSection/sectionPart/required -->
    <div class="grid-row pseudo-label-box">
        <div class="col-5">
            <label for="itemref-required">{{__ 'Required'}}</label>
        </div>
        <div class="col-6">
            <label>
                <input type="checkbox" name="itemref-required" value="true" data-bind="required" data-bind-encoder="boolean" />
                <span class="icon-checkbox"></span>
            </label>
        </div>
        <div class="col-1 help">
            <span class="icon-help" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span> 
            <div class="tooltip-content">
            {{__ 'If required it must appear (at least once) in the selection.'}}
            </div>
        </div>
    </div>

<!-- assessmentTest/testPart/assessmentSection/sectionPart/fixed -->
    <div class="grid-row pseudo-label-box">
        <div class="col-5">
            <label for="itemref-fixed">{{__ 'Fixed'}}</label>
        </div>
        <div class="col-6">
            <label>
                <input type="checkbox" name="itemref-fixed" value="true" data-bind="fixed" data-bind-encoder="boolean" />
                <span class="icon-checkbox"></span>
            </label>
        </div>
        <div class="col-1 help">
            <span class="icon-help" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span> 
            <div class="tooltip-content">
            {{__ 'Not shuffled, the position remains fixed.'}}
            </div>
        </div>
    </div>


    <h4 class="toggler closed" data-toggle="~ .itemref-item-session-control">{{__ 'Item Session Control'}}</h4>

<!-- assessmentTest/testPart/assessmentSection/sectionPart/itemSessionControl -->
    <div class="itemref-item-session-control toggled">
<!-- assessmentTest/testPart/assessmentSection/sectionPart/itemSessionControl/maxAttempts -->
        <div class="grid-row">
            <div class="col-5">
                <label for="itemref-max-attempts">{{__ 'Max Attempts'}}</label>
            </div>
            <div class="col-6">
                <input name="itemref-max-attempts" type="text" data-increment="1" data-min="0" value="1" data-bind="itemSessionControl.maxAttempts" data-bind-encoder="number" />
            </div>
            <div class="col-1 help">
                <span class="icon-help" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span> 
                <div class="tooltip-content">
                {{__ 'Controls the maximum number of attempts allowed. 0 means unlimited.'}}
                </div>
            </div>
        </div>

<!-- assessmentTest/testPart/assessmentSection/sectionPart/itemSessionControl/showFeedback -->
        <div class="grid-row pseudo-label-box">
            <div class="col-5">
                <label for="itemref-show-feedback">{{__ 'Show Feedback'}}</label>
            </div>
            <div class="col-6">
                <label>
                    <input type="checkbox" name="itemref-show-feedback" value="true" data-bind="itemSessionControl.showFeedback" data-bind-encoder="boolean" />
                    <span class="icon-checkbox"></span>
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
<!-- assessmentTest/testPart/assessmentSection/sectionPart/itemSessionControl/allowReview -->
        <div class="grid-row pseudo-label-box">
            <div class="col-5">
                <label for="itemref-show-allow-review">{{__ 'Allow Review'}}</label>
            </div>
            <div class="col-6">
                <label>
                    <input type="checkbox" name="itemref-allow-review" value="true" checked="checked" data-bind="itemSessionControl.allowReview" data-bind-encoder="boolean" />
                    <span class="icon-checkbox"></span>
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
<!-- assessmentTest/testPart/assessmentSection/sectionPart/itemSessionControl/showSolution -->
        <div class="grid-row pseudo-label-box">
            <div class="col-5">
                <label for="itemref-show-solution">{{__ 'Show Solution'}}</label>
            </div>
            <div class="col-6">
                <label>
                    <input type="checkbox" name="itemref-show-solution" value="true"  data-bind="itemSessionControl.showSolution" data-bind-encoder="boolean" />
                    <span class="icon-checkbox"></span>
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

<!-- assessmentTest/testPart/assessmentSection/sectionPart/itemSessionControl/allowComment -->
        <div class="grid-row pseudo-label-box">
            <div class="col-5">
                <label for="itemref-allow-comment">{{__ 'Allow Comment'}}</label>
            </div>
            <div class="col-6">
                <label>
                    <input type="checkbox" name="itemref-allow-comment" value="true"  data-bind="itemSessionControl.allowComment" data-bind-encoder="boolean" />
                    <span class="icon-checkbox"></span>
                </label>
            </div>
            <div class="col-1 help">
                <span class="icon-help" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span> 
                <div class="tooltip-content">
                {{__ 'This constraint controls whether or not the candidate is allowed to provide a comment on the item during the session. Comments are not part of the assessed responses.'}}
                </div>
            </div>
        </div>

<!-- assessmentTest/testPart/assessmentSection/sectionPart/itemSessionControl/allowSkipping -->
        <div class="grid-row pseudo-label-box">
            <div class="col-5">
                <label for="itemref-allow-skipping">{{__ 'Allow Skipping'}}</label>
            </div>
            <div class="col-6">
                <label>
                    <input type="checkbox" name="itemref-allow-skipping" value="true" checked="checked" data-bind="itemSessionControl.allowSkipping" data-bind-encoder="boolean" />
                    <span class="icon-checkbox"></span>
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
<!-- assessmentTest/testPart/assessmentSection/sectionPart/itemSessionControl/validateResponses -->
        <div class="grid-row pseudo-label-box">
            <div class="col-5">
                <label for="itemref-validate-responses">{{__ 'Validate Responses'}}</label>
            </div>
            <div class="col-6">
                <label>
                    <input type="checkbox" name="itemref-validate-responses" value="true" data-bind="itemSessionControl.validateResponses" data-bind-encoder="boolean" />
                    <span class="icon-checkbox"></span>
                </label>
            </div>
            <div class="col-1 help">
                <span class="icon-help" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span> 
                <div class="tooltip-content">
                {{__ "The candidate is not allowed to submit wrong responses."}}
                </div>
            </div>
        </div>
--}}
    </div>

    <h4 class="toggler closed" data-toggle="~ .itemref-time-limits">{{__ 'Time Limits'}}</h4>

<!-- assessmentTest/timeLimits -->
    <div class="itemref-time-limits toggled">


{{!-- Property not yet available in delivery
<!-- assessmentTest/testPart/assessmentSection/sectionPart/timeLimits/minTime -->
        <div class="grid-row">
            <div class="col-5">
                <label for="itemref-min-time">{{__ 'Minimum Duration'}}</label>
            </div>
            <div class="col-6">
                <input type="text" name="itemref-min-time" value="00:00:00" data-duration="HH:mm:ss" data-bind="timeLimits.minTime" data-bind-encoder="time" />
            </div>
                <div class="col-1 help">
                <span class="icon-help" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span> 
                <div class="tooltip-content">
                    {{__ 'Minimum duration for this item.'}}
                    </div>
                </div>
        </div>
--}}

<!-- assessmentTest/testPart/assessmentSection/sectionPart/timeLimits/maxTime -->
        <div class="grid-row">
            <div class="col-5">
                <label for="itemref-max-time">{{__ 'Maximum Duration'}}</label>
            </div>
            <div class="col-6">
                <input type="text" name="max-time" value="00:00:00" data-duration="HH:mm:ss" data-bind="timeLimits.maxTime" data-bind-encoder="time" />
            </div>
            <div class="col-1 help">
                <span class="icon-help" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span> 
                <div class="tooltip-content">
                {{__ 'Maximum duration for this item.'}}
                </div>
            </div>
        </div>

<!-- assessmentTest/testPart/assessmentSection/sectionPart/timeLimits/allowLateSubmission -->
        <div class="grid-row pseudo-label-box">
            <div class="col-5">
                <label for="itemref-allow-late-submission">{{__ 'Late submission allowed'}}</label>
            </div>
            <div class="col-6">
                <label>
                    <input type="checkbox" name="itemref-allow-late-submission" value="true" data-bind="timeLimits.allowLateSubmission" data-bind-encoder="boolean" />
                    <span class="icon-checkbox"></span>
                </label>
            </div>
            <div class="col-1 help">
                <span class="icon-help" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span> 
                <div class="tooltip-content">
                {{__ "Whether a candidate's response that is beyond the maximum duration of the item should still be accepted."}}
                </div>
            </div>
        </div>
    </div>
</div>
