<div class="panel">
    <label for="" class="has-icon">{{__ "Identifier"}}</label>
    <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content:first" data-tooltip-theme="info"></span>
    <div class="tooltip-content">{{__ 'The identifier of the modal feedback. This identifier must not be used by any other modal feedback or item variable. An identifier is a string of characters that must start with a Letter or an underscore ("_") and contain only Letters, underscores, hyphens ("-"), period (".", a.k.a. full-stop), Digits, CombiningChars and Extenders.'}}</div>
    <input type="text" 
           name="identifier" 
           value="{{identifier}}" 
           placeholder="e.g. modal-feedback_1" 
           data-validate="$notEmpty; $qtiIdentifier; $availableIdentifier(serial={{serial}});">
</div>
<div class="panel">
    <label for="feedbackStyle" class="spinner">{{__ "Feedback Style"}}</label>
    <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content:first" data-tooltip-theme="info"></span>
    <span class="tooltip-content">{{__ "Select predefined feedback style from the list."}}</span>
    <select name="feedbackStyle" class="select2" data-has-search="false">
    	{{#each feedbackStyles}}
    		<option value="{{cssClass}}" {{#if selected}}selected="selected"{{/if}}>{{title}}</option>
    	{{/each}}
    </select>
</div>
