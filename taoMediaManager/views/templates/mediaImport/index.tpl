<div class="main-container flex-container-main-form">
    <h2><?=__('Upload Media')?></h2>
    <div class="form-content">
        <form id="alt-form">
            <select name="lang">
                <?php foreach(get_data('lang') as $uri=>$label):?>
                    <option value="<?=$uri?>"><?=$label?></option>
                <?php endforeach;?>
            </select>
            <input type="hidden" name="classUri" value="<?=get_data('class')?>">
        </form>
        <div class="xhtml_form">
            <div id="upload-container" data-url="<?=_url('import','MediaImport');?>"></div>
        </div>
    </div>
</div>