<div id="item-editor-math-editor" class="sidebar-popup-container-box">
    <div id="math-editor-container" class="sidebar-popup">
        <h3 id="math-editor-title">{{__ "Title"}}</h3>
        <span class="icon-grip-h"></span>
        <textarea id="math-editor-textarea"></textarea>
        <a class="closer" href="#" data-close="#math-editor-container"></a>
    </div>
    <div class="clearfix">

        <!-- this would be the content of static/math.tpl -->
        <label>{{__ "Label"}}</label>
        <input name="latex" id="math-inp" value="" type="text">
        <span>…</span>

    </div>
</div>
