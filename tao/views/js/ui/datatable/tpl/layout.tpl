<div class="grid-row">
    <div class="col-6">{{__ 'Page'}} <strong>{{page}}</strong> {{__ 'of'}} {{total}}</div>
    <div class="col-6 txt-rgt">
        <button class="btn-info small datatable-backward"><span class="icon-backward"></span>{{__ 'Previous'}}</button>
        <button class="btn-info small datatable-forward">{{__ 'Next'}}<span class="icon-forward r"></span></button>
    </div>
</div>
<div class="datatable-container">
    <table class="matrix datatable">
        <colgroup>
            <col/>
            {{#model}}
            <col/>
            {{/model}}
        </colgroup>
        <thead>
            <tr>
                <th class="id"></th>
            {{#model}}
                <th {{#if sortable}}data-sort-by="{{id}}"{{/if}}>{{label}}</th>
            {{/model}}
            </tr>
        </thead>
        <tbody>
            {{#data}}
                <tr>
                    {{#each this}}
                    <td class="{{@key}}">{{this}}</td>
                    {{/each}}
                    {{#if ../actions}}
                    <td data-item-identifier="{{id}}">
                        {{#each ../../actions}}

                        <button class="btn-info small {{this}}"><span class="icon-{{this}}"></span> {{this}}</button>

                        {{/each}}
                    </td>
                    {{/if}}
                </tr>
            {{/data}}
        </tbody>
    </table>
</div>
<div class="grid-row" style="margin-top:20px;">
    <div class="col-6">{{__ 'Page'}} <strong>{{page}}</strong> {{__ 'of'}} {{total}}</div>
    <div class="col-6 txt-rgt">
        <button class="btn-info small datatable-backward"><span class="icon-backward"></span>{{__ 'Previous'}}</button>
        <button class="btn-info small datatable-forward">{{__ 'Next'}}<span class="icon-forward r"></span></button>
    </div>
</div>
