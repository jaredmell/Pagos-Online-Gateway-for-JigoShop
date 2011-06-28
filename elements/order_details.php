<ul class="order_details">
  <li class="order">
	  <?php _e('Order:', 'jigoshop'); ?>
		<strong># <?php echo $order->id; ?></strong>
	</li>
  <li class="date">
	  <?php _e('Date:', 'jigoshop'); ?>
		<strong><?php echo date('d.m.Y', strtotime($order->order_date)); ?></strong>
	</li>
	<li class="total">
	  <?php _e('Total:', 'jigoshop'); ?>
		<strong><?php echo jigoshop_price($order->order_total); ?></strong>
	</li>
	<li class="method">
	  <?php _e('Payment method:', 'jigoshop'); ?>
		<strong>
      <?php
        $gateways = jigoshop_payment_gateways::payment_gateways();
				if (isset($gateways[$order->payment_method])) echo $gateways[$order->payment_method]->title;
				else echo $order->payment_method;
		 ?>
    </strong>
	</li>
</ul>
<div class="clear"></div>
