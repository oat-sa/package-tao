<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<title><?=__('Configured LTI Link');?></title>

    <link rel="stylesheet" href="<?= ROOT_URL ?>tao/views/css/tao-main-style.css">	
</head>

<body>

<div class="main-container" id="delivery-main-container">
	<div class="ui-widget-header ui-corner-top ui-state-default">
		<?=__('Deliveries %s', get_data('class')->getLabel())?>
	</div>
	<div class="ui-widget-content ui-corner-bottom tao-scope">
        <div class="grid-row">
            <div class="col-12">
                <button class="btn-info" type="button"><?=__('Publish New')?></button>
                <button class="btn-info" type="button"><?= tao_helpers_Icon::iconImport().__('Import')?></button>
            </div>
        </div>
        <div class="grid-row">
            <div class="col-12">
                <table class="matrix">
                    <thead>
                    <tr>
                        <?php foreach (get_data('properties') as $property) :?>
                            <th><?=$property->getLabel()?></th>
                        <?php endforeach;?>
    						<th colspan="3"><?=__("Actions")?></th>
    					</tr>
    				</thead>
                    <tbody>
                        <?php foreach (get_data('deliveries') as $delivery) :?>
                            <tr>
                            <?php foreach ($delivery as $propValues) :?>
                                <td><?= array_pop($propValues)?></td>
                            <?php endforeach;?>
                                <td style="border: none; text-align: center">
                                    <?= tao_helpers_Icon::iconEdit(array('title' => __('Edit delivery'), 'style' => "cursor: pointer;")); ?>
                                </td><td style="border: none; text-align: center">
                                    <?= tao_helpers_Icon::iconExport(array(
                                        'title' => __('Download delivery'),
                                        'data-uri' => $assembly['uri'],
                                        'style' => 'cursor: pointer'
                                    )); ?>
                                </td><td style="border: none; text-align: center">
                                    <?= tao_helpers_Icon::iconBin(array(
                                        'title' => __('Delete delivery'),
                                        'class' => 'compilationButton',
                                        'style' => 'cursor: pointer'
                                    )); ?>
                                </td>
                            </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>        
</body>
