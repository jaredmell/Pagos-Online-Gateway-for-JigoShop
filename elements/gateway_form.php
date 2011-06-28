<form action="<?php print $action ?>" method="post" id="pagosonline_payment_form">
	<?php foreach ($fields as $id => $value): ?>
	<input type="hidden" name="<?php print $id; ?>" value="<?php print $value; ?>" />
	<?php endforeach; ?>
  <input type="submit" class="button-alt" id="submit_pagosonline_payment_form" value="<?php print __('Pay via Pagos Online', 'pagosonline'); ?>" />
  <a class="button cancel" href="<?php print $cancel_order_url; ?>"><?php print __('Cancel order &amp; restore cart', 'jigoshop'); ?></a>

	<script type="text/javascript">
		jQuery(function(){
		  jQuery("body").block({
		    message: "<img src=\"<?php print jigoshop::plugin_url() . '/assets/images/ajax-loader.gif'; ?>\" alt=\"<?php print __('Redirecting...', 'pagosonline'); ?>\" /><br/><?php print __('Thank you for your order. We are now redirecting you to Pagos Online to make payment.', 'pagosonline'); ?>",
        overlayCSS: {
				  background: "#fff",
					opacity: 0.6
				},
				css: {
				  padding: 20,
				  textAlign: "center",
				  color: "#555",
				  border: "3px solid #aaa",
					backgroundColor: "#fff",
					cursor: "wait"
			  }
			});
      jQuery("#submit_pagosonline_payment_form").click();
		});
	</script>
</form>
