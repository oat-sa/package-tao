<div class="test-props props clearfix">

    <!-- test properties -->
    <h3 data-bind="title"></h3>

<!-- assessmentTest/identifier -->
    <div class="grid-row">
        <div class="col-5">
            <label for="test-identifier">{{__ 'Identifier'}} <abbr title="{{__ 'Required field'}}">*</abbr></label>
        </div>
        <div class="col-6">
            <input type="text" name="test-identifier" data-bind="identifier" data-validate="$notEmpty; $testIdFormat; $testIdAvailable(original={{identifier}});" />
        </div>
        <div class="col-1 help">
            <span class="icon-help" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
            <div class="tooltip-content">
            {{__ 'The principle identifier of the test.'}}
            </div>
        </div>
    </div>

<!-- assessmentTest/title -->
    <div class="grid-row">
        <div class="col-5">
            <label for="test-title">{{__ 'Title'}} <abbr title="{{__ 'Required field'}}">*</abbr></label>
        </div>
        <div class="col-6">
            <input type="text" name="test-title" data-bind="title" data-validate="$notEmpty" />
        </div>
        <div class="col-1 help">
            <span class="icon-help" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
            <div class="tooltip-content">
            {{__ 'The test title.'}}
            </div>
        </div>
    </div>

    <h4 class="toggler closed" data-toggle="~ .test-time-limits">{{__ 'Time Limits'}}</h4>

<!-- assessmentTest/timeLimits -->
    <div class="test-time-limits toggled">

{{!-- Property not yet available in delivery
<!--assessmentTest/timeLimits/minTime -->
        <div class="grid-row">
            <div class="col-5">
                <label for="test-min-time">{{__ 'Minimum Duration'}}</label>
            </div>
            <div class="col-6">
                <input type="text" name="test-min-time" value="00:00:00" data-duration="HH:mm:ss" data-bind="timeLimits.minTime" data-bind-encoder="time" />
            </div>
            <div class="col-1 help">
                <span class="icon-help" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
                <div class="tooltip-content">
                {{__ 'Minimum duration for the test.'}}
                </div>
            </div>
        </div>
--}}

<!-- assessmentTest/timeLimits/maxTime -->
        <div class="grid-row">
            <div class="col-5">
                <label for="test-max-time">{{__ 'Maximum Duration'}}</label>
            </div>
            <div class="col-6">
                <input type="text" name="max-time" value="00:00:00" data-duration="HH:mm:ss" data-bind="timeLimits.maxTime" data-bind-encoder="time" />
            </div>
            <div class="col-1 help">
                <span class="icon-help" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
                <div class="tooltip-content">
                {{__ 'Maximum duration for the all test.'}}
                </div>
            </div>
        </div>

<!-- assessmentTest/timeLimits/allowLateSubmission -->
        <div class="grid-row pseudo-label-box">
            <div class="col-5">
                {{__ 'Late submission allowed'}}
            </div>
            <div class="col-6">
                <label>
                    <input type="checkbox" name="test-allow-late-submission" value="true" data-bind="timeLimits.allowLateSubmission" data-bind-encoder="boolean" />
                    <span class="icon-checkbox"></span>
                </label>
            </div>
            <div class="col-1 help">
                <span class="icon-help" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
                <div class="tooltip-content">
                {{__ "Whether a candidate's response that is beyond the maximum duration should still be accepted."}}
                </div>
            </div>
        </div>
    </div>

</div>
