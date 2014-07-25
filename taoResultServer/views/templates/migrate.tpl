
<link rel="stylesheet" type="text/css" media="screen" href="<?=TAOBASE_WWW?>css/style.css"/>
<link rel="stylesheet" type="text/css" media="screen" href="<?=TAOBASE_WWW?>css/layout.css"/>

<link rel="stylesheet" href="<?= BASE_WWW ?>css/migration.css" />

<script type="text/javascript">
requirejs.config({
    config: {}
});
</script>

<div class="tao-scope" id="resultStorageMigration">
    
   <div class="grid-row ">
        <h2 class="col-12"><?=__('Results Data Migration Tool')?></h2>
   </div>
   <div class="grid-row">
   <div class="col-5" id="sourceStorage">
        <h3><?=__('Source (Readable Storage)')?></h3>
            <?
                foreach (get_data('availableRStorage') as $storage) {
            ?>
                <div>
                    <label>
                        <input type="checkbox" id="source<?=$storage->getUri()?>" value="<?=$storage->getUri()?>">
                        <span class="icon-checkbox"></span>
                        <?=$storage->getLabel()?>
                    </label>
                    <!--
                    <input type="checkbox" name="source" id="source<?=$storage->getUri()?>" value="<?=$storage->getUri()?>"  />
                    <label for="source<?=$storage->getUri()?>"><?=$storage->getLabel()?></label>
                    !-->
                </div>
            <?
                }
            ?>
           
        
    </div>   
    <div class="col-2" id="operations">
                
         <!--<div class="btn-warning opButton" id="migrate"><?=__('Migrate Data')?></div>-->
         <div class="btn-button opButton" id="clone"><?=__('Clone Data')?></div>
               
        
    </div>
    
    <div class="col-5" id="targetStorage">
        <h3><?=__('Target (Writable Storage)')?></h3>

         <?
                foreach (get_data('availableWStorage') as $storage) {
            ?>
                <div>
                     <label>
                        <input type="checkbox" id="target<?=$storage->getUri()?>" value="<?=$storage->getUri()?>">
                        <span class="icon-checkbox"></span>
                        <?=$storage->getLabel()?>
                    </label>
                    <!--
                    <input type="checkbox" name="target" id="target<?=$storage->getUri()?>" value="<?=$storage->getUri()?>" />
                    <label for="target<?=$storage->getUri()?>"><?=$storage->getLabel()?></label>
                    -->
                </div>
            <?
                }
            ?>
    </div>
       <div id="feedback">
           <div class="feedback-success">
                <span class="icon-success"></span>Migration Successful
           </div>
           
       </div>
    </div ><!--//grid row!-->
    <div id="migrationProgress" title="Data Migration">

        <h3><?=__('Operation')?></h3>
        <div id='selOperation'/>
        
        <h3><?=__('Source')?></h3>
        <div id='selSource'/>
        
        <h3><?=__('Target')?></h3>
        <div id='selTarget'/>
     
    </div>
</div>
