<link rel="stylesheet" type="text/css" href="<?= ROOT_URL ?>taoResults/views/css/simpleReport.css" />
<div class="ui-helper-reset" id="section_to_print" style="height:100%;" >
	<div id="form-title" class="ui-widget-header ui-corner-top ui-state-default">
		<?=get_data('reportTitle')?>
	</div>
	<div class="report ui-widget-content ui-corner-right">
		    <table><!--TODO CSS the table-->
			<tr>
			    <td>
				<span id="printButton" class="ui-state-default ui-corner-all">
					<a href="javascript:window.print()" ><?=__('Print Report')?></a>
				</span>
				<br />
				<br />
				<!--<?=__('Report generated from the following subset of results : ')?><strong><?=get_data('selectedFilter')?></strong><br/>-->
				<?=__('Generated on: ')?><strong><?=get_data('date')?></strong><br /><br />
				<?=__('Related Deliveries:')?><br />
				<ul>
				<?foreach (get_data('deliveries') as $delivery) :?>
				<li>	<?=$delivery?>
				 <? endforeach ?>
				</ul>
				<br /><?=__('Data collection statistics :')?>
				<ul>
				<li><?=__('# Collected Results')?>: <strong><?=get_data('nbExecutions')?></strong><br /><em>*The number of Tests delivery being executed and collected so far</em>
				<li><?=__('# Distinct variable types')?>: <strong><?=get_data('numberVariables')?></strong><br /><em>*The number of different type of single score variables collected in this delivery definition</em>
				<li><?=__('# Collected Score Variables')?>: <strong><?=get_data('#')?></strong><br /><em>*The number of Score variables collected so far</em>
				<li><?=__('# Distinct Test Takers')?>: : <strong><?=get_data('numberOfDistinctTestTaker')?></strong><br />

				<!--<li><li><?=__('Total Standard Deviation')?>: <strong><?=get_data('std')?></strong><br /><em></em>-->
				<!--<li>Remaining Tokens: <strong><?=get_data('tokensLeft')?></strong><br /><em>*The number of remaining Tests delivery executions (according to the number of attempts granted)</em>-->
				</ul>
				<br /><?=__('Scores and response rates statistics')?>
				<table class="minimal">
					<tr><td><?=__('VariableName')?></td><td><?=__('Average')?></td><td>#</td></tr></strong>
				    <?foreach (get_data('listOfVariables') as $variable) :?>

				    <tr><td><?=$variable["label"]?></td><td><?=round($variable["infos"]["avg"],2)?></td><td><?=$variable["infos"]["#"]?></td></tr>

				    <? endforeach ?>
				     <tr><td><b>Total Average Score</b></td><td><b><?=get_data('average')?></b></td><td></tr>
				</table>


			    </td>
			    <td rowspan="2">
			        <img src="<? echo get_data('variablesAvgComparison');?>" />
				<img src="<? echo get_data('variablesFreqComparison');?>" />
			    </td>
			</tr>
			<tr>
				<td><i>Data extracted in <? echo get_data('dataExtractionTime').__(" seconds");?></i><br/>
				    <i>Report built in <? echo get_data('reportBuildTime').__(" seconds");?></i>
				</td>

			</tr>
		    </table>
		<!-- not very relevant yet without measurement boudaries<div style="border:1px;">
			<img src="<?=get_data('deliveryBarChart');?>"/>
		</div>!-->
		<!-- requries upper version of jpgraph<div style="border:1px;">
			<img src="<?=get_data('compareVariablesPlot');?>"/>
		</div>-->
	</div>
    <!--
	<div id="form-title" class="ui-widget-header ui-corner-top ui-state-default">
		<?=__('Observed performances per distinc score variable types ')?>
	</div>
    -->
	<div class="ui-widget-content ui-corner-right">

		<?foreach (get_data('listOfVariables') as $variable) :?>
		<div id="form-title" class="ui-widget-header ui-corner-top ui-state-default">
			<?=__('Results distribution')?> : <? echo $variable["label"];?>
		</div>


		<ul>
				<ul>
				<li><?=__('Collected Results')?>: <strong><?=$variable["infos"]["#"]?></strong><br />
				<li><?=__('Score average')?>: <strong><? echo round($variable["infos"]["avg"],2); ?></strong>
				</ul>


			</ul>
			<img src="<? echo $variable["urlFrequencies"];?>"/>
			<img src="<? echo $variable["urlScores"];?>"/>





		<? endforeach ?>

	<span id="printButton" class="ui-state-default ui-corner-all">
					<a href="javascript:window.print()" ><?=__('Print Report')?></a>
				</span>
	</div>
</div>
<?include(TAO_TPL_PATH . 'footer.tpl')?>