<div class="rubricblock-props props clearfix">

    <h3>{{__ 'Rubric Block'}}: {{index}}</h3>

 <!--assessmentTest/testPart/assessmentSection/sectionPart/rubrickBlocK/views -->
    <div class="grid-row">
        <div class="col-5">
            <label for="itemref-identifier">{{__ 'Views'}} <abbr title="{{__ 'Required field'}}">*</abbr></label>
        </div>
        <div class="col-6">
            <select name="view" multiple="multiple" data-bind="views">
                <option value="author">{{__ 'Author'}}</option>
                <option value="candidate">{{__ 'Candidate'}}</option>
                <option value="proctor">{{__ 'Proctor'}}</option>
                <option value="scorer">{{__ 'Scorer'}}</option>
                <option value="testConstructor">{{__ 'Test constructor'}}</option>
                <option value="tutor">{{__ 'Tutor'}}</option>
            </select>
        </div>
        <div class="col-1 help">
            <span class="icon-help" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span> 
            <div class="tooltip-content">
            {{__ 'Who can view the rubric block during the delivery.'}}
            </div>
        </div>
    </div>

</div>
