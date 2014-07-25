<div class="panel">
    <label>
        <input name="shuffle" type="checkbox" {{#if shuffle}}checked="checked"{{/if}}/>
        <span class="icon-checkbox"></span>
        {{__ "Shuffle choices"}}
    </label>
    <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content:first" data-tooltip-theme="info"></span>
    <span class="tooltip-content">
{{__ "If the shuffle attribute is true then the delivery engine will randomize the order in which the choices are initially presented.
        However each choice may be “shuffled” of “fixed” individually."}}
    </span>
</div>

<div class="panel">
    <h3>{{__ "Number of associations"}}</h3>

    <div>
        <label for="minAssociations" class="spinner">Min</label>
        <input name="minAssociations" value="{{minAssociations}}" data-increment="1" data-min="0" data-max="100" type="text" />
        <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
        <span class="tooltip-content">
            {{__ "The minimum number of choices that the candidate is required to select to form a valid response."}}
        </span>
    </div>
    <div>
        <label for="maxAssociations" class="spinner">Max</label>
        <input name="maxAssociations" value="{{maxAssociations}}" data-increment="1" data-min="0" data-max="100" type="text" />
        <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
        <span class="tooltip-content">
{{__ "The maximum number of choices that the candidate is allowed to select to form a valid response."}}
        </span>
    </div>
</div>