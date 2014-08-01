define([
    'taoQtiItem/qtiCreator/model/pciCreatorContext'
], function(pciCreatorContext){

    var pciCreator = {
        registerPciModel : function(pciModel){

        },
        getPciInstance : function(interaction){

            var pciTypeIdentifier,
                pci = interaction.data('pci') || undefined;

            if(!pci){

                pciTypeIdentifier = interaction.typeIdentifier;
                pci = pciCreatorContext.getPci(pciTypeIdentifier);

                if(pci){

                    //two-way binding for pci hook and tao interaction
                    interaction.data('pci', pci);
                    pci._taoCustomInteractionCreator = interaction;

                }else{
                    throw 'no custom interaction hook found for the type ' + pciTypeIdentifier;
                }
            }

            return pci;

        }

    };

    return pciCreator;
});