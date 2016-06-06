<div class="panel">
    <label for="src">{{__ 'File'}}</label>
    <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content:first" data-tooltip-theme="info"></span>
    <div class="tooltip-content">{{__ 'The file path to the image.'}}</div>
    <input type="text" name="src" value="{{src}}" data-validate="$notEmpty; $fileExists(baseUrl={{baseUrl}})"/>
    <button class="btn-info small block" data-role="upload-trigger">{{__ 'Select image'}}</button>
</div>

<div class="panel">
    <label for="alt">{{__ "Alt Text"}}</label>
    <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content:first" data-tooltip-theme="info"></span>
    <div class="tooltip-content">{{__ 'The text to be displayed if the image is not available.'}}</div>
    <input type="text" name="alt" value="{{alt}}" placeholder="e.g. House with a garden"/>
</div>

<div class="change-alt-modal-feedback modal">
    <div class="modal-body clearfix">
        <p>{{__ "Would you like to replace the alt text"}}</p>
        <p class="alt-text"></p>

        <div class="rgt">
            <button class="btn-regular small cancel" type="button">{{__ "No"}}</button>
            <button class="btn-info small save" type="button">{{__ "Yes"}}</button>
        </div>
    </div>
</div>

<div data-role="advanced" style="display:none">
    
    <hr/>
    
<!--    <div class="panel">
        <h3>{{__ 'Size and position'}}</h3>

        <p class="img-resizer-slider"></p>

        <label for="height">{{__ 'Height'}}</label>
        <span id="item-editor-font-size-manual-input" class="item-editor-unit-input-box">
            <input type="text" name="height" value="{{height}}" data-validate="$numeric" data-validate-option="$allowEmpty;"/>
            <span class="unit-indicator">px</span>
        </span>
    </div>
    <div class="panel">

        <label for="width">{{__ 'Width'}}</label>
        <span id="item-editor-font-size-manual-input" class="item-editor-unit-input-box">
            <input type="text" name="width" value="{{width}}" data-validate="$numeric" data-validate-option="$allowEmpty;"/>
            <span class="unit-indicator">px</span>
        </span>
    </div>
    <div class="panel">

        <label for="align">{{__ "Alignment"}}</label>
        <select name="align" class="select2" data-has-search="false">
            <option value="default">{{__ 'default'}}</option>
            <option value="left">{{__ 'left'}}</option>
            <option value="right">{{__ 'right'}}</option>
        </select>
    </div>-->
    
    <h3>{{__ 'Size and position'}}</h3>
    <div class="panel img-resizer"></div>
    
</div>