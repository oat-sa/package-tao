<div class="panel">

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
    
    <h3>{{__ "Choice Image"}}</h3>

    <div class="panel">
        <label for="data">{{__ 'File'}}</label>
        <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content:first" data-tooltip-theme="info"></span>
        <div class="tooltip-content">{{__ 'The file path to the image.'}}</div>
        <input type="text" name="data" value="{{data}}" data-validate="$notEmpty; $fileExists(baseUrl={{baseUrl}})"/>
        <button class="btn-info small block" data-role="upload-trigger">{{__ 'Select image'}}</button>
    </div>

    <div>
        <label for="width">{{__ 'Width'}}</label>
        <input name="width" value="{{width}}" type="text" />
    </div>

    <div>
        <label for="height">{{__ 'Height'}}</label>
        <input name="height" value="{{height}}" type="text" />
    </div>

    <div>
        <label for="type">{{__ 'Mime type'}}</label>
        <input name="type" value="{{type}}" type="text" />
    </div>

    <hr>

    <h3>{{__ "Allowed number of matches"}}
        <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
        <span class="tooltip-content">
            {{__ 'The maximum number of choices this choice may be associated with.'}}<br>
            {{__ 'The minimum number of choices this choice must be associated with to form a valid response.'}}
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
</div>
