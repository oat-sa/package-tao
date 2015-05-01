<customInteraction {{{join attributes '=' ' ' '"'}}}>
    <{{ns.pci}}portableCustomInteraction customInteractionTypeIdentifier="{{typeIdentifier}}" hook="{{entryPoint}}">

        <{{ns.pci}}responseSchema href="http://imsglobal.org/schema/json/v1.0/response.json"/>

        <{{ns.pci}}resources location="http://imsglobal.org/pci/1.0.15/sharedLibraries.xml">
            <{{ns.pci}}libraries>
                {{#each libraries}}
                <{{../ns.pci}}lib id="{{.}}"/>
                {{/each}}
            </{{ns.pci}}libraries>
        </{{ns.pci}}resources>

        {{{portableElementProperties properties ns.pci}}}

        <{{ns.pci}}markup>
            {{{markup}}}
        </{{ns.pci}}markup>

    </{{ns.pci}}portableCustomInteraction>
</customInteraction>