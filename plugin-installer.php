<?php


function pagosonline_jigo_create_pages() {
  global $wpdb;

  $slug = esc_sql(_x('pagosonline_response', 'page_slug', 'jigoshop'));
	$page_found = $wpdb->get_var("SELECT ID FROM " . $wpdb->posts . " WHERE post_name = '$slug' LIMIT 1");

  if(!$page_found) {
    $page_data = array(
      'post_status' => 'publish',
      'post_type' => 'page',
      'post_author' => 1,
      'post_name' => $slug,
      'post_title' => __('Secure payment via Pagos Online', 'pagosonline'),
      'post_content' => '[pagosonline_confirmation]',
      'comment_status' => 'closed'
    );
    $page_id = wp_insert_post($page_data);

    update_option('pagosonline_response_page_id', $page_id);

  } else {
    update_option('pagosonline_response_page_id', $page_found);
  }

}

function pagosonline_jigo_create_options() {
  add_option('jigoshop_pagosonline_enabled', 'yes');
  add_option('jigoshop_pagosonline_title', 'Pagos Online');
  add_option('jigoshop_pagosonline_description', 'Pay via Pagos Online, the leading Colombian payment gateway. You can pay with your credit card or debit cards from Colombian banks.');
  add_option('jigoshop_pagosonline_testmode', 'no');
  add_option('jigoshop_pagosonline_userid', '');
  add_option('jigoshop_pagosonline_salt', '');
  add_option('jigoshop_pagosonline_gatewayurl', '');
  add_option('jigoshop_pagosonline_template', '');
  add_option('jigoshop_pagosonline_psecode', '');
  add_option('jigoshop_pagosonline_enabledpms', '');
  add_option('jigoshop_pagosonline_defaultlang', '');
}

?>