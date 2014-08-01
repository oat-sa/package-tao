<div class="panel">
    <label for="">{{__ "Identifier"}}</label>
    <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content:first" data-tooltip-theme="info"></span>
    <div class="tooltip-content">{{__ "The principle identifier of the item. This identifier must have a corresponding entry in the item's metadata."}}</div>

    <input type="text" 
           name="identifier" 
           value="{{identifier}}" 
           placeholder="e.g. my-item_123456" 
           data-validate="$notEmpty; $qtiIdentifier; $availableIdentifier(serial={{serial}});">
    
</div>

<div class="panel">
    <label>
        <input name="timeDependent" type="checkbox" {{#if timeDependent}}checked="checked"{{/if}}/>
        <span class="icon-checkbox"></span>
        {{__ "Time dependent"}}
    </label>
    <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content:first" data-tooltip-theme="info"></span>
    <span class="tooltip-content">
        {{__ "Define whether the item should be time dependent on delivery."}}
    </span>
</div>
