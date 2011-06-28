/**
 * Disables the Post Code input for Colombia based customers.
 * (Postal codes in Colombia are rarely used)
 */

(function($) {

		var form;

		function handle_country_change(event) {
				var target = $(event.target);
				var panel = /^billing/.test(target.attr('id')) ? 'billing' : 'shipping';
				var input = $('input#' + panel + '-postcode', form);
				if (target.val() == "CO") {
						input.val(input.val() ? input.val() : '0000');
				}
		}

		function disable_post_codes() {
				form = $('form.checkout');
				if (!form.length) {
						return;
				}

				var billing_country = $('select#billing-country', form);
				var shipping_country = $('select#shipping-country', form);
				$.each([billing_country, shipping_country], function(i) {
						this.change(handle_country_change)
								.trigger('change');
				});
		}

		$(document).ready(disable_post_codes);

})(jQuery);

