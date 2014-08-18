		
		<div id="footer">
			<?if(get_data('Upload RDF File')):?>
				<div id="section-lg">
					<?=__('Data language')?>: <strong><?=__(get_data('user_lang'))?></strong> 
				</div>
			<?endif?>
			TAO<sup>&reg;</sup> - <?=date('Y')?> - A joint initiative of CRP Henri Tudor and the University of Luxembourg
		</div>
	</body>
</html>