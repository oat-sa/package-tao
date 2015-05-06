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
</div>
