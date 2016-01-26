<div class="panel">

    
    <h3>{{__ "Interaction Background"}}</h3>

    <div class="panel">
        <label for="data">{{__ 'File'}}</label>
        <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content:first" data-tooltip-theme="info"></span>
        <div class="tooltip-content">{{__ 'The file path to the image.'}}</div>
        <input type="text" name="data" value="{{data}}" data-validate="$notEmpty; $fileExists(baseUrl={{baseUrl}})"/>
        <button class="btn-info small block" data-role="upload-trigger">{{__ 'Select image'}}</button>
    </div>

    <div class="panel media-sizer-panel">
        <!-- media sizer goes here -->
    </div>
    <input name="width" value="{{width}}" type="hidden" />
    <input name="height" value="{{height}}" type="hidden" />


    <input name="type" value="{{type}}" type="hidden" readonly />

    <hr />
    
    <h3>{{__ "Allowed associations"}}</h3>
        <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
        <span class="tooltip-content">
            {{__ 'The minimum number of associations that the candidate is required to make to form a valid response.'}}<br>
        {{__ 'The maximum number of associations that the candidate is allowed to make.'}}
        </span>


    <div class="panel">
        <div>
            <label for="minAssociations" class="spinner">{{__ 'Min'}}</label>
        <input name="minAssociations" value="{{minAssociations}}" data-increment="1" data-min="0" data-max="{{choicesCount}}" type="text" />
    </div>
        <div>
            <label for="maxAssociations" class="spinner">{{__ 'Max'}}</label>
        <input name="maxAssociations" value="{{maxAssociations}}" data-increment="1" data-min="0" data-max="{{choicesCount}}" type="text" />
        </div>
    </div>
</div>
