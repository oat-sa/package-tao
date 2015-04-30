<?php
use oat\tao\helpers\Template;
?>
<link rel="stylesheet" href="<?= BASE_WWW ?>css/migration.css" />

<div id="resultStorageMigration" class="flex-container-full">
    
   <div class="grid-row ">
        <h2 class="col-12"><?=__('Results Data Migration Tool')?></h2>
   </div>
   <div class="grid-row">
   <div class="col-5" id="sourceStorage">
        <h3><?=__('Source (Readable Storage)')?></h3>
            <?php
                foreach (get_data('availableRStorage') as $storage) :
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
            <?php
                endforeach;
            ?>
           
        
    </div>   
    <div class="col-2" id="operations">
                
         <!--<div class="btn-warning opButton" id="migrate"><?=__('Migrate Data')?></div>-->
         <button class="btn-info opButton" id="clone"><?=__('Clone Data')?></button>
               
        
    </div>
    
    <div class="col-5" id="targetStorage">
        <h3><?=__('Target (Writable Storage)')?></h3>

         <?php
                foreach (get_data('availableWStorage') as $storage) :
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
            <?php
                endforeach;
            ?>
    </div>
    </div ><!--//grid row!-->
    <div id="migrationProgress" title="Data Migration">
        <div class="migrationInfo">
            
            <h3><?=__('Operation')?></h3>
            <div id="selOperation"></div>
            
            <h3><?=__('Source')?></h3>
            <div id="selSource"></div>
            
            <h3><?=__('Target')?></h3>
            <div id="selTarget"></div>
            
        </div>
        <div class="migrationProgress">
            <span><?=__('Migration in progress, please wait...')?></span>
        </div>
    </div>
</div>
