<div class="test-props props clearfix">
    
    <!-- test properties --> 
    <h3>{{__ 'Test Properties'}}</h3>

<!-- assessmentTest/identifier -->
    <div class="grid-row">
        <div class="col-5">
            <label for="test-identifier">{{__ 'Identifier'}} <abbr title="{{__ 'Required field'}}">*</abbr></label>
        </div>
        <div class="col-6">
            <input type="text" name="test-identifier" data-bind="identifier" />
        </div>
        <div class="col-1 help">
           <span class="icon-help" data-tooltip="~ .help-content"></span> 
            <div class="help-content">
            {{__ "I'm the principle identifier of the test."}}
            </div>
        </div>
    </div>
</div>
