/**
 * This class is used to render the generis facet filter widget in horizontal
 * box mode

 * @see GenerisFacetFilterClass
 *
 * @require jquery >= 1.3.2 [http://jquery.com/]
 *
 * @author Alfonsi Cédric, <taosupport@tudor.lu>
 * @author Jehan Bihin (class)
 */


define(['jquery', 'lodash', 'class'], function($, _, Class) {
	var GenerisFacetFilterHboxAdapter = Class.extend({
		init: function() {
			//
		},
		/**
		 * Render the header
		 */
		header: function() {
			var html = '<div>';
			return html;
		},
		/**
		 * Render the footer
		 */
		footer: function() {
			var html = '</div>';
			return html;
		},
		/**
		 * Render the content
		 */
		content: function(id, label) {
			var html = '<div class="ui-widget ui-facet-filter-node ui-helper-horizontal"> \
				<div class="ui-state-default ui-widget-header ui-corner-top container-title" > \
					' + label + ' \
				</div> \
				<div class="ui-widget-content container-content ui-corner-bottom"> \
					<div id="list-' + id + '"></div> \
				</div> \
				<!--<div class="ui-state-default ui-corner-bottom" style="text-align:center; padding:4px;"> \
				</div>--> \
			</div>';
			return html;
		},
		/**
		 * Render
		 */
		render: function(selector, filterNodes) {
			var output = '';

			output += this.header();
			for(var i in filterNodes){
				output += this.content(filterNodes[i].id, filterNodes[i].label);
			}
			output += this.footer();

			$(selector).append(output);
			var width = 100/ _.keys(filterNodes).length -1;
			$(selector).find('.ui-facet-filter-node').css('width', width+'%');
		}
	});

	return GenerisFacetFilterHboxAdapter;
});