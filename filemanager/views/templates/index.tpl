<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?=__('Media Manager')?></title>
        <link rel="shortcut icon" href="<?=BASE_WWW?>img/favicon.ico" type="image/x-icon" />
        <base href="<?=BASE_WWW?>" />

        <?=tao_helpers_Scriptloader::render()?>

        <script type='text/javascript'>
            var root_url = "<?=ROOT_URL?>";
            var baseUrl = "<?=BASE_URL?>";
            var basePath = "<?=addslashes(BASE_PATH);?>";
            var baseData = "<?=addslashes(BASE_DATA);?>";
            var urlData = "<?=URL_DATA?>";

            var openFolder = '/';

<?if(get_data("openFolder")):?>
                openFolder = "<?=get_data('openFolder')?>";
<?endif?>

            var runner = window.top.opener.fmRunner.single;
            runner.urlData = urlData;
            runner.mediaData = {};
        </script>
    </head>
    <body>
        <div id="header" class="ui-widget-header ui-corner-all"><?=__('Media Manager')?></div>
        <div id="main-container">
            <div id="file-browser">
                <div id="file-container-title" class="ui-state-default ui-corner-top" ><?=__('File Browser')?></div>
                <div id="file-container"></div>
            </div>

            <div id="file-data-container">
                <?if(get_data('error')):?>
                    <div class="ui-widget ui-corner-all ui-state-error error-message">
                        <?=urldecode(get_data('error'))?>
                    </div>
                <?endif?>
                <div class="ui-state-highlight ui-corner-all">
                    <strong><?=__('Current directory')?></strong>:
                    <span id="dir-uri" class="data-container"><?=(get_data("openFolder") ? get_data("openFolder") : '/')?></span>
                </div>
                <div class="ui-state-highlight ui-corner-all" id="url">
                    <strong><?=__('URL')?></strong>:
                    <span id="file-url" class="data-container"></span>
                    <span id="file-uri" style="display:none;"></span>
                </div>
                <div class="ui-state-highlight ui-corner-all">
                    <strong><?=__('Preview')?></strong>
                    <div id="file-preview" style="text-align:center;"></div>
                </div>
                <div class="ui-state-highlight ui-corner-all">
                    <strong><?=__('Actions')?></strong>
                    <ul id="actions">
                        <?if(get_data('showSelect') == true):?>
                            <li class="ui-corner-all"><a class="link select disabled" href="#"><?=__('Select')?></a></li>
                        <?endif;?>
                        <li class="ui-corner-all"><a class="link root disabled" href="#"><?=__('Root')?></a></li>
                        <li class="ui-corner-all"><a class="link new-dir" href="#"><?=__('New directory')?></a></li>
                        <li class="ui-corner-all"><a class="link download disabled" href="#"><?=__('Download')?></a></li>
                        <li class="ui-corner-all"><a class="link delete disabled" href="#"><?=__('Delete')?></a></li>
                    </ul>
                </div>
                <div class="ui-widget-content ui-corner-all">
                    <strong><?=__('File upload')?></strong><br /><br />
                    <form enctype='multipart/form-data' action="<?=ROOT_URL?>/filemanager/Browser/fileUpload" method="post">
                        <input id="media_folder" type="hidden" name="media_folder" value="<?=(get_data("openFolder") ? get_data("openFolder") : '/')?>" />
                        <input type="hidden" name="MAX_FILE_SIZE" value="<?=get_data('upload_limit')?>" />
                        <span><?=__('Max filesize')?> <?=round(get_data('upload_limit') / 1048576, 1)?><?=__(' MB')?></span><br /><br />
                        <span class="form-label"><?=__('File')?></span><input id="media_file" type="file" name="media_file" /><br />
                        <span class="form-label"><?=__('Name')?></span><input id="media_name" type="text" name="media_name" /><br />
                        <input id="button-submit" type="submit" value="<?=__('Upload')?>" />
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>