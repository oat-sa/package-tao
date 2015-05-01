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
    <label>
        <input name="required" type="checkbox" {{#if required}}checked="checked"{{/if}}/>
        <span class="icon-checkbox"></span>
        {{__ "required"}}
    </label>
    <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content:first" data-tooltip-theme="info"></span>
    <span class="tooltip-content">
{{__ "Define whether a choice must be selected by the candidate in order to form a valid response to the interaction."}}
    </span>
</div>
