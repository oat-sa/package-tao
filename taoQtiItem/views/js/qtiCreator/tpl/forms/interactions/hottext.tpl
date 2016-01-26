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
        <label for="maxChoices" class="spinner">{{__ 'Max'}}</label>
        <input name="maxChoices" value="{{maxChoices}}" data-increment="1" data-min="0" data-max="{{choicesCount}}" type="text" />

        <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
            <span class="tooltip-content">
            {{__ 'The maximum number of choices that the candidate is allowed to select to form a valid response.'}}
        </span>
    </div>
</div>
