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
    <a href="#" data-context="latex" class="sidebar-popup-trigger" data-popup="~ .math-editor-container">{{__ "Display larger editor"}}</a>

    <div class="sidebar-popup math-editor-container latex two-fifty">
        <div class="sidebar-popup-title">
            <h3>{{__ "Latex"}}</h3>
            <a class="closer" href="#" title="{{__ 'Close'}}"></a>
        </div>
        <div class="sidebar-popup-content">
            <input data-for="latex"/>
        </div>
    </div>
</div>

<div class="panel sidebar-popup-container-box" data-role="mathml" style="display:none;">
    <label for="sidebar-mathml-field" class="has-icon">{{__ "MathML"}}</label>
    <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content:first" data-tooltip-theme="info"></span>
    <div class="tooltip-content">{{__ "Edit math expression using MathML"}}</div>

    <textarea id="sidebar-mathml-field" name="mathml">{{{mathml}}}</textarea>

    <div class="math-buffer" style="visibility:hidden;"></div>
    <a href="#" data-context="mathml" class="sidebar-popup-trigger" data-popup="~ .math-editor-container">{{__ "Display larger editor"}}</a>

    <div class="sidebar-popup math-editor-container mathml two-fifty">
        <div class="sidebar-popup-title">
            <h3>{{__ "MathML"}}</h3>
            <a class="closer" href="#" title="{{__ 'Close'}}"></a>
        </div>
        <div class="sidebar-popup-content">
            <textarea data-for="mathml"></textarea>
        </div>
    </div>

</div>

{{else}}
<div class="panel">
    {{__ "MathJax is not installed."}}
</div>
{{/if}}
