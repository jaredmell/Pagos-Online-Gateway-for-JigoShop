<?php

class pagosonline_jigoshop_pages
{

  private static $instance;

  public static function getInstance() {
    if (self::$instance == NULL) {
      self::$instance = new pagosonline_jigoshop_pages();
    }

    return self::$instance;
  }

  function confirmation_page() {
    if (!isset($_GET['order'])) {
      return '';
    }

    $data = stripslashes_deep($_GET);
    $order_id = intval($_GET['order']);
    if (!pagosonline_jigoshop_gateway::validate_response($data, $order_id)) {
      return '';
    }

    $response_code = intval($data['codigo_respuesta_pol']);
    $transaction_code = intval($data['estado_pol']);
    $state = pagosonline_jigoshop_gateway::get_order_state_for_response_code($response_code);

    $order =& new jigoshop_order($order_id);
    if ($state == 'completed') {
      jigoshop_cart::empty_cart();
    }

    $responses = pagosonline_jigoshop_settings::$responses;
    $transaction_statuses = pagosonline_jigoshop_settings::$transaction_statuses;

    $log = array();
    $log[] = sprintf(__('Your transaction state: %s (#%d)', 'pagosonline'), $responses[$response_code], $response_code);
    if (!empty($data['ref_pol'])) {
      $log[] = sprintf(__('Pagos Online Transaction Reference: %d', 'pagosonline'), $data['ref_pol']);
    }
    if (!empty($data['codigo_autorizacion'])) {
      $log[] = sprintf(__('Auth Code: %s', 'pagosonline'), $data['codigo_autorizacion']);
    }
    if (!empty($data['cus']) && intval($data['cus'])) {
      $log[] = sprintf(__('Bank Payment Reference: %d', 'pagosonline'), intval($data['cus']));
    }

    $note = implode('<br />', $log);
    $order->add_order_note($note, 0);

    return pagosonline_jigoshop::render('confirmation_page.php', compact('order', 'state'));
  }
}


?>