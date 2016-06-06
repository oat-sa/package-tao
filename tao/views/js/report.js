define(['jquery', 'context', 'i18n'], function($, context, __){
	'use strict';
	
	var reportModule = {
		
		fold: function() {
			var $content = $('.report > .feedback-nesting-0 > div');
			var $top = $('.report > .feedback-nesting-0');
			
			if ($content.css('display') === 'none') {
				$content.css('display', 'block');
				$top.css('background-color', 'transparent');
				$top.css('border-color', 'transparent');
				
				$('#fold > span.check-txt').text(__('Hide detailed report'));
			}
			else {
				$content.css('display', 'none');
				if ($top.hasClass('feedback-success')) {
					$top.css('border-color', '#3ea76f');
					$top.css('background-color', '#e6f4ed');
				}
				else if ($top.hasClass('feedback-warning')) {
					$top.css('border-color', '#dfbe7b');
					$top.css('background-color', '#fbf6ee');
				}
				else if ($top.hasClass('feedback-error')) {
					$top.css('border-color', '#c74155');
					$top.css('background-color', '#f8e7e9');
				}
				else {
					// info
					$top.css('border-color', '#3e7da7');
					$top.css('background-color', '#e6eef4');
				}
				
				$('#fold > span.check-txt').text(__('Show detailed report'));
			}
		}
	}

	return reportModule;
});