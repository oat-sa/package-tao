<infoControl {{{join attributes '=' ' ' '"'}}}>
    <{{ns.pic}}portableInfoControl infoControlTypeIdentifier="{{typeIdentifier}}" hook="{{entryPoint}}">

        <{{ns.pic}}resources location="http://imsglobal.org/pci/1.0.15/sharedLibraries.xml">
            <{{ns.pic}}libraries>
                {{#each libraries}}
                <{{../ns.pic}}lib id="{{.}}"/>
                {{/each}}
            </{{ns.pic}}libraries>
        </{{ns.pic}}resources>

        {{{portableElementProperties properties ns.pic}}}

        <{{ns.pic}}markup>
            {{{markup}}}
        </{{ns.pic}}markup>

    </{{ns.pic}}portableInfoControl>
</infoControl>