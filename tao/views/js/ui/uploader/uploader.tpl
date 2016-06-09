<div class="file-upload grid-row">
    <span class="btn-info btn-browse small col-5"><span class="icon-folder-open"></span>{{__ 'Browse...'}}</span>
    <span class="file-name col-7 truncate placeholder">{{__ 'No file selected'}}</span>
    <input type="file" name="{{inputName}}" {{#if multiple}}multiple{{/if}}>
</div>
<!--<div class="grid-row" style="display:none;">-->
<!--<ul class="file-list"></ul>-->
<!--</div>-->
<div class="grid-row">
    <div class="file-drop col-12" data-drop-msg="{{__ 'Drop the files to upload'}}">
        <div class="dragholder">
            ~ {{__ 'or'}} ~
            <br/>
            {{#if multiple}}
            {{__ 'Drag files here'}}
            {{else}}
            {{__ 'Drag file here'}}
            {{/if}}
        </div>
    </div>
</div>
<div class="grid-row">
    <div class="progressbar col-12"></div>
    <br/>
</div>
<div class="grid-row">
    {{#if showResetButton}}
    <button type="button" class="btn-info btn-reset small"><span class="icon-eraser"></span>{{__ 'Reset'}}</button>
    {{/if}}
    {{#if showUploadButton}}
    <button class="btn-success btn-upload small"><span class="icon-upload"></span>{{uploadBtnText}}</button>
    {{/if}}
</div>