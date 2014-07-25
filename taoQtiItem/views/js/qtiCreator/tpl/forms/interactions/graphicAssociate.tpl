<div class="panel">

    
    <h3>{{__ "Interaction Background"}}</h3>

    <div class="panel">
        <label for="data">{{__ 'File'}}</label>
        <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content:first" data-tooltip-theme="info"></span>
        <div class="tooltip-content">{{__ 'The file path to the image.'}}</div>
        <input type="text" name="data" value="{{data}}" data-validate="$notEmpty; $fileExists(baseUrl={{baseUrl}})"/>
        <button class="btn-info small block" data-role="upload-trigger">{{__ 'Select image'}}</button>
    </div>

    <div class="panel">
        <label for="width">{{__ 'Width'}}</label>
        <input name="width" value="{{width}}" type="text" />
    </div>

    <div class="panel">
        <label for="height">{{__ 'Height'}}</label>
        <input name="height" value="{{height}}" type="text" />
    </div>

    <div class="panel">
        <label for="type">{{__ 'Mime Type'}}</label>
        <input name="type" value="{{type}}" type="text" />
    </div>
    
     <h3>{{__ "Allowed number of associations"}}
        <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
        <span class="tooltip-content">
            {{__ 'The minimum number of associations that the candidate is required to make to form a valid response.'}}<br>
            {{__ 'he maximum number of associations that the candidate is allowed to make.'}}
        </span>
    </h3>

    <div class="panel">
        <label for="minAssociations" class="spinner">Min</label>
        <input name="minAssociations" value="{{minAssociations}}" data-increment="1" data-min="0" data-max="{{choicesCount}}" type="text" />
    </div>

    <div class="panel">
        <label for="maxAssociations" class="spinner">Max</label>
        <input name="maxAssociations" value="{{maxAssociations}}" data-increment="1" data-min="0" data-max="{{choicesCount}}" type="text" />
    </div>
</div>
