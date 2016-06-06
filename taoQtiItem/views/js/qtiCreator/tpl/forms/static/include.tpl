<div class="panel">
    <label for="href">{{__ 'File'}}</label>
    <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content:first" data-tooltip-theme="info"></span>
    <div class="tooltip-content">{{__ 'The file path to the shared stimulus.'}}</div>
    <input type="text" name="href" value="{{href}}" data-validate="$notEmpty;" readonly/>
    <button class="btn-info small block" data-role="upload-trigger">{{__ 'Select shared stimulus'}}</button>
</div>