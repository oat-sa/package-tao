<ul class="list-box flex-grid plain">
    {{#each list}}
    <li class="entry flex-col-{{#if width}}{{width}}{{else}}{{#if ../../width}}{{../../../width}}{{else}}12{{/if}}{{/if}}{{#if cls}} {{cls}}{{/if}}">
        <a class="block box" href="{{url}}">
            <h3 class="title">{{label}}</h3>
            {{#if content}}<div class="content clearfix">{{{content}}}</div>{{/if}}
            <div class="bottom clearfix">
                {{#if html}}<span class="text-html">{{{html}}}</span>{{/if}}
                {{#if text}}<span class="text-link"><span class="icon-play"></span>{{text}}</span>{{/if}}
            </div>
        </a>
    </li>
    {{/each}}
</ul>
