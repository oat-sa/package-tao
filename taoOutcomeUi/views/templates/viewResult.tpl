<?php
use oat\tao\helpers\Template;
?>
<link rel="stylesheet" type="text/css" href="<?= ROOT_URL ?>taoOutcomeUi/views/css/result.css" />

<header class="section-header flex-container-full">
    <h2><?=__('View result')?> - <?= _dh(get_data('deliveryResultLabel')) ?></h2>
</header>
<div class="main-container flex-container-full">

    <div id="view-result">
        <div id="resultsViewTools">
            <select class="result-filter">
                <option  value="all" ><?=__('All collected variables')?></option>
                <option  value="firstSubmitted" ><?=__('First submitted variables only')?></option>
                <option  value="lastSubmitted" ><?=__('Last submitted variables only')?></option>
            </select>
            <button class="btn-info small result-filter-btn"><?=__('Filter');?></button>
        </div>
        <div id="resultsHeader">
            <div class="tile testtaker">
                <strong>
                    <span class="icon-test-taker"></span>
                    <?=__('Test Taker')?>
                </strong>
                <table class="mini">
                    <tr><td class="field"><?=__('Login:')?></td><td class="fieldValue"><?= _dh(get_data('userLogin'))?></td></tr>
                    <tr><td class="field"><?=__('Label:')?></td><td class="fieldValue"><?= _dh(get_data('userLabel'))?></td></tr>
                    <tr><td class="field"><?=__('Last Name:')?></td><td class="fieldValue"><?= _dh(get_data('userLastName'))?></td></tr>
                    <tr><td class="field"><?=__('First Name:')?></td><td class="fieldValue"><?= _dh(get_data('userFirstName'))?></td></tr>
                    <tr><td class="field"><?=__('Email:')?></td><td class="fieldValue userMail"><?= _dh(get_data('userEmail'))?></td></tr>
                </table>
            </div>
        </div>
        <div id="resultsBox">
            <table class="matrix">
                <thead>
                <tr >
                    <th class="headerRow" colspan="4">
                        <span class="itemName">
                            <?=__('Test Variables')?> (<?=count(get_data("deliveryVariables"))?>)
                        </span>
                    </th>
                </tr>
                </thead>
                <?php foreach (get_data("deliveryVariables") as $testVariable){
                $baseType = $testVariable->getBaseType();
                $cardinality = $testVariable->getCardinality();
                ?>
                <tbody>
                <tr>
                    <td><?=$testVariable->getIdentifier()?></td>
                    <td><?=$testVariable->getValue()?></td>
                    <td> 
                        <?php 
                        echo $cardinality;
                        ?>
                    </td>
                    <td> 
                        <?php 
                        echo $baseType;
                        ?>
                    </td>
                </tr>
                </tbody>
                <?php
                }
                ?>
            </table>
            <?php  foreach (get_data('variables') as $itemUri => $item){
           ?>
           
            <table class="matrix">
                <thead>
                    <tr >
                        <th colspan="5" class="bold">
                            <b>
                                <?= _dh($item['label']) ?>
                                (<?= _dh($item['itemModel']) ?>)
                            </b>
                        </th>
                        <th>
                            <a href="#" data-uri="<?=$itemUri?>" class="btn-info small preview" target="preview">
                                <span class="icon-preview"></span>
                                    <?=__('Preview')?>
                            </a>
                            
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($item['sortedVars'][CLASS_RESPONSE_VARIABLE])) {?>
                    <tr>
                        <th colspan="6" class="italic">
                            <i><?=__('Responses')?> (<?=count($item['sortedVars'][CLASS_RESPONSE_VARIABLE]) ?>)</i>
                        </th>
                    </tr>
                <?php
		foreach ($item['sortedVars'][CLASS_RESPONSE_VARIABLE] as $variableIdentifier  => $observations){
		    $rowspan = 'rowspan="'.count($observations).'"';
		    foreach ($observations as $key=>$observation) {
                    $variable = $observation["var"];
        	?>
		<tr>
		<?php if ($key === key($observations)) {?>
		     <td <?=$rowspan?> class="variableIdentifierField"><?=$variableIdentifier?></td>
		<?php }?>
		<td class="dataResult" colspan="2">
            <?php
            if ($variable->getBaseType() === "file" && $variable->getCandidateResponse() !== '') {
                    echo '<button class="download btn-info small" value="'.$observation["uri"].'"><span class="icon-download"></span> '.__('Download').'</button>';
            }
            else{
            ?>
		    <?php
                        $rdfValue = $variable->getValue();
                        if (is_array($rdfValue)) {
                            echo "<OL>";
                            foreach ($rdfValue as $value) {
                                echo "<LI>";
                                    echo tao_helpers_Display::htmlEscape(nl2br($value));
                                echo "</LI>";
                            }
                            echo "</OL>";
                        } elseif (is_string($rdfValue)) {
                            echo tao_helpers_Display::htmlEscape(nl2br($rdfValue));
                        } else {
                            echo tao_helpers_Display::htmlEscape($rdfValue);
                        }
                    }
                    ?>

                <span class="    
                      <?php
                      switch ($observation['isCorrect']){
                          case "correct":{ echo "icon-result-ok";break;}
                          case "incorrect":{ echo "icon-result-nok"; break;}
                          default: { echo "icon-not-evaluated";break;}
                          }
                          ?>
                          rgt"></span>
                          </td>
                          <td class="cardinalityField">
                              <?php 
                              echo $variable->getCardinality();
                              ?>
                          </td>
                          <td class="basetypeField">
                              <?php 
                              echo $variable->getBaseType();
                              ?>
                          </td>

                          <td class="epoch"><?=tao_helpers_Date::displayeDate(tao_helpers_Date::getTimeStamp($variable->getEpoch()), tao_helpers_Date::FORMAT_VERBOSE)?></td>
                          </tr>
                          <?php
                          }
                          }
                          ?>
                          <?php } ?>
                          <?php if (isset($item['sortedVars'][CLASS_OUTCOME_VARIABLE])) {?>
                <tr>
                    <th colspan="6" class="italic">
                        <i><?=__('Grades')?>  (<?=count($item['sortedVars'][CLASS_OUTCOME_VARIABLE]) ?>)</i>
                    </th>
                </tr>
                <?php
		foreach ($item['sortedVars'][CLASS_OUTCOME_VARIABLE] as $variableIdentifier  => $observations){
		   $rowspan = 'rowspan="'.count($observations).'"';
		    foreach ($observations as $key=>$observation) {
                    $variable = $observation["var"];
        	?>
		<tr>
		<?php if ($key === key($observations)) {?>
		    <td <?=$rowspan?> class="variableIdentifierField"><?=$variableIdentifier?></td>
		<?php }?>
            <td colspan="2" class="dataResult">
                <?= tao_helpers_Display::htmlEscape($variable->getValue())?>
            </td>
            <td class="cardinalityField">
              <?= $variable->getCardinality(); ?>
            </td>
            <td class="basetypeField">
              <?= $variable->getBaseType(); ?>
            </td>
            <td class="epoch">
              <?=tao_helpers_Date::displayeDate(tao_helpers_Date::getTimeStamp($variable->getEpoch()), tao_helpers_Date::FORMAT_VERBOSE)?>
            </td>
            </tr>
            <?php
            }
            }
            ?>
            
            <?php } ?>
            </tbody>
                </table>
                <br />
                <?php } ?>
            </div>
        </div>
    </div>
<div id="form-container" >

    <?php if(get_data('errorMessage')):?>
    <fieldset class='ui-state-error'>
        <legend><strong><?=__('Error')?></strong></legend>
        <?=get_data('errorMessage')?>
    </fieldset>
    <?php endif?>

</div>

<script type="text/javascript">
    requirejs.config({
        config: {
            'taoOutcomeUi/controller/viewResult': {
                uri: '<?=get_data("uri")?>',
                classUri: '<?=get_data("classUri")?>',
                filter: '<?=get_data("filter")?>',
            }
        }
    });
</script>

<?php
Template::inc('footer.tpl', 'tao');
?>
