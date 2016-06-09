<div class="panel">
    <label>
        <input name="shuffle" type="checkbox" {{#if shuffle}}checked="checked"{{/if}}/>
        <span class="icon-checkbox"></span>
        {{__ "Shuffle choices"}}
    </label>
    <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content:first" data-tooltip-theme="info"></span>
    <span class="tooltip-content">
        {{__ 'If the shuffle attribute is true then the delivery engine will randomize the order in which the choices are initially presented.
        However each choice may be "shuffled" of "fixed" individually.'}}
    </span>
</div>

<div class="panel">
    <h3>{{__ "Allowed choices"}}</h3>

    <div>
        <label for="minChoices" class="spinner">Min</label>
        <input name="minChoices" value="{{minChoices}}" data-increment="1" data-min="0" data-max="{{choicesCount}}" type="text" />

        <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
        <span class="tooltip-content">
            {{__ 'The minimum number of choices that the candidate is required to select to form a valid response.'}}
        </span>
    </div>
    <div>
        <label for="maxChoices" class="spinner">Max</label>
        <input name="maxChoices" value="{{maxChoices}}" data-zero="true" data-increment="1" data-min="0" data-max="{{choicesCount}}" type="text" />

        <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
            <span class="tooltip-content">
            {{__ 'The maximum number of choices that the candidate is allowed to select to form a valid response.'}}
        </span>
    </div>
</div>



<div class="panel">
    <h3>{{__ "List Style"}}</h3>
    <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
            <span class="tooltip-content">
            {{__ 'Use this if you want the list of choices to be prefixed (e.g. 1,2,3 a,b,c)'}}
        </span>

    <select data-list-style/>
</div>


<div class="panel">
    <h3>{{__ 'Orientation'}}</h3>
    <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
            <span class="tooltip-content">
            {{__ 'Display the choices either horizontaly or verticaly'}}
        </span>
    <div>
        <label class="smaller-prompt">
            <input type="radio" name="orientation" value="vertical" {{#unless horizontal}}checked{{/unless}} />
            <span class="icon-radio"></span>
            {{__ 'Vertical'}}
        </label>
        <br>
        <label class="smaller-prompt">
            <input type="radio" name="orientation" value="horizontal" {{#if horizontal}}checked{{/if}} />
            <span class="icon-radio"></span>
            {{__ 'Horizontal'}}
        </label>
    </div>
</div>
