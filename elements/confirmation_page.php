<?php $view_order_link = add_query_arg('order', $order->id, get_permalink(get_option('jigoshop_view_order_page_id'))); ?>

<?php if ($state == 'cancelled'): ?>
  <h4><?php print __('Your order has been cancelled.', 'pagosonline'); ?></h4>
  <p>
    <?php print __('Why so? We\'re eager to help you in completing your purchase. Don\'t hesitate to contact us regarding assistance or complaints.'); ?> 
    <?php if ($contact = get_option('pagosonline_contact_page', FALSE)): ?>
     <a href="<?php print get_permalink($contact); ?>"><?php print __('Go to contact page', 'pagosonline'); ?></a>
    <?php endif; ?>
  </p>

<?php elseif ($state == 'on-hold' or $state == 'processing'): ?>
  <h4><?php print __('We\'re processing your payment!', 'pagosonline'); ?></h4>
  <p>
    <?php print __('Your payment is being processed. We will send you a confirmation once it arrives.', 'pagosonline'); ?> <a href="<?php print $view_order_link; ?><?php print __('View order', 'pagosonline'); ?></a>
  </p>

<?php elseif ($state == 'completed'): ?>
  <h4><?php print __('Thank you for your order!', 'pagosonline'); ?></h4>
  <p>
    <?php print __('Your purchase is complete. We\'re now preparing the goods for shipment. A confirmation message will provide you with further instructions.', 'pagosonline'); ?> <a href="<?php print $view_order_link; ?>"><?php print __('View order', 'pagosonline'); ?></a>
  </p>
  <?php print pagosonline_jigoshop::render('order_details.php', compact('order')); ?>

<?php elseif ($state == 'pending'): ?>
  <h4><?php print __('Your order is pending for payment.', 'pagosonline'); ?></h4>
  <p>
    <?php print __('We were unable to process the payment information. Please try again or cancel your order', 'pagosonline'); ?>. <a href="<?php print $order->get_checkout_payment_url(); ?><?php print __('Go to checkout', 'pagosonline'); ?></a> or  <a href="<?php print $order->get_cancel_order_url(); ?>"><?php print __('cancel order', 'pagosonline'); ?></a>
  </p>
<?php endif; ?>


