
<div class="panel">
    <label for="">{{__ "identifier"}}</label>
    <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content:first" data-tooltip-theme="info"></span>
    <div class="tooltip-content">{{__ 'The identifier of the choice. This identifier must not be used by any other choice or item variable. An identifier is a string of characters that must start with a Letter or an underscore ("_") and contain only Letters, underscores, hyphens ("-"), period (".", a.k.a. full-stop), Digits, CombiningChars and Extenders.'}}</div>

    <input type="text" 
           name="identifier" 
           value="{{identifier}}" 
           placeholder="e.g. my-hotspot_1" 
           data-validate="$notEmpty; $qtiIdentifier; $availableIdentifier(serial={{serial}});">
</div>

<h3>{{__ "Allowed number of matches"}}
    <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
    <span class="tooltip-content">
        {{__ 'The minimum number of choices this choice must be associated with to form a valid response.'}}<br>
        {{__ 'The maximum number of choices this choice may be associated with.'}}
    </span>
</h3>

<div>
    <label for="matchMin" class="spinner">Min</label>
    <input name="matchMin" value="{{matchMin}}" data-increment="1" data-min="0" data-max="{{choicesCount}}" type="text" />
</div>
<div>
    <label for="matchMax" class="spinner">Max</label>
    <input name="matchMax" value="{{matchMax}}" data-increment="1" data-min="0" data-max="{{choicesCount}}" type="text" />
</div>

<h3>{{__ "Shape position"}}</h3>

<div class="panel">
    <label for="x">{{__ 'Left'}}</label>
    <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content:first" data-tooltip-theme="info"></span>
    <div class="tooltip-content">{{__ "The left position of the shape relative to the top left corner of the background in pixels. This value may be scaled when the background image size is adapted to it's container"}}</div>
    <input name="x" value="{{x}}" type="text" readonly />
</div>

<div class="panel">
    <label for="y">{{__ 'Top'}}</label>
    <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content:first" data-tooltip-theme="info"></span>
    <div class="tooltip-content">{{__ "The top position of the shape relative to the top left corner of the background in pixels. This value may be scaled when the background image size is adapted to it's container"}}</div>
    <input name="y" value="{{y}}" type="text" readonly />
</div>

<div class="panel">
    <label for="width">{{__ 'Width'}}</label>
    <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content:first" data-tooltip-theme="info"></span>
    <div class="tooltip-content">{{__ "The shape width in pixels. This value may be scaled when the background image size is adapted to it's container"}}</div>
    <input name="width" value="{{width}}" type="text" readonly />
</div>

<div class="panel">
    <label for="height">{{__ 'Height'}}</label>
    <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content:first" data-tooltip-theme="info"></span>
    <div class="tooltip-content">{{__ "The shape height in pixels. This value may be scaled when the background image size is adapted to it's container"}}</div>
    <input name="height" value="{{height}}" type="text" readonly />
</div>


