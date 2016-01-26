<div class="listbox">
    <h1 {{#unless title}}class="hidden"{{/unless}}>{{title}}</h1>
    <h2>
        <span class="empty-list{{#unless textEmpty}} hidden{{/unless}}">{{textEmpty}}</span>
        <span class="available-list{{#unless textNumber}} hidden{{/unless}}"><span class="label">{{textNumber}}</span>: <span class="count"></span></span>
        <span class="loading{{#unless textLoading}} hidden{{/unless}}"><span>{{textLoading}}</span>...</span>
    </h2>
    <div class="list"></div>
</div>
