<div id="section-props-{{identifier}}" class="section-props props clearfix">
    <h3>{{title}}</h3>

<!-- assessmentTest/testPart/assessmentSection/identifier -->
    <div class="grid-row">
        <div class="col-5">
            <label for="section-identifier">{{__ 'Identifier'}} <abbr title="{{__ 'Required field'}}">*</abbr></label>
        </div>
        <div class="col-6">
            <input type="text" name="section-identifier" data-bind="identifier" data-validate="$notEmpty; $idFormat; $testIdAvailable(original={{identifier}});" />
        </div>
        <div class="col-1 help">
            <span class="icon-help" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
            <div class="tooltip-content">
            {{__ 'The identifier of the section.'}}
            </div>
        </div>
    </div>

<!-- assessmentTest/testPart/assessmentSection/title -->
    <div class="grid-row">
        <div class="col-5">
            <label for="section-title">{{__ 'Title'}} <abbr title="{{__ 'Required field'}}">*</abbr></label>
        </div>
        <div class="col-6">
            <input type="text" name="section-title" data-bind="title" data-validate="$notEmpty" />
        </div>
        <div class="col-1 help">
            <span class="icon-help" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
            <div class="tooltip-content">
            {{__ 'The section title.'}}
            </div>
        </div>
    </div>

{{!-- Property not yet available in delivery
<!-- assessmentTest/testPart/assessmentSection/required -->
    <div class="grid-row pseudo-label-box">
        <div class="col-5">
            <label for="section-required">{{__ 'Required'}}</label>
        </div>
        <div class="col-6">
            <label>
                <input type="checkbox" name="section-required" value="true" data-bind="required" data-bind-encoder="boolean" />
                <span class="icon-checkbox"></span>
            </label>
        </div>
        <div class="col-1 help">
            <span class="icon-help" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
            <div class="tooltip-content">
            {{__ 'If required, it must appears at least once in the selection.'}}
            </div>
        </div>
    </div>
--}}

{{!-- Property not yet available in delivery
<!-- assessmentTest/testPart/assessmentSection/fixed -->
    <div class="grid-row pseudo-label-box">
        <div class="col-5">
            <label for="section-fixed">{{__ 'Fixed'}}</label>
        </div>
        <div class="col-6">
            <label>
                <input type="checkbox" name="section-fixed" value="true" data-bind="fixed" data-bind-encoder="boolean" />
                <span class="icon-checkbox"></span>
            </label>
        </div>
        <div class="col-1 help">
            <span class="icon-help" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
            <div class="tooltip-content">
            {{__ 'Fixed position in a shuffled the selection.'}}
            </div>
        </div>
    </div>
--}}

<!-- assessmentTest/testPart/assessmentSection/visible -->
    <div class="grid-row pseudo-label-box">
        <div class="col-5">
            <label for="section-visible">{{__ 'Visible'}} <abbr title="{{__ 'Required field'}}">*</abbr></label>
        </div>
        <div class="col-6">
            <label>
                <input type="checkbox" name="section-visible" value="true" checked="checked"  data-bind="visible" data-bind-encoder="boolean" />
                <span class="icon-checkbox"></span>
            </label>
        </div>
        <div class="col-1 help">
            <span class="icon-help" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
            <div class="tooltip-content">
            {{__ 'A visible section is one that is identifiable by the candidate.'}}
            </div>
        </div>
    </div>

<!-- assessmentTest/testPart/assessmentSection/keepTogether -->
    <div class="grid-row pseudo-label-box">
        <div class="col-5">
            <label for="section-keep-together">{{__ 'Keep Together'}}</label>
        </div>

        <div class="col-6">
            <label>
                <input type="checkbox" name="section-keep-together" value="true" checked="checked"  data-bind="keepTogether" data-bind-encoder="boolean" />
                <span class="icon-checkbox"></span>
            </label>
        </div>
        <div class="col-1 help">
            <span class="icon-help" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
            <div class="tooltip-content">
            {{__ 'An invisible section with a parent that is subject to shuffling can specify whether or not its children, which will appear to the candidate as if they were part of the parent, are shuffled as a block or mixed up with the other children of the parent section.'}}
            </div>
        </div>
    </div>

    <!-- assessmentTest/testPart/assessmentSection/sectionPart/category -->
    <div class="grid-row">
        <div class="col-5">
            <label for="section-category">{{__ 'Categories'}}</label>
        </div>
        <div class="col-6">
            <input type="text" name="section-category" />
        </div>
        <div class="col-1 help">
            <span class="icon-help" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
            <div class="tooltip-content">
            {{__ 'Section level category enables configuring the categories of its composing items all at once. A category in gray means that all items have that category. A category in white means that only a few items have that category.'}}
            </div>
        </div>
    </div>

    <h4 class="toggler closed" data-toggle="~ .section-selection">{{__ 'Selection'}}</h4>

<!-- assessmentTest/testPart/assessmentSection/selection -->
    <div class="section-selection toggled">

        <div class="grid-row pseudo-label-box">
            <div class="col-5">
                <label for="section-enable-selection">{{__ 'Enable selection'}}</label>
            </div>

            <div class="col-6">
                <label>
                    <input type="checkbox" name="section-enable-selection"  />
                    <span class="icon-checkbox"></span>
                </label>
            </div>
        </div>

<!-- assessmentTest/testPart/assessmentSection/selection/select -->
        <div class="grid-row">
            <div class="col-5">
                <label for="section-select">{{__ 'Select'}} <abbr title="{{__ 'Required field'}}">*</abbr></label>
            </div>
            <div class="col-6">
                <input name="section-select" type="text" data-increment="1" data-min="0" value="0" data-bind="selection.select"  data-bind-encoder="number" />
            </div>
            <div class="col-1 help">
                <span class="icon-help" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
                <div class="tooltip-content">
                {{__ 'The number of child elements to be selected.'}}
                </div>
            </div>
        </div>

<!-- assessmentTest/testPart/assessmentSection/selection/withReplacement -->
        <div class="grid-row pseudo-label-box">
            <div class="col-5">
                <label for="section-with-replacement">{{__ 'With Replacement'}}</label>
            </div>

            <div class="col-6">
                <label>
                    <input type="checkbox" name="section-with-replacement" value="true" data-bind="selection.withReplacement"  data-bind-encoder="boolean" />
                    <span class="icon-checkbox"></span>
                </label>
            </div>
            <div class="col-1 help">
                <span class="icon-help" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
                <div class="tooltip-content">
                {{__ 'When selecting child elements each element is normally eligible for selection once only.'}}
                </div>
            </div>
        </div>
    </div>

    <h4 class="toggler closed" data-toggle="~ .section-ordering">{{__ 'Ordering'}}</h4>

<!-- assessmentTest/testPart/assessmentSection/ordering -->
    <div class="section-ordering toggled">
<!-- assessmentTest/testPart/assessmentSection/ordering/shuffle -->
        <div class="grid-row pseudo-label-box">
            <div class="col-5">
                <label for="section-shuffle">{{__ 'Shuffle'}}</label>
            </div>

            <div class="col-6">
                <label>
                    <input type="checkbox" name="section-shuffle" value="true" data-bind="ordering.shuffle"  data-bind-encoder="boolean"  />
                    <span class="icon-checkbox"></span>
                </label>
            </div>
            <div class="col-1 help">
                <span class="icon-help" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
                <div class="tooltip-content">
                {{__ 'If set, it causes the order of the child elements to be randomized, otherwise it uses the order in which the child elements are defined.'}}
                </div>
            </div>
        </div>
    </div>

    <h4 class="toggler closed" data-toggle="~ .section-item-session-control">{{__ 'Item Session Control'}}</h4>

<!-- assessmentTest/testPart/assessmentSection/itemSessionControl -->
    <div class="section-item-session-control toggled">
<!-- assessmentTest/testPart/assessmentSection/itemSessionControl/maxAttempts -->
        <div class="grid-row">
            <div class="col-5">
                <label for="section-max-attempts">{{__ 'Max Attempts'}}</label>
            </div>
            <div class="col-6">
                <input name="section-max-attempts" type="text" data-increment="1" data-min="0" value="1" data-bind="itemSessionControl.maxAttempts" data-bind-encoder="number" />
            </div>
            <div class="col-1 help">
                <span class="icon-help" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
                <div class="tooltip-content">
                {{__ 'Controls the maximum number of attempts allowed. 0 means unlimited.'}}
                </div>
            </div>
        </div>

<!-- assessmentTest/testPart/assessmentSection/itemSessionControl/showFeedback -->
        <div class="grid-row pseudo-label-box">
            <div class="col-5">
                <label for="section-show-feedback">{{__ 'Show Feedback'}}</label>
            </div>
            <div class="col-6">
                <label>
                    <input type="checkbox" name="section-show-feedback" value="true" data-bind="itemSessionControl.showFeedback" data-bind-encoder="boolean" />
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
<!-- assessmentTest/testPart/assessmentSection/itemSessionControl/allowReview -->
        <div class="grid-row pseudo-label-box">
            <div class="col-5">
                <label for="section-show-allow-review">{{__ 'Allow Review'}}</label>
            </div>
            <div class="col-6">
                <label>
                    <input type="checkbox" name="section-allow-review" value="true" checked="checked" data-bind="itemSessionControl.allowReview" data-bind-encoder="boolean" />
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
<!-- assessmentTest/testPart/assessmentSection/itemSessionControl/showSolution -->
        <div class="grid-row pseudo-label-box">
            <div class="col-5">
                <label for="section-show-solution">{{__ 'Show Solution'}}</label>
            </div>
            <div class="col-6">
                <label>
                    <input type="checkbox" name="section-show-solution" value="true"  data-bind="itemSessionControl.showSolution" data-bind-encoder="boolean" />
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

<!-- assessmentTest/testPart/assessmentSection/itemSessionControl/allowComment -->
        <div class="grid-row pseudo-label-box">
            <div class="col-5">
                <label for="section-allow-comment">{{__ 'Allow Comment'}}</label>
            </div>
            <div class="col-6">
                <label>
                    <input type="checkbox" name="section-allow-comment" value="true"  data-bind="itemSessionControl.allowComment" data-bind-encoder="boolean" />
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

<!-- assessmentTest/testPart/assessmentSection/itemSessionControl/allowSkipping -->
        <div class="grid-row pseudo-label-box">
            <div class="col-5">
                <label for="section-allow-skipping">{{__ 'Allow Skipping'}}</label>
            </div>
            <div class="col-6">
                <label>
                    <input type="checkbox" name="section-allow-skipping" value="true" checked="checked" data-bind="itemSessionControl.allowSkipping" data-bind-encoder="boolean" />
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
<!-- assessmentTest/testPart/assessmentSection/itemSessionControl/validateResponses -->
        <div class="grid-row pseudo-label-box">
            <div class="col-5">
                <label for="section-validate-responses">{{__ 'Validate Responses'}}</label>
            </div>
            <div class="col-6">
                <label>
                    <input type="checkbox" name="section-validate-responses" value="true" data-bind="itemSessionControl.validateResponses" data-bind-encoder="boolean" />
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

    <h4 class="toggler closed" data-toggle="~ .section-time-limits">{{__ 'Time Limits'}}</h4>

<!-- assessmentTest/timeLimits -->
    <div class="section-time-limits toggled">


{{!-- Property not yet available in delivery
<!-- assessmentTest/testPart/assessmentSection/timeLimits/minTime -->
        <div class="grid-row">
            <div class="col-5">
                <label for="section-min-time">{{__ 'Minimum Duration'}}</label>
            </div>
            <div class="col-6">
                <input type="text" name="section-min-time" value="00:00:00" data-duration="HH:mm:ss" data-bind="timeLimits.minTime" data-bind-encoder="time" />
            </div>
            <div class="col-1 help">
                <span class="icon-help" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
                <div class="tooltip-content">
                {{__ 'Minimum duration for this section.'}}
                </div>
            </div>
        </div>
--}}

<!-- assessmentTest/testPart/assessmentSection/timeLimits/maxTime -->
        <div class="grid-row">
            <div class="col-5">
                <label for="section-max-time">{{__ 'Maximum Duration'}}</label>
            </div>
            <div class="col-6">
                <input type="text" name="max-time" value="00:00:00" data-duration="HH:mm:ss" data-bind="timeLimits.maxTime" data-bind-encoder="time" />
            </div>
            <div class="col-1 help">
                <span class="icon-help" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
                <div class="tooltip-content">
                {{__ 'Maximum duration for this section.'}}
                </div>
            </div>
        </div>

<!-- assessmentTest/testPart/assessmentSection/timeLimits/allowLateSubmission -->
        <div class="grid-row pseudo-label-box">
            <div class="col-5">
                <label for="section-allow-late-submission">{{__ 'Late submission allowed'}}</label>
            </div>
            <div class="col-6">
                <label>
                    <input type="checkbox" name="section-allow-late-submission" value="true" data-bind="timeLimits.allowLateSubmission" data-bind-encoder="boolean" />
                    <span class="icon-checkbox"></span>
                </label>
            </div>
            <div class="col-1 help">
                <span class="icon-help" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
                <div class="tooltip-content">
                {{__ "Whether a candidate's response that is beyond the maximum duration of the section should still be accepted."}}
                </div>
            </div>
        </div>
    </div>
</div>
