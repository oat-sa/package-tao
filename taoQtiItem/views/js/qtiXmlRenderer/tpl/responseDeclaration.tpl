<responseDeclaration {{{join attributes '=' ' ' '"'}}}
{{~#if empty~}}/>
{{~else~}}>
    {{~#if defaultValue.length}}
    <defaultValue>
        {{~#each defaultValue}}
        <value>{{.}}</value>
        {{/each}}
    </defaultValue>
    {{/if}}
    {{~#if correctResponse.length}}
    <correctResponse>
        {{~#each correctResponse}}
        <value>{{.}}</value>
        {{~/each}}
    </correctResponse>
    {{/if}}
    {{~#if MAP_RESPONSE}}
        {{~#if mapEntries~}}
        <mapping{{#each mappingAttributes}} {{@key}}="{{.}}"{{/each}}>
            {{~#each mapEntries}}
            <mapEntry mapKey="{{@key}}" mappedValue="{{.}}" caseSensitive="false"/>
            {{~/each}}
        </mapping>
        {{/if}}
    {{/if}}
    {{~#if MAP_RESPONSE_POINT}}
        {{~#if mapEntries~}}
        <areaMapping{{#each mappingAttributes}} {{@key}}="{{.}}"{{/each}}>
            {{~#each mapEntries}}
            <areaMapEntry shape="{{shape}}" coords="{{coords}}" mappedValue="{{mappedValue}}" />
            {{~/each}}
        </areaMapping>
        {{/if}}
    {{/if}}
    </responseDeclaration>
{{~/if}}