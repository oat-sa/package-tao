<div class="panel">
    <label for="src">{{__ 'File'}}</label>
    <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content:first" data-tooltip-theme="info"></span>
    <div class="tooltip-content">{{__ 'The file path to the media.'}}</div>
    <input type="text" name="data" value="{{data}}" data-validate="$notEmpty; $fileExists(baseUrl={{baseUrl}})"/>
    <button class="btn-info small block" data-role="upload-trigger">{{__ 'Select media'}}</button>
</div>

<div data-role="advanced" style="display:none">
    <div class="panel">
        <h3>{{__ 'Size and position'}}</h3>

        <!--not available yet-->
        <!--    <label>
        <input name="responsive" type="checkbox" />
        <span class="icon-checkbox"></span>
        {{__ 'Adapt to item size'}}
    </label>
    <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content:first" data-tooltip-theme="info"></span>
    <span class="tooltip-content">
        Recommended.
        Define whether the image size should automatically adapt to item size.
        If this option is active, the image width and height will be a percentage of its text container.
    </span>-->

        <p class="img-resizer-slider"></p>

        <label for="height">{{__ 'Height (optional)'}}</label>
        <span id="item-editor-font-size-manual-input" class="item-editor-unit-input-box">
            <input type="text" name="height" value="{{height}}" data-validate="$numeric" data-validate-option="$allowEmpty;"/>
            <span class="unit-indicator">px</span>
        </span>
    </div>
    <div class="panel">

        <label for="width">{{__ 'Width (optional)'}}</label>
        <span id="item-editor-font-size-manual-input" class="item-editor-unit-input-box">
            <input type="text" name="width" value="{{width}}" data-validate="$numeric" data-validate-option="$allowEmpty;"/>
            <span class="unit-indicator">px</span>
        </span>
    </div>
    <div class="panel">

        <label for="align">{{__ "Alignment (optional)"}}</label>
        <select name="align" class="select2" data-has-search="false">
            <option value="default">{{__ 'default'}}</option>
            <option value="left">{{__ 'left'}}</option>
            <option value="right">{{__ 'right'}}</option>
        </select>
    </div>
</div>