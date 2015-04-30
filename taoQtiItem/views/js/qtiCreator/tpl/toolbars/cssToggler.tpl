{{#each stylesheets}}
<li data-css-res="{{path}}">
    <span class="icon-preview style-sheet-toggler" title="{{title}}"></span>
    <span class="file-label truncate" title="{{editLabelTxt}}">{{label}}</span>
    <input type="text" class="style-sheet-label-editor" value="{{label}}">
    <span class="icon-bin" title="{{deleteTxt}}" data-role="css-delete"></span>
    <span class="icon-download" title="{{downloadTxt}}" data-role="css-download"></span>
</li>
{{/each}}