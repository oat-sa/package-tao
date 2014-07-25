<div class="panel">
    <label for="">{{__ "Identifier"}}</label>
    <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content:first" data-tooltip-theme="info"></span>
    <div class="tooltip-content">{{__ 'The identifier of the choice. This identifier must not be used by any other choice or item variable. An identifier is a string of characters that must start with a Letter or an underscore ("_") and contain only Letters, underscores, hyphens ("-"), period (".", a.k.a. full-stop), Digits, CombiningChars and Extenders.'}}</div>

    <input type="text" 
           name="identifier" 
           value="{{identifier}}" 
           placeholder="e.g. my-choice_1" 
           data-validate="$notEmpty; $qtiIdentifier; $availableIdentifier(serial={{serial}});">
</div>

<div class="panel">
    <h3 class="has-icon">{{__ "Allowed number of uses"}}
    </h3>
    <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
    <span class="tooltip-content">
        {{__ "The maximum number of choices this choice may be associated with. If matchMax is 0 there is no restriction."}}
    </span>

    <!--not supported yet-->
    <div style="display:none;">
        <label for="matchMin" class="spinner">{{__ "Min"}}</label>
        <input name="matchMin" value="{{matchMin}}" data-increment="1" data-min="0" data-max="100" type="text" />
    </div>

    <div>
        <label for="matchMax" class="spinner">{{__ "Max"}}</label>
        <input name="matchMax" value="{{matchMax}}" data-increment="1" data-min="0" data-max="100" type="text" />
    </div>
</div>
