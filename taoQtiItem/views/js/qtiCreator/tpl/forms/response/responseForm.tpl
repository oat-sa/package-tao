<div class="panel">
    <label for="" class="has-icon">{{__ "Response identifier"}}</label>
    <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content:first" data-tooltip-theme="info"></span>
    <div class="tooltip-content">{{__ 'The identifier of the choice. This identifier must not be used by any other response or item variable. An identifier is a string of characters that must start with a Letter or an underscore ("_") and contain only Letters, underscores, hyphens ("-"), period (".", a.k.a. full-stop), Digits, CombiningChars and Extenders.'}}</div>

    <input type="text" 
           name="identifier" 
           value="{{identifier}}" 
           placeholder="e.g. RESPONSE" 
           data-validate="$notEmpty; $qtiIdentifier; $availableIdentifier(serial={{serial}});">
</div>

<div class="panel">
    <label for="" class="has-icon">{{__ "Response processing"}}</label>
    <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content:first" data-tooltip-theme="info"></span>
    <div class="tooltip-content">{{__ "Select the way the response of your interaction should be processed"}}</div>

    <select name="template" class="select2" data-has-search="false">
        {{#each templates}}
        <option value="{{@key}}">{{.}}</option>
        {{/each}}
    </select>
</div>

{{#if editMapping}}
<hr/>

<div class="panel" data-editx="map">
    <h3>{{__ "Score range"}}
    </h3>
    <div>
        <label for="lowerBound" class="spinner">Min</label>
        <input name="lowerBound" value="{{lowerBound}}" class="score" type="text" data-validate="$numeric" data-validate-option="$allowEmpty; $event(type=keyup)" />
        <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
        <span class="tooltip-content">{{__ "Minimal  score for this interaction."}}</span>
    </div>
    <div>
        <label for="upperBound" class="spinner">Max</label>
        <input name="upperBound" value="{{upperBound}}" class="score" type="text" data-validate="$numeric" data-validate-option="$allowEmpty; $event(type=keyup)" />
        <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
        <span class="tooltip-content">{{__ "Maximal score for this interaction."}}</span>
    </div>
</div>

<div class="panel" data-editx="map">
    <label for="defaultValue" class="spinner">{{__ "Mapping default"}}</label>
    <input name="defaultValue" value="{{defaultValue}}" class="score" type="text" data-validate="$notEmpty; $numeric;" data-validate-option="$lazy; $event(type=keyup)" />
    <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content:first" data-tooltip-theme="info"></span>
    <span class="tooltip-content">{{__ "The default value from the target set to be used when no explicit mapping for a source value is given."}}</span>
</div>

<div class="panel" data-editx="map">
    <label>
        <input name="defineCorrect" type="checkbox" data-role="defineCorrect"{{#if defineCorrect}} checked="checked"{{/if}} />
        <span class="icon-checkbox"></span>
        {{__ "Define correct response"}}
    </label>
    <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content:first" data-tooltip-theme="info"></span>
    <span class="tooltip-content">{{__ "Define the correct response."}}</span>
</div>
{{/if}}

{{#if editFeedbacks}}
<hr/>

<div class="panel feedbackRule-panel"></div>
{{/if}}
