{{#if mathjax}}

<div class="panel">
    <label for="display" class="has-icon">{{__ "Display"}}</label>
    <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
    <div class="tooltip-content">{{__ "How the math expression should be displayed."}}</div>

    <select name="display" class="select2" data-has-search="false">
        <option value="inline">{{__ "inline"}}</option>
        <option value="block">{{__ "block"}}</option>
    </select>
</div>

<div class="panel">
    <label for="editMode" class="has-icon">{{__ "Editing Mode"}}</label>
    <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
    <div class="tooltip-content">{{__ "How the math expression should be edited"}}.</div>

    <select name="editMode" class="select2" data-has-search="false">
        <option value="latex">{{__ "LaTex"}}</option>
        <option value="mathml">{{__ "MathML"}}</option>
    </select>

</div>

<div class="panel" data-role="latex" style="display:none;">
    <label for="sidebar-latex-field" class="has-icon">{{__ "Latex"}}</label>
    <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content:first" data-tooltip-theme="info"></span>
    <div class="tooltip-content">{{__ "Edit math expression using LaTex type setting system, e.g. e^{i \pi} = -1"}}</div>

    <input id="sidebar-latex-field" type="text" name="latex" value="{{latex}}" placeholder="e.g. e^{i \pi} = -1"/>
    <a href="#latex" class="math-editor-trigger">{{__ "Display larger editor"}}</a>
</div>

<div class="panel sidebar-popup-container-box" data-role="mathml" style="display:none;">
    <label for="sidebar-mathml-field" class="has-icon">{{__ "MathML"}}</label>
    <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content:first" data-tooltip-theme="info"></span>
    <div class="tooltip-content">{{__ "Edit math expression using MathML"}}</div>

    <textarea id="sidebar-mathml-field" name="mathml">{{{mathml}}}</textarea>

    <div class="math-buffer" style="visibility:hidden;"></div>
    <a href="#mathml" class="math-editor-trigger">{{__ "Display larger editor"}}</a>
</div>

<div class="sidebar-popup-container-box">    

    <div id="math-editor-container" class="sidebar-popup">
        <h3 id="math-editor-title"></h3>
        <span class="icon-grip-h dragger"></span>
        <textarea id="math-editor-textarea" data-target="mathml"></textarea>
        <input id="math-editor-input" data-target="latex"/>
        <a class="closer" href="#" title="{{__ 'Close'}}"></a>
    </div>
    <div class="math-buffer" style="visibility:hidden;"></div>
</div>
{{else}}
<div class="panel">
    {{__ "MathJax is not installed."}}
</div>
{{/if}}
