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
        {{~#if ../isRecord}}
        {{~#if value}}
        <value fieldIdentifier="{{fieldIdentifier}}" baseType="{{baseType}}"><![CDATA[{{{value}}}]]></value>
        {{/if}}
        {{else}}
        <value><![CDATA[{{{.}}}]]></value>
        {{/if}}
        {{~/each}}
    </correctResponse>
    {{/if}}
    {{~#if isAreaMapping}}
        {{~#if hasMapEntries~}}
        <areaMapping{{#each mappingAttributes}} {{@key}}="{{.}}"{{/each}}>
            {{~#each mapEntries}}
            <areaMapEntry shape="{{shape}}" coords="{{coords}}" mappedValue="{{mappedValue}}" />
            {{~/each}}
        </areaMapping>
        {{/if}}
    {{~else~}}
        {{~#if hasMapEntries~}}
        <mapping{{#each mappingAttributes}} {{@key}}="{{.}}"{{/each}}>
            {{~#each mapEntries}}
            <mapEntry mapKey="{{@key}}" mappedValue="{{.}}" caseSensitive="false"/>
            {{~/each}}
        </mapping>
        {{/if}}
    {{/if}}
    </responseDeclaration>
{{~/if}}
